<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function login(Request $request): JsonResponse
    {
 
        $nobpValue = $request->nobp
            ?? $request->nim
            ?? $request->nidn
            ?? $request->noBP
            ?? $request->username
            ?? null;

        // Merge ke field 'nobp' agar validasi konsisten
        if ($nobpValue !== null && ! $request->has('nobp')) {
            $request->merge(['nobp' => $nobpValue]);
        }

        $request->validate([
            'nobp'     => ['required', 'string', 'max:20'],
            'password' => ['required', 'string'],
        ]);

        $identifier = trim($request->nobp);

       
        if (strtoupper($identifier) === 'D001') {
            $identifier = 'DSN001';
        }

       
        $dosen = Dosen::where('dosenid', $identifier)
            ->orWhere('dosennidn', $identifier)
            ->first();
        if ($dosen) {
            // Cari user dosen berdasarkan dosenid utama
            $user = User::where('dosenid', $dosen->dosenid)
                ->where('role', 'dosen')
                ->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'nobp' => ['ID Dosen atau password salah.'],
                ]);
            }

            $user->tokens()->delete();
            $token = $user->createToken('mobile-app')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil.',
                'data'    => [
                    'token'      => $token,
                    'token_type' => 'Bearer',
                    'user'       => [
                        'nobp'   => $dosen->dosenid,
                        'nama'   => $dosen->nama_lengkap,
                        'prodi'  => null,
                        'role'   => 'dosen',
                        'nidn'   => $dosen->dosennidn,
                    ],
                ],
            ]);
        }

        // ── Login MAHASISWA ──────────────────────────────────────────────
        $user = User::where('mhsnobp', $identifier)
            ->where('role', 'mahasiswa')
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'nobp' => ['NoBP atau password salah.'],
            ]);
        }

        // Cek apakah mahasiswa masih aktif
        $mahasiswa = Mahasiswa::with('stat')->find($identifier);
        if (! $mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan.',
            ], 404);
        }

        // Revoke semua token lama (single session)
        $user->tokens()->delete();

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data'    => [
                'token'      => $token,
                'token_type' => 'Bearer',
                'user'       => [
                    'nobp'  => $user->mhsnobp,
                    'nama'  => $mahasiswa->mhsnama,
                    'prodi' => $mahasiswa->prodi?->prodinama,
                    'role'  => 'mahasiswa',
                ],
            ],
        ]);
    }

    /**
     * Logout — revoke token aktif.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    /**
     * Info user yang sedang login.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        // Response dosen
        if ($user->isDosen()) {
            $dosen = Dosen::find($user->dosenid);
            return response()->json([
                'success' => true,
                'data'    => [
                    'nobp'  => $dosen?->dosenid,
                    'email' => $user->email,
                    'nama'  => $dosen?->nama_lengkap,
                    'prodi' => null,
                    'role'  => 'dosen',
                    'nidn'  => $dosen?->dosennidn,
                ],
            ]);
        }

        // Response mahasiswa
        $mahasiswa = Mahasiswa::with(['prodi', 'stat'])->find($user->mhsnobp);

        return response()->json([
            'success' => true,
            'data'    => [
                'nobp'  => $user->mhsnobp,
                'email' => $user->email,
                'nama'  => $mahasiswa?->mhsnama,
                'prodi' => $mahasiswa?->prodi?->prodinama,
                'status'=> $mahasiswa?->stat?->statnama,
                'role'  => 'mahasiswa',
            ],
        ]);
    }
}
