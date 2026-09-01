<?php

namespace App\Services;

use App\Models\FcmToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Firebase Cloud Messaging Service
 * Menggunakan FCM HTTP v1 API dengan OAuth2 JWT dari Service Account
 */
class FcmService
{
    /** Cache access token agar tidak request ulang setiap kirim */
    private ?string $cachedToken   = null;
    private int     $tokenExpiry   = 0;

    // ── Public API ─────────────────────────────────────────────

    /**
     * Kirim notifikasi ke SEMUA device mahasiswa terdaftar.
     */
    public function sendToAllMahasiswa(string $title, string $body, array $data = []): void
    {
        $tokens = FcmToken::pluck('token')->toArray();

        if (empty($tokens)) {
            Log::info('[FCM] Tidak ada token terdaftar, notifikasi dilewati.');
            return;
        }

        $this->sendBatch($tokens, $title, $body, $data);
    }

    /**
     * Kirim notifikasi ke mahasiswa tertentu berdasarkan NoBP.
     */
    public function sendToMahasiswa(string $nobp, string $title, string $body, array $data = []): void
    {
        $tokens = FcmToken::where('nobp', $nobp)->pluck('token')->toArray();

        if (empty($tokens)) {
            Log::info("[FCM] Tidak ada token untuk nobp={$nobp}");
            return;
        }

        $this->sendBatch($tokens, $title, $body, $data);
    }

    /**
     * Kirim ke daftar NoBP tertentu (misal: satu prodi).
     */
    public function sendToNobpList(array $nobpList, string $title, string $body, array $data = []): void
    {
        $tokens = FcmToken::whereIn('nobp', $nobpList)->pluck('token')->toArray();

        if (empty($tokens)) return;

        $this->sendBatch($tokens, $title, $body, $data);
    }

    // ── Internal ───────────────────────────────────────────────

    private function sendBatch(array $tokens, string $title, string $body, array $data): void
    {
        $accessToken = $this->getAccessToken();

        if (! $accessToken) {
            Log::error('[FCM] Gagal mendapatkan access token. Notifikasi tidak terkirim.');
            return;
        }

        $projectId   = config('firebase.project_id', env('FIREBASE_PROJECT_ID', ''));
        $invalidTokens = [];
        $successCount  = 0;

        // FCM v1 API: kirim satu per satu (tidak ada multicast di v1)
        // Untuk production dengan banyak user, gunakan Laravel Queue
        foreach ($tokens as $token) {
            $result = $this->sendSingle($accessToken, $projectId, $token, $title, $body, $data);

            if ($result === 'invalid') {
                $invalidTokens[] = $token;
            } elseif ($result === 'success') {
                $successCount++;
            }
        }

        // Bersihkan token tidak valid
        if (! empty($invalidTokens)) {
            FcmToken::whereIn('token', $invalidTokens)->delete();
            Log::info('[FCM] Dihapus ' . count($invalidTokens) . ' token tidak valid.');
        }

        Log::info("[FCM] Berhasil: {$successCount}/" . count($tokens) . " | Judul: {$title}");
    }

    /**
     * Kirim ke satu token, return 'success' | 'invalid' | 'error'
     */
    private function sendSingle(
        string $accessToken,
        string $projectId,
        string $token,
        string $title,
        string $body,
        array  $data
    ): string {
        $payload = [
            'message' => [
                'token'        => $token,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                'android' => [
                    'priority'     => 'high',
                    'notification' => [
                        'sound'        => 'default',
                        'channel_id'   => 'halojayanusa_default',
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    ],
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                            'badge' => 1,
                        ],
                    ],
                ],
                // Data payload — diterima oleh React Native / Flutter
                'data' => array_merge(
                    ['click_action' => 'FLUTTER_NOTIFICATION_CLICK'],
                    array_map('strval', $data) // FCM data harus string semua
                ),
            ],
        ];

        try {
            $response = Http::withToken($accessToken)
                ->timeout(10)
                ->post(
                    "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send",
                    $payload
                );

            if ($response->successful()) {
                return 'success';
            }

            $errorCode = $response->json('error.details.0.errorCode') ?? '';
            $status    = $response->json('error.status') ?? '';

            // Token tidak valid / sudah tidak terdaftar
            if (in_array($errorCode, ['REGISTRATION_TOKEN_NOT_REGISTERED', 'INVALID_ARGUMENT'])
                || $status === 'INVALID_ARGUMENT') {
                return 'invalid';
            }

            Log::warning("[FCM] Gagal kirim ke token. Status: {$status} | Code: {$errorCode}");
            return 'error';

        } catch (\Exception $e) {
            Log::error('[FCM] Exception saat sendSingle: ' . $e->getMessage());
            return 'error';
        }
    }

    /**
     * Ambil OAuth2 access token dari Service Account JSON.
     * Di-cache selama 1 jam.
     */
    private function getAccessToken(): ?string
    {
        // Return cache jika belum expired
        if ($this->cachedToken && time() < $this->tokenExpiry) {
            return $this->cachedToken;
        }

        try {
            $credentialsPath = base_path(
                env('FIREBASE_CREDENTIALS', 'storage/app/firebase-service-account.json')
            );

            if (! file_exists($credentialsPath)) {
                Log::error("[FCM] File service account tidak ditemukan: {$credentialsPath}");
                return null;
            }

            $creds = json_decode(file_get_contents($credentialsPath), true);

            if (! isset($creds['client_email'], $creds['private_key'])) {
                Log::error('[FCM] Service account JSON tidak valid.');
                return null;
            }

            $now   = time();
            $exp   = $now + 3600;
            $scope = 'https://www.googleapis.com/auth/firebase.messaging';

            // Buat JWT header.payload
            $header  = rtrim(strtr(base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT'])), '+/', '-_'), '=');
            $payload = rtrim(strtr(base64_encode(json_encode([
                'iss'   => $creds['client_email'],
                'scope' => $scope,
                'aud'   => 'https://oauth2.googleapis.com/token',
                'exp'   => $exp,
                'iat'   => $now,
            ])), '+/', '-_'), '=');

            $signingInput = "{$header}.{$payload}";

            // Sign dengan private key RSA
            openssl_sign($signingInput, $signature, $creds['private_key'], 'sha256WithRSAEncryption');
            $sig = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');
            $jwt = "{$signingInput}.{$sig}";

            // Exchange JWT untuk access token
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]);

            if (! $response->successful()) {
                Log::error('[FCM] Gagal exchange JWT: ' . $response->body());
                return null;
            }

            $this->cachedToken = $response->json('access_token');
            $this->tokenExpiry = $now + 3500; // sedikit kurang dari 1 jam

            return $this->cachedToken;

        } catch (\Exception $e) {
            Log::error('[FCM] getAccessToken error: ' . $e->getMessage());
            return null;
        }
    }
}
