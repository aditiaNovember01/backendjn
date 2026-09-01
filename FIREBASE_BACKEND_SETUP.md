# Firebase FCM — Setup Backend Laravel (jayanusabackend)
> Tujuan: Ketika dosen input pengumuman, notifikasi otomatis dikirim ke semua device mahasiswa
> Dibuat: 10 Agustus 2026

---

## Alur Sistem

```
Dosen POST /api/v1/pengumuman
        ↓
PengumumanController@store()
        ↓
Event: PengumumanCreated
        ↓
Listener: SendFcmNotification
        ↓
Firebase FCM → semua device mahasiswa
```

---

## Langkah 1 — Install Firebase Admin SDK

Jalankan di folder `jayanusabackend`:

```bash
composer require kreait/laravel-firebase
```

---

## Langkah 2 — Konfigurasi Firebase

### 2a. Download Service Account Key
1. Buka [console.firebase.google.com](https://console.firebase.google.com) → project **HaloJayanusa**
2. Settings (gear icon) → **Project settings** → tab **Service accounts**
3. Klik **"Generate new private key"** → download file JSON
4. Rename file menjadi `firebase-service-account.json`
5. Taruh di: `jayanusabackend/storage/app/firebase-service-account.json`

### 2b. Tambah ke `.env`

```env
FIREBASE_CREDENTIALS=storage/app/firebase-service-account.json
FIREBASE_PROJECT_ID=halojayanusa
```

### 2c. Publish config `kreait/laravel-firebase`

```bash
php artisan vendor:publish --provider="Kreait\Laravel\Firebase\ServiceProvider"
```

---

## Langkah 3 — Buat Tabel FCM Tokens

### Buat migration:

```bash
php artisan make:migration create_fcm_tokens_table
```

### Isi migration (`database/migrations/xxxx_create_fcm_tokens_table.php`):

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fcm_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('nobp', 10)->index();
            $table->text('token');
            $table->string('platform', 10)->default('android'); // android / ios
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            // Satu device satu token
            $table->unique(['nobp', 'token']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fcm_tokens');
    }
};
```

```bash
php artisan migrate
```

---

## Langkah 4 — Buat Model FcmToken

**File: `app/Models/FcmToken.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FcmToken extends Model
{
    protected $fillable = ['nobp', 'token', 'platform', 'last_used_at'];

    protected $casts = [
        'last_used_at' => 'datetime',
    ];
}
```

---

## Langkah 5 — Endpoint Register FCM Token

### Route (`routes/api.php`) — tambahkan di dalam group auth:

```php
// FCM Token
Route::post('/fcm/register', [FcmTokenController::class, 'register']);
Route::delete('/fcm/unregister', [FcmTokenController::class, 'unregister']);
```

### Controller:

```bash
php artisan make:controller Api/FcmTokenController
```

**File: `app/Http/Controllers/Api/FcmTokenController.php`**

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use Illuminate\Http\Request;

class FcmTokenController extends Controller
{
    /**
     * Register FCM token device mahasiswa
     * POST /api/v1/fcm/register
     */
    public function register(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'nobp'  => 'required|string',
        ]);

        // Upsert: update jika sudah ada, insert jika belum
        FcmToken::updateOrCreate(
            [
                'nobp'  => $request->nobp,
                'token' => $request->token,
            ],
            [
                'platform'     => $request->input('platform', 'android'),
                'last_used_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'FCM token berhasil didaftarkan.',
        ]);
    }

    /**
     * Hapus FCM token saat logout
     * DELETE /api/v1/fcm/unregister
     */
    public function unregister(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        FcmToken::where('token', $request->token)->delete();

        return response()->json([
            'success' => true,
            'message' => 'FCM token dihapus.',
        ]);
    }
}
```

---

## Langkah 6 — Service FCM Notification

**File: `app/Services/FcmService.php`**

```php
<?php

namespace App\Services;

use App\Models\FcmToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    /**
     * Kirim notifikasi ke semua mahasiswa
     */
    public function sendToAllMahasiswa(string $title, string $body, array $data = []): void
    {
        $tokens = FcmToken::pluck('token')->toArray();

        if (empty($tokens)) {
            Log::info('[FCM] Tidak ada token terdaftar.');
            return;
        }

        $this->sendMulticast($tokens, $title, $body, $data);
    }

    /**
     * Kirim notifikasi ke mahasiswa tertentu berdasarkan nobp
     */
    public function sendToMahasiswa(string $nobp, string $title, string $body, array $data = []): void
    {
        $tokens = FcmToken::where('nobp', $nobp)->pluck('token')->toArray();

        if (empty($tokens)) return;

        $this->sendMulticast($tokens, $title, $body, $data);
    }

    /**
     * Kirim ke banyak token sekaligus (max 500 per request FCM)
     */
    private function sendMulticast(array $tokens, string $title, string $body, array $data = []): void
    {
        // Ambil access token dari service account
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            Log::error('[FCM] Gagal mendapatkan access token.');
            return;
        }

        $projectId = config('firebase.project_id', env('FIREBASE_PROJECT_ID', 'halojayanusa'));

        // Kirim per batch 500 token
        $chunks = array_chunk($tokens, 500);
        foreach ($chunks as $chunk) {
            foreach ($chunk as $token) {
                $this->sendSingle($accessToken, $projectId, $token, $title, $body, $data);
            }
        }

        Log::info("[FCM] Notifikasi terkirim ke " . count($tokens) . " device. Judul: {$title}");
    }

    private function sendSingle(string $accessToken, string $projectId, string $token, string $title, string $body, array $data): void
    {
        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'sound'        => 'default',
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    ],
                ],
                'data' => array_merge(['click_action' => 'FLUTTER_NOTIFICATION_CLICK'], $data),
            ],
        ];

        $response = Http::withToken($accessToken)
            ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", $payload);

        if (!$response->successful()) {
            $errorBody = $response->json();
            // Hapus token yang tidak valid
            if (isset($errorBody['error']['details'][0]['errorCode']) &&
                in_array($errorBody['error']['details'][0]['errorCode'], ['REGISTRATION_TOKEN_NOT_REGISTERED', 'INVALID_ARGUMENT'])) {
                FcmToken::where('token', $token)->delete();
                Log::info("[FCM] Token tidak valid dihapus: {$token}");
            }
        }
    }

    /**
     * Ambil OAuth2 access token dari service account JSON
     */
    private function getAccessToken(): ?string
    {
        try {
            $credentialsPath = base_path(env('FIREBASE_CREDENTIALS', 'storage/app/firebase-service-account.json'));

            if (!file_exists($credentialsPath)) {
                Log::error("[FCM] Service account file tidak ditemukan: {$credentialsPath}");
                return null;
            }

            $credentials = json_decode(file_get_contents($credentialsPath), true);

            // Buat JWT untuk OAuth2
            $now  = time();
            $exp  = $now + 3600;
            $scope = 'https://www.googleapis.com/auth/firebase.messaging';

            $header  = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $payload = base64_encode(json_encode([
                'iss'   => $credentials['client_email'],
                'scope' => $scope,
                'aud'   => 'https://oauth2.googleapis.com/token',
                'exp'   => $exp,
                'iat'   => $now,
            ]));

            $signingInput = "{$header}.{$payload}";
            openssl_sign($signingInput, $signature, $credentials['private_key'], 'sha256WithRSAEncryption');
            $jwt = "{$signingInput}." . base64_encode($signature);

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]);

            return $response->json('access_token');
        } catch (\Exception $e) {
            Log::error('[FCM] getAccessToken error: ' . $e->getMessage());
            return null;
        }
    }
}
```

---

## Langkah 7 — Integrasi ke PengumumanController

Cari file controller pengumuman (biasanya `app/Http/Controllers/Api/PengumumanController.php`), tambahkan di method `store()`:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use App\Services\FcmService;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    protected FcmService $fcm;

    public function __construct(FcmService $fcm)
    {
        $this->fcm = $fcm;
    }

    /**
     * POST /api/v1/pengumuman
     * Dosen buat pengumuman baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi'   => 'required|string',
        ]);

        // Simpan pengumuman ke database
        $pengumuman = Pengumuman::create([
            'judul'      => $request->judul,
            'isi'        => $request->isi,
            'penulis'    => $request->user()?->nama ?? 'Admin',
            'nidn'       => $request->user()?->nidn ?? null,
            'dosen_id'   => $request->user()?->id ?? null,
            'tgl_publish'=> now()->toDateString(),
            'aktif'      => true,
        ]);

        // ── Kirim notifikasi FCM ke semua mahasiswa ──────────────────
        $this->fcm->sendToAllMahasiswa(
            title: '📢 Pengumuman Baru',
            body:  $request->judul,
            data:  [
                'type'           => 'pengumuman',
                'pengumuman_id'  => (string) $pengumuman->id,
                'judul'          => $request->judul,
            ]
        );
        // ─────────────────────────────────────────────────────────────

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil ditambahkan.',
            'data'    => $pengumuman,
        ], 201);
    }
}
```

---

## Langkah 8 — Register FcmService di ServiceProvider (opsional)

Tambahkan di `app/Providers/AppServiceProvider.php`:

```php
use App\Services\FcmService;

public function register(): void
{
    $this->app->singleton(FcmService::class);
}
```

---

## Langkah 9 — Jalankan di Server

```bash
# Di folder jayanusabackend di server
composer require kreait/laravel-firebase
php artisan migrate
php artisan config:cache
php artisan route:cache
```

---

## Ringkasan File yang Perlu Dibuat/Diubah

| File | Aksi |
|------|------|
| `database/migrations/xxxx_create_fcm_tokens_table.php` | Buat baru |
| `app/Models/FcmToken.php` | Buat baru |
| `app/Http/Controllers/Api/FcmTokenController.php` | Buat baru |
| `app/Services/FcmService.php` | Buat baru |
| `app/Http/Controllers/Api/PengumumanController.php` | Edit — tambah FCM di `store()` |
| `routes/api.php` | Edit — tambah route FCM |
| `app/Providers/AppServiceProvider.php` | Edit — register singleton |
| `storage/app/firebase-service-account.json` | Upload dari Firebase Console |
| `.env` | Tambah `FIREBASE_CREDENTIALS` dan `FIREBASE_PROJECT_ID` |

---

## Catatan Penting

- **`firebase-service-account.json`** jangan di-commit ke Git — tambahkan ke `.gitignore`
- Token FCM otomatis dihapus jika tidak valid (device uninstall app)
- Notifikasi dikirim secara **background job** idealnya menggunakan Laravel Queue agar tidak block response API — tambahkan `dispatch(new SendFcmJob(...))` jika perlu
