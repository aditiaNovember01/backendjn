<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FcmTokenController extends Controller
{
    /**
     * Daftarkan FCM token device.
     * Dipanggil saat app dibuka / login berhasil.
     *
     * POST /api/v1/fcm/register
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'token'    => ['required', 'string', 'max:500'],
            'platform' => ['nullable', 'in:android,ios'],
        ]);

        $nobp = $request->user()->mhsnobp
            ?? $request->user()->dosenid
            ?? (string) $request->user()->id;

        // Upsert: update jika token sudah ada, insert jika belum
        FcmToken::updateOrCreate(
            [
                'nobp'  => $nobp,
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
     * Hapus FCM token saat logout.
     * Agar device tidak menerima notifikasi setelah logout.
     *
     * DELETE /api/v1/fcm/unregister
     */
    public function unregister(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
        ]);

        FcmToken::where('token', $request->token)->delete();

        return response()->json([
            'success' => true,
            'message' => 'FCM token berhasil dihapus.',
        ]);
    }
}
