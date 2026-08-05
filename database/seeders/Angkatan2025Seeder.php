<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Angkatan2025Seeder extends Seeder
{
    public function run(): void
    {
        $nobp = '2510001';

        // Cleanup data lama jika ada
        DB::table('krs')->where('krsmhsnobp', $nobp)->delete();
        DB::table('bukti_pembayaran')->where('mhsnobp', $nobp)->delete();
        DB::table('registrasi')->where('regmhsnobp', $nobp)->delete();
        DB::table('spp')->where('sppmhsnobp', $nobp)->delete();

        // ── 0. TAHUNKUR ──────────────────────────────────────────────────
        DB::table('tahunkur')->insertOrIgnore([
            ['tahun' => 2025],
        ]);

        // ── 1. MAHASISWA ─────────────────────────────────────────────────
        DB::table('mahasiswa')->insertOrIgnore([
            [
                'mhsnobp'        => $nobp,
                'mhsnama'        => 'REHAN MAULANA',
                'mhsalamat'      => 'PADANG',
                'mhsangkatan'    => 2025,
                'mhsprodiid'     => 1, // Sistem Informasi
                'mhsagamaid'     => 1, // Islam
                'mhsjalurid'     => 1, // Reguler
                'mhsstatid'      => 1, // Aktif
                'mhstgllhr'      => '1 Januari 2005',
                'mhstmplhr'      => 'Padang',
                'mhsjkl'         => 'L',
                'mhsortu'        => 'SYAMSUL',
                'mhsibu'         => 'MARLINA',
                'mhstelp'        => '081234567890',
                'mhstahunkur'    => 2025,
                'mhskel'         => 'R',
                'mhsasalsekolah' => 'SMAN 1 Padang',
                'mhssemidmasuk'  => 20251,
                'mhsnik'         => '1371010101050001',
                'mhsnisn'        => '0051234567',
                'mhsumberbiayaid'=> 1,
                'mhsemail'       => 'rehan@jayanusa.ac.id',
                'mhstgllahir'    => '2005-01-01',
                'mhskelurahan'   => 'Padang Pasir',
                'mhskecamatan'   => 'Padang Barat',
            ],
        ]);

        // ── 2. USER AUTH ──────────────────────────────────────────────────
        User::firstOrCreate(
            ['mhsnobp' => $nobp],
            [
                'name'     => 'REHAN MAULANA',
                'email'    => 'rehan@jayanusa.ac.id',
                'password' => Hash::make('01012005'), // ddmmyyyy (01-01-2005)
                'role'     => 'mahasiswa',
                'mhsnobp'  => $nobp,
            ]
        );

        // ── 3. MATAKULIAH SEMESTER 1 & 2 ──────────────────────────────────
        $matakuliahList = [
            // Semester 1 (Ganjil 2025/2026 - 20251)
            ['mtkid' => 'SI1013', 'mtknama' => 'ALGORITMA & PEMROGRAMAN', 'sks' => 3, 'sem' => 1],
            ['mtkid' => 'SI1023', 'mtknama' => 'KONSEP SISTEM INFORMASI', 'sks' => 3, 'sem' => 1],
            ['mtkid' => 'SI1033', 'mtknama' => 'PENGANTAR TEKNOLOGI INFORMASI', 'sks' => 3, 'sem' => 1],
            ['mtkid' => 'SI1043', 'mtknama' => 'MATEMATIKA DISKRIT', 'sks' => 3, 'sem' => 1],
            ['mtkid' => 'UNI1012', 'mtknama' => 'BAHASA INDONESIA', 'sks' => 2, 'sem' => 1],
            ['mtkid' => 'UNI1022', 'mtknama' => 'PANCASILA', 'sks' => 2, 'sem' => 1],

            // Semester 2 (Genap 2025/2026 - 20252)
            ['mtkid' => 'SI2013', 'mtknama' => 'PEMROGRAMAN WEB I', 'sks' => 3, 'sem' => 2],
            ['mtkid' => 'SI2023', 'mtknama' => 'BASIS DATA I', 'sks' => 3, 'sem' => 2],
            ['mtkid' => 'SI2033', 'mtknama' => 'STRUKTUR DATA', 'sks' => 3, 'sem' => 2],
            ['mtkid' => 'SI2042', 'mtknama' => 'BAHASA INGGRIS AKADEMIK', 'sks' => 2, 'sem' => 2],
            ['mtkid' => 'SI2053', 'mtknama' => 'STATISTIK & PROBABILITAS', 'sks' => 3, 'sem' => 2],
            ['mtkid' => 'UNI2012', 'mtknama' => 'KEWARGANEGARAAN', 'sks' => 2, 'sem' => 2],
        ];

        foreach ($matakuliahList as $mk) {
            DB::table('matakuliah')->insertOrIgnore([
                'mtkid'        => $mk['mtkid'],
                'mtknama'      => $mk['mtknama'],
                'mtkasing'     => $mk['mtknama'],
                'mtksks'       => $mk['sks'],
                'mtkdesc'      => 'Mata Kuliah Angkatan 2025',
                'mtkkelid'     => 'MKB',
                'mtkuserinput' => 'admin',
                'mtktglinput'  => now(),
            ]);

            DB::table('kurikulum')->insertOrIgnore([
                'kurmtkid'   => $mk['mtkid'],
                'kurprodiid' => 1,
                'kurtahun'   => 2025,
                'kursem'     => $mk['sem'],
            ]);
        }

        // ── 4. KELAS & JADWAL ─────────────────────────────────────────────
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $kelasMap = [];

        foreach ($matakuliahList as $idx => $mk) {
            $semId = $mk['sem'] === 1 ? 20251 : 20252;
            $kurid = DB::table('kurikulum')
                ->where('kurmtkid', $mk['mtkid'])
                ->where('kurprodiid', 1)
                ->where('kurtahun', 2025)
                ->value('kurid');

            $kelaskode = "{$semId}-1-" . str_pad($idx + 10, 3, '0', STR_PAD_LEFT);

            $existingKelas = DB::table('kelas')->where('kelaskode', $kelaskode)->value('kelasid');
            if ($existingKelas) {
                $kelasid = $existingKelas;
            } else {
                $kelasid = DB::table('kelas')->insertGetId([
                    'kelaskode'         => $kelaskode,
                    'kelaskurid'        => $kurid,
                    'kelasprodiid'      => 1,
                    'kelassem'          => $semId,
                    'kelasmax'          => 40,
                    'kelastanggalinput' => now(),
                    'kelasuserinput'    => 'admin',
                    'kelaskel'          => 'R',
                    'kelaslabel'        => 'A',
                ]);
            }

            $kelasMap[$mk['mtkid']] = [
                'kelasid' => $kelasid,
                'semid'   => $semId,
                'sks'     => $mk['sks'],
            ];

            DB::table('kelasjadwal')->insertOrIgnore([
                'jadwalkelasid'   => $kelasid,
                'jadwalruangid'   => 1,
                'jadwaljamidawal' => 1,
                'jadwaljamidakhir'=> $mk['sks'],
                'jadwalhari'      => $hariList[$idx % count($hariList)],
                'jadwaldosenid'   => 'DSN001',
                'jadwaltanggalinput' => now(),
                'jadwaluserinput' => 'admin',
            ]);

            DB::table('kelasangkatan')->insertOrIgnore([
                'kelasangkelasid' => $kelasid,
                'kelasangangkatan'=> 2025,
            ]);
        }

        // ── 5. SPP TAGIHAN (ANGKATAN 2025 = RP 4.100.000) ─────────────────
        DB::table('spp')->insertOrIgnore([
            ['sppmhsnobp' => $nobp, 'sppsem' => 20251, 'spptagihan' => 4100000],
            ['sppmhsnobp' => $nobp, 'sppsem' => 20252, 'spptagihan' => 4100000],
        ]);

        // ── 6. SEMESTER 1 (20251) = LUNAS + KRS TERISI + NILAI KHS ─────────
        $regIdS1 = DB::table('registrasi')->insertGetId([
            'regmhsnobp'      => $nobp,
            'regsem'          => 20251,
            'regjumlahbayar'  => 4100000,
            'regtanggalbayar' => '2025-09-01',
            'reguserinput'    => 'admin',
            'regnobukti'      => 'JN/2025/09/00251',
        ]);

        DB::table('bukti_pembayaran')->insertOrIgnore([
            'mhsnobp'        => $nobp,
            'sppsem'         => 20251,
            'jumlah_bayar'   => 4100000,
            'tipe_bayar'     => 'penuh',
            'status'         => 'dikonfirmasi',
            'confirmed_by'   => 1,
            'confirmed_at'   => '2025-09-01 09:00:00',
            'created_at'     => '2025-09-01 08:00:00',
            'updated_at'     => '2025-09-01 09:00:00',
        ]);

        // Matakuliah S1: Isi KRS & Nilai KHS
        $nilaiNilai = [
            ['SI1013', 'A',  4.0],
            ['SI1023', 'A',  4.0],
            ['SI1033', 'B+', 3.5],
            ['SI1043', 'B+', 3.5],
            ['UNI1012', 'A',  4.0],
            ['UNI1022', 'A',  4.0],
        ];

        foreach ($nilaiNilai as $n) {
            $mkid  = $n[0];
            $nilai = $n[1];
            $bobot = $n[2];
            $kId   = $kelasMap[$mkid]['kelasid'];

            DB::table('krs')->insertOrIgnore([
                'krsregid'        => $regIdS1,
                'krsmhsnobp'      => $nobp,
                'krskelasid'      => $kId,
                'krssem'          => 20251,
                'krshapus'        => 0,
                'krsapproved'     => 1,
                'krsnilai'        => $nilai,
                'krsbobot'        => $bobot,
                'krstanggalambil' => '2025-09-02 10:00:00',
                'krsuserambil'    => $nobp,
            ]);
        }

        // ── 7. SEMESTER 2 (20252) = BELUM BAYAR ────────────────────────────
        // (Registrasi dan Bukti Pembayaran TIDAK di-insert agar berstatus Belum Bayar)

        $this->command->info('✅ Angkatan2025Seeder berhasil dijalankan!');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('Mahasiswa : REHAN MAULANA (NoBP: 2510001)');
        $this->command->info('Login API : nobp=2510001 | password=01012005');
        $this->command->info('SPP 20251 : LUNAS (Rp 4.100.000) | KRS S1 Terisi + Nilai');
        $this->command->info('SPP 20252 : BELUM BAYAR (KRS S2 Dikunci sampai bayar SPP)');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}
