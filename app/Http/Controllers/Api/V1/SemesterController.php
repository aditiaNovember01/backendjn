<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Sem;
use Illuminate\Http\JsonResponse;

class SemesterController extends Controller
{
    public function aktif(): JsonResponse
    {
        $sem = Sem::aktif();

        if (! $sem) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada semester aktif saat ini.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'sem_id'          => $sem->semid,
                'nama'            => $sem->semnama,
                'mulai'           => $sem->semmulai?->format('d F Y'),
                'selesai'         => $sem->semselesai?->format('d F Y'),
                'krs_mulai'       => $sem->semtglkrsmulai?->format('d F Y H:i'),
                'krs_selesai'     => $sem->semtglkrsselesai?->format('d F Y H:i'),
                'krs_open'        => $sem->isKrsOpen(),
            ],
        ]);
    }
}
