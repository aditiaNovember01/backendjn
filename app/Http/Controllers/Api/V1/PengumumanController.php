<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    /**
     * Daftar pengumuman aktif, terbaru dulu.
     */
    public function index(Request $request): JsonResponse
    {
        $pengumuman = Pengumuman::published()
            ->orderByDesc('tgl_publish')
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => $pengumuman->items(),
            'meta'    => [
                'current_page' => $pengumuman->currentPage(),
                'per_page'     => $pengumuman->perPage(),
                'total'        => $pengumuman->total(),
                'last_page'    => $pengumuman->lastPage(),
            ],
        ]);
    }

    /**
     * Detail satu pengumuman.
     */
    public function show(int $id): JsonResponse
    {
        $pengumuman = Pengumuman::published()->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'          => $pengumuman->id,
                'judul'       => $pengumuman->judul,
                'isi'         => $pengumuman->isi,
                'tgl_publish' => $pengumuman->tgl_publish?->format('d F Y'),
                'created_at'  => $pengumuman->created_at?->format('d F Y H:i'),
            ],
        ]);
    }

    /**
     * Tambah pengumuman baru (Dosen / Admin).
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'isi'   => ['required', 'string'],
        ]);

        $pengumuman = Pengumuman::create([
            'judul'       => $request->judul,
            'isi'         => $request->isi,
            'tgl_publish' => now()->toDateString(),
            'aktif'       => true,
            'user_id'     => $request->user()?->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil dipublikasikan.',
            'data'    => [
                'id'          => $pengumuman->id,
                'judul'       => $pengumuman->judul,
                'isi'         => $pengumuman->isi,
                'tgl_publish' => $pengumuman->tgl_publish?->format('d F Y') ?? now()->format('d F Y'),
            ],
        ], 201);
    }
}
