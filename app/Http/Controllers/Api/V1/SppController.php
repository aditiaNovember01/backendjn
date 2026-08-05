<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\BuktiPembayaran;
use App\Models\Registrasi;
use App\Models\Setting;
use App\Models\Spp;
use App\Services\ImageCompressionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SppController extends Controller
{
    public function __construct(protected ImageCompressionService $compressor) {}

    /**
     * Semua tagihan SPP mahasiswa.
     */
    public function index(Request $request): JsonResponse
    {
        $nobp = $request->user()->mhsnobp;

        $sppList = Spp::with('semester')
            ->where('sppmhsnobp', $nobp)
            ->orderByDesc('sppsem')
            ->get();

        $data = $sppList->map(fn($spp) => $this->formatSpp($spp, $nobp));

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    /**
     * Tagihan semester aktif.
     */
    public function aktif(Request $request): JsonResponse
    {
        $nobp  = $request->user()->mhsnobp;
        $semId = Setting::semesterAktif();

        $spp = Spp::with('semester')
            ->where('sppmhsnobp', $nobp)
            ->where('sppsem', $semId)
            ->first();

        if (! $spp) {
            return response()->json([
                'success' => false,
                'message' => 'Data tagihan semester aktif tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $this->formatSpp($spp, $nobp),
        ]);
    }

    /**
     * Upload bukti pembayaran.
     * Menerima multipart/form-data: file (image), jumlah_bayar
     */
    public function uploadBukti(Request $request): JsonResponse
    {
        $request->validate([
            'file'         => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'jumlah_bayar' => ['required', 'numeric', 'min:1'],
            'sem_id'       => ['nullable', 'integer'],
            'tipe_bayar'   => ['nullable', 'in:penuh,cicilan1,cicilan2,cicilan3'],
        ]);

        $nobp     = $request->user()->mhsnobp;
        $semId    = $request->input('sem_id', Setting::semesterAktif());
        $tipeBayar = $request->input('tipe_bayar', 'penuh');

        // Cek tagihan SPP exist
        $spp = Spp::where('sppmhsnobp', $nobp)->where('sppsem', $semId)->first();
        if (! $spp) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan SPP tidak ditemukan untuk semester ini.',
            ], 404);
        }

        // Cek sudah lunas (ada registrasi)
        $registrasi = Registrasi::where('regmhsnobp', $nobp)->where('regsem', $semId)->first();
        if ($registrasi) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran semester ini sudah dikonfirmasi (Lunas).',
            ], 422);
        }

        // Ambil semua bukti yang sudah dikonfirmasi untuk semester ini
        $buktiDikonfirmasi = BuktiPembayaran::where('mhsnobp', $nobp)
            ->where('sppsem', $semId)
            ->where('status', 'dikonfirmasi')
            ->get();

        $tipeYangSudahDikonfirmasi = $buktiDikonfirmasi->pluck('tipe_bayar')->toArray();

        // Validasi urutan cicilan
        if ($tipeBayar === 'cicilan2') {
            // cicilan2 hanya boleh jika cicilan1 sudah dikonfirmasi
            if (! in_array('cicilan1', $tipeYangSudahDikonfirmasi)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cicilan 2 hanya dapat dibayarkan setelah Cicilan 1 dikonfirmasi oleh admin.',
                ], 422);
            }
        }

        // Cek apakah sudah ada bukti tipe yang sama yang masih pending
        $pending = BuktiPembayaran::where('mhsnobp', $nobp)
            ->where('sppsem', $semId)
            ->where('tipe_bayar', $tipeBayar)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return response()->json([
                'success' => false,
                'message' => 'Sudah ada bukti pembayaran ' . $tipeBayar . ' yang sedang menunggu konfirmasi admin.',
            ], 422);
        }

        // Proses upload & kompresi
        $paths = $this->compressor->store($request->file('file'), 'bukti-pembayaran');

        BuktiPembayaran::create([
            'mhsnobp'         => $nobp,
            'sppsem'          => $semId,
            'jumlah_bayar'    => $request->jumlah_bayar,
            'tipe_bayar'      => $tipeBayar,
            'file_path'       => $paths['original'],
            'file_compressed' => $paths['compressed'],
            'status'          => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bukti pembayaran berhasil diupload. Menunggu konfirmasi admin.',
        ], 201);
    }

    /**
     * Cek status pembayaran untuk semester tertentu.
     */
    public function statusPembayaran(Request $request, int $semId): JsonResponse
    {
        $nobp = $request->user()->mhsnobp;

        $spp = Spp::with('semester')->where('sppmhsnobp', $nobp)->where('sppsem', $semId)->first();

        $bukti = BuktiPembayaran::with('semester')
            ->where('mhsnobp', $nobp)
            ->where('sppsem', $semId)
            ->latest()
            ->first();

        $registrasi = Registrasi::where('regmhsnobp', $nobp)
            ->where('regsem', $semId)
            ->first();

        $totalDikonfirmasi = BuktiPembayaran::where('mhsnobp', $nobp)
            ->where('sppsem', $semId)
            ->where('status', 'dikonfirmasi')
            ->sum('jumlah_bayar');

        $tagihanTotal = $spp?->spptagihan ?? 0;
        $jumlahBayar  = $registrasi?->regjumlahbayar ?? ($totalDikonfirmasi > 0 ? (int)$totalDikonfirmasi : ($bukti?->jumlah_bayar ?? 0));
        $tanggalBayar = $registrasi?->regtanggalbayar?->format('d F Y')
            ?? ($bukti?->confirmed_at ? $bukti->confirmed_at->format('d F Y') : $bukti?->created_at?->format('d F Y'));
        $noBukti      = $registrasi?->regnobukti
            ?? ($bukti ? ('JN/' . date('Y/m') . '/' . str_pad($bukti->id, 6, '0', STR_PAD_LEFT)) : null);

        return response()->json([
            'success' => true,
            'data'    => [
                'sem_id'             => $semId,
                'semester'           => $spp?->semester?->semnama ?? $bukti?->semester?->semnama ?? '-',
                'status_lunas'       => $registrasi ? 'Lunas' : 'Belum Lunas',
                'tanggal_bayar'      => $tanggalBayar,
                'no_bukti'           => $noBukti,
                'jumlah_bayar'       => $jumlahBayar,
                'tagihan_total'      => $tagihanTotal,
                'total_dikonfirmasi' => (int) $totalDikonfirmasi,
                'sisa_tagihan'       => max(0, $tagihanTotal - $totalDikonfirmasi),
                'upload_terakhir'    => $bukti ? [
                    'id'           => $bukti->id,
                    'status'       => $bukti->status,
                    'jumlah'       => $bukti->jumlah_bayar,
                    'catatan'      => $bukti->catatan,
                    'file_url'     => $bukti->file_url,
                    'uploaded_at'  => $bukti->created_at?->format('d F Y H:i'),
                    'confirmed_at' => $bukti->confirmed_at?->format('d F Y H:i'),
                ] : null,
            ],
        ]);
    }

    // ── Helper ────────────────────────────────────────────────
    private function formatSpp(Spp $spp, string $nobp): array
    {
        $registrasi = Registrasi::where('regmhsnobp', $nobp)
            ->where('regsem', $spp->sppsem)
            ->first();

        // Ambil semua bukti pembayaran yang dikonfirmasi untuk semester ini
        $buktiDikonfirmasi = BuktiPembayaran::where('mhsnobp', $nobp)
            ->where('sppsem', $spp->sppsem)
            ->where('status', 'dikonfirmasi')
            ->get();

        $totalDikonfirmasi = $buktiDikonfirmasi->sum('jumlah_bayar');
        $tipesDikonfirmasi = $buktiDikonfirmasi->pluck('tipe_bayar')->toArray();

        // Hitung status cicilan:
        // - lunas     : ada registrasi (admin sudah confirm semua)
        // - cicilan1  : cicilan1 dikonfirmasi tapi cicilan2 belum
        // - pending   : ada upload pending (menunggu konfirmasi)
        // - belum     : belum ada pembayaran sama sekali
        if ($registrasi) {
            $statusCicilan = 'lunas';
        } elseif (in_array('cicilan1', $tipesDikonfirmasi) || $buktiDikonfirmasi->count() > 0) {
            $statusCicilan = 'cicilan1'; // Cicilan 1 sudah dikonfirmasi, menunggu cicilan 2 (pelunasan)
        } else {
            // Cek apakah ada yang masih pending
            $adaPending = BuktiPembayaran::where('mhsnobp', $nobp)
                ->where('sppsem', $spp->sppsem)
                ->where('status', 'pending')
                ->exists();
            $statusCicilan = $adaPending ? 'pending' : 'belum';
        }

        // Bukti upload terakhir (untuk info di card)
        $buktiTerakhir = BuktiPembayaran::where('mhsnobp', $nobp)
            ->where('sppsem', $spp->sppsem)
            ->latest()
            ->first();

        $jumlahBayar  = $registrasi?->regjumlahbayar ?? ($totalDikonfirmasi > 0 ? (int)$totalDikonfirmasi : null);
        $tanggalBayar = $registrasi?->regtanggalbayar?->format('d F Y')
            ?? ($buktiTerakhir?->confirmed_at ? $buktiTerakhir->confirmed_at->format('d F Y') : null);
        $noBukti      = $registrasi?->regnobukti
            ?? ($buktiTerakhir && $totalDikonfirmasi > 0 ? ('JN/' . date('Y/m') . '/' . str_pad($buktiTerakhir->id, 6, '0', STR_PAD_LEFT)) : null);

        return [
            'spp_id'             => $spp->sppid,
            'sem_id'             => $spp->sppsem,
            'semester'           => $spp->semester?->semnama ?? '-',
            'tagihan'            => $spp->spptagihan,
            'status_lunas'       => $registrasi ? 'Lunas' : 'Belum Lunas',
            'status_cicilan'     => $statusCicilan,  // 'lunas' | 'cicilan1' | 'pending' | 'belum'
            'tanggal_bayar'      => $tanggalBayar,
            'no_bukti'           => $noBukti,
            'jumlah_bayar'       => $jumlahBayar,
            'total_dikonfirmasi' => (int) $totalDikonfirmasi,
            'sisa_tagihan'       => max(0, $spp->spptagihan - $totalDikonfirmasi),
            'status_upload'      => $buktiTerakhir?->status,
            'tipe_upload'        => $buktiTerakhir?->tipe_bayar,
        ];
    }
}
