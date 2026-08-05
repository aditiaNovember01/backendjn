<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class KampusSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. AGAMA ─────────────────────────────────────────────────────
        DB::table('agama')->insertOrIgnore([
            ['agamaid' => 1, 'agamanama' => 'Islam'],
        ]);

        // ── 2. JALUR ─────────────────────────────────────────────────────
        DB::table('jalur')->insertOrIgnore([
            ['jalurid' => 1, 'jalurnama' => 'Reguler'],
        ]);

        // ── 3. KEL (kelas biaya) ──────────────────────────────────────────
        DB::table('kel')->insertOrIgnore([
            ['kelid' => 'R', 'kelnama' => 'Reguler'],
            ['kelid' => 'E', 'kelnama' => 'Reguler Malam'],
            ['kelid' => 'K', 'kelnama' => 'KIP Kuliah'],
        ]);

        // ── 4. KELOMPOK mata kuliah ───────────────────────────────────────
        DB::table('kelompok')->insertOrIgnore([
            ['kelid' => 'MKB', 'kelnama' => 'Mata Kuliah Bidang Studi'],
        ]);

        // ── 5. FAKULTAS ───────────────────────────────────────────────────
        DB::table('fakultas')->insertOrIgnore([
            [
                'fakid'   => 1,
                'faknama' => 'Fakultas Teknologi Informasi',
                'fakpim'  => 'Dr. Hendra, M.Kom',
                'fakwapim'=> 'Ir. Santi, M.T',
            ],
        ]);

        // ── 6. STAT (status mahasiswa) ────────────────────────────────────
        DB::table('stat')->insertOrIgnore([
            ['statid' => 1, 'statnama' => 'Aktif'],
            ['statid' => 2, 'statnama' => 'Non-Aktif'],
            ['statid' => 3, 'statnama' => 'Cuti'],
        ]);

        // ── 7. SUMBERBIAYAKUL ─────────────────────────────────────────────
        DB::table('sumberbiayakul')->insertOrIgnore([
            ['sumberid' => 1, 'sumbernama' => 'Mandiri'],
            ['sumberid' => 2, 'sumbernama' => 'KIP Kuliah'],
        ]);

        // ── 8. TAHUNKUR ───────────────────────────────────────────────────
        DB::table('tahunkur')->insertOrIgnore([
            ['tahun' => 2022],
        ]);

        // ── 9. PRODI ──────────────────────────────────────────────────────
        DB::table('prodi')->insertOrIgnore([
            [
                'prodiid'         => 1,
                'prodinama'       => 'Sistem Informasi',
                'prodinamaasing'  => 'Information Systems',
                'prodifakid'      => 1,
                'proditanggalsk'  => '2010-01-01',
                'prodinosk'       => '001/SK/2010',
                'prodijpid'       => 1,
                'prodipejabat'    => 'Mike Febri Mayang Sari, M.Kom',
                'prodikodeps'     => 'SI',
                'prodikodejenjang'=> 'S',
                'prodiptid'       => 1,
                'prodinbkode'     => 1,
            ],
        ]);

        // ── 10. DOSEN ─────────────────────────────────────────────────────
        DB::table('dosen')->insertOrIgnore([
            [
                'dosenid'           => 'DSN001',
                'dosennama'         => 'Mike Febri Mayang Sari',
                'dosenalamat'       => 'Padang',
                'dosentelp'         => '081234567890',
                'dosengelardepan'   => null,
                'dosengelarbelakang'=> 'M.Kom',
                'dosenjpid'         => 1,
                'dosenprodiid'      => 1,
                'dosennidn'         => '1001018801',
                'dosenstatus'       => 1,
            ],
        ]);

        // ── 11. MAHASISWA ─────────────────────────────────────────────────
        DB::table('mahasiswa')->insertOrIgnore([
            [
                'mhsnobp'        => '2210050',
                'mhsnama'        => 'ADITIA NOVIRMAN',
                'mhsalamat'      => 'KOTO PULAI',
                'mhsangkatan'    => 2022,
                'mhsprodiid'     => 1,
                'mhsagamaid'     => 1,
                'mhsjalurid'     => 1,
                'mhsstatid'      => 1,
                'mhstgllhr'      => '1 November 2002',
                'mhstmplhr'      => 'Koto Pulai',
                'mhsjkl'         => 'L',
                'mhsortu'        => 'WAZARIATI ZARWAN',
                'mhsibu'         => 'YURNIDA',
                'mhstelp'        => '089517647957',
                'mhstahunkur'    => 2022,
                'mhskel'         => 'R',
                'mhsasalsekolah' => 'SMAN 1 Koto Pulai',
                'mhssemidmasuk'  => 20221,
                'mhsnik'         => '1301070110020003',
                'mhsnisn'        => '0',
                'mhsumberbiayaid'=> 1,
                'mhsemail'       => 'aditia@jayanusa.ac.id',
                'mhstgllahir'    => '2002-11-01',
                'mhskelurahan'   => 'Koto Pulai',
                'mhskecamatan'   => 'Lubuk Kilangan',
            ],
        ]);

        // ── 12. MATAKULIAH ────────────────────────────────────────────────
        DB::table('matakuliah')->insertOrIgnore([
            [
                'mtkid'          => 'MKB107226',
                'mtknama'        => 'PRAKTEK KERJA LAPANGAN',
                'mtkasing'       => 'Field Work Practice',
                'mtksks'         => 2,
                'mtkdesc'        => 'Praktek di industri',
                'mtkkelid'       => 'MKB',
                'mtkuserinput'   => 'admin',
                'mtktglinput'    => now(),
                'mtkuserubah'    => null,
                'mtktglubah'     => null,
            ],
        ]);

        // ── 13. KURIKULUM ─────────────────────────────────────────────────
        DB::table('kurikulum')->insertOrIgnore([
            [
                'kurmtkid'           => 'MKB107226',
                'kurprodiid'         => 1,
                'kurtahun'           => 2022,
                'kursem'             => 7,              // semester 7
                'kurmtkidprasyarat'  => null,
                'kurmtkidprasyarat2' => null,
                'kurmtkidprasyarat3' => null,
            ],
        ]);

        $kurid = DB::table('kurikulum')
            ->where('kurmtkid', 'MKB107226')
            ->where('kurprodiid', 1)
            ->value('kurid');

        // ── 14. SEM ───────────────────────────────────────────────────────
        // Semester-semester historis + semester aktif
        DB::table('sem')->insertOrIgnore([
            [
                'semid'              => 20221,
                'semnama'            => 'Ganjil 2022/2023',
                'semmulai'           => '2022-09-01 00:00:00',
                'semselesai'         => '2023-01-31 00:00:00',
                'semtglkrsmulai'     => '2022-08-15 00:00:00',
                'semtglkrsselesai'   => '2022-09-05 23:59:59',
                'semtglnilaimulai'   => '2023-01-01 00:00:00',
                'semtglnilaiselesai' => '2023-01-31 23:59:59',
                'semsksbaru'         => 20,
                'semsksbss'          => 24,
                'semangkatanbaru'    => 2022,
                'semlalu'            => null,
                'semaktif'           => 0,
                'semtglkomplain'     => null,
            ],
            [
                'semid'              => 20222,
                'semnama'            => 'Genap 2022/2023',
                'semmulai'           => '2023-02-01 00:00:00',
                'semselesai'         => '2023-07-31 00:00:00',
                'semtglkrsmulai'     => '2023-01-20 00:00:00',
                'semtglkrsselesai'   => '2023-02-10 23:59:59',
                'semtglnilaimulai'   => '2023-06-01 00:00:00',
                'semtglnilaiselesai' => '2023-07-31 23:59:59',
                'semsksbaru'         => 20,
                'semsksbss'          => 24,
                'semangkatanbaru'    => 2022,
                'semlalu'            => 20221,
                'semaktif'           => 0,
                'semtglkomplain'     => null,
            ],
            [
                'semid'              => 20231,
                'semnama'            => 'Ganjil 2023/2024',
                'semmulai'           => '2023-09-01 00:00:00',
                'semselesai'         => '2024-01-31 00:00:00',
                'semtglkrsmulai'     => '2023-08-15 00:00:00',
                'semtglkrsselesai'   => '2023-09-05 23:59:59',
                'semtglnilaimulai'   => '2024-01-01 00:00:00',
                'semtglnilaiselesai' => '2024-01-31 23:59:59',
                'semsksbaru'         => 20,
                'semsksbss'          => 24,
                'semangkatanbaru'    => 2023,
                'semlalu'            => 20222,
                'semaktif'           => 0,
                'semtglkomplain'     => null,
            ],
            [
                'semid'              => 20232,
                'semnama'            => 'Genap 2023/2024',
                'semmulai'           => '2024-02-01 00:00:00',
                'semselesai'         => '2024-07-31 00:00:00',
                'semtglkrsmulai'     => '2024-01-20 00:00:00',
                'semtglkrsselesai'   => '2024-02-10 23:59:59',
                'semtglnilaimulai'   => '2024-06-01 00:00:00',
                'semtglnilaiselesai' => '2024-07-31 23:59:59',
                'semsksbaru'         => 20,
                'semsksbss'          => 24,
                'semangkatanbaru'    => 2023,
                'semlalu'            => 20231,
                'semaktif'           => 0,
                'semtglkomplain'     => null,
            ],
            [
                'semid'              => 20241,
                'semnama'            => 'Ganjil 2024/2025',
                'semmulai'           => '2024-09-01 00:00:00',
                'semselesai'         => '2025-01-31 00:00:00',
                'semtglkrsmulai'     => '2024-08-15 00:00:00',
                'semtglkrsselesai'   => '2024-09-05 23:59:59',
                'semtglnilaimulai'   => '2025-01-01 00:00:00',
                'semtglnilaiselesai' => '2025-01-31 23:59:59',
                'semsksbaru'         => 20,
                'semsksbss'          => 24,
                'semangkatanbaru'    => 2024,
                'semlalu'            => 20232,
                'semaktif'           => 0,
                'semtglkomplain'     => null,
            ],
            [
                'semid'              => 20242,
                'semnama'            => 'Genap 2024/2025',
                'semmulai'           => '2025-02-01 00:00:00',
                'semselesai'         => '2025-07-31 00:00:00',
                'semtglkrsmulai'     => '2025-01-20 00:00:00',
                'semtglkrsselesai'   => '2025-02-10 23:59:59',
                'semtglnilaimulai'   => '2025-06-01 00:00:00',
                'semtglnilaiselesai' => '2025-07-31 23:59:59',
                'semsksbaru'         => 20,
                'semsksbss'          => 24,
                'semangkatanbaru'    => 2024,
                'semlalu'            => 20241,
                'semaktif'           => 0,
                'semtglkomplain'     => null,
            ],
            [
                'semid'              => 20251,
                'semnama'            => 'Ganjil 2025/2026',
                'semmulai'           => '2025-09-01 00:00:00',
                'semselesai'         => '2026-01-31 00:00:00',
                'semtglkrsmulai'     => '2025-08-15 00:00:00',
                'semtglkrsselesai'   => '2025-09-05 23:59:59',
                'semtglnilaimulai'   => '2026-01-01 00:00:00',
                'semtglnilaiselesai' => '2026-01-31 23:59:59',
                'semsksbaru'         => 20,
                'semsksbss'          => 24,
                'semangkatanbaru'    => 2025,
                'semlalu'            => 20242,
                'semaktif'           => 0,
                'semtglkomplain'     => null,
            ],
            [
                'semid'              => 20252,
                'semnama'            => 'Genap 2025/2026',
                'semmulai'           => '2026-02-01 00:00:00',
                'semselesai'         => '2026-07-31 00:00:00',
                'semtglkrsmulai'     => '2026-01-20 00:00:00',
                'semtglkrsselesai'   => '2026-02-10 23:59:59',
                'semtglnilaimulai'   => '2026-06-01 00:00:00',
                'semtglnilaiselesai' => '2026-07-31 23:59:59',
                'semsksbaru'         => 20,
                'semsksbss'          => 24,
                'semangkatanbaru'    => 2025,
                'semlalu'            => 20251,
                'semaktif'           => 1,
                'semtglkomplain'     => null,
            ],
        ]);

        // ── 15. RUANG ─────────────────────────────────────────────────────
        DB::table('ruang')->insertOrIgnore([
            ['ruid' => 1, 'runama' => 'Lokal 1'],
        ]);

        // ── 16. KELAS ─────────────────────────────────────────────────────
        DB::table('kelas')->insertOrIgnore([
            [
                'kelasid'           => 1,
                'kelaskode'         => '20252-1-005',
                'kelaskurid'        => $kurid,
                'kelasprodiid'      => 1,
                'kelassem'          => 20252,
                'kelasmax'          => 30,
                'kelastanggalinput' => now(),
                'kelasuserinput'    => 'admin',
                'kelastanggalubah'  => null,
                'kelasuserubah'     => null,
                'kelasnobpmin'      => '0',
                'kelasnobpmax'      => '9999999',
                'kelaskel'          => 'R',
                'kelaslabel'        => 'A',
                'kelasnilai'        => null,
                'kelasket'          => null,
            ],
        ]);

        // ── 17. KELASJADWAL ───────────────────────────────────────────────
        DB::table('kelasjadwal')->insertOrIgnore([
            [
                'jadwalid'           => 1,
                'jadwalkelasid'      => 1,
                'jadwalruangid'      => 1,
                'jadwaljamidawal'    => 1,
                'jadwalhari'         => 'Minggu',
                'jadwaldosenid'      => 'DSN001',
                'jadwaltanggalinput' => now(),
                'jadwaluserinput'    => 'admin',
                'jadwaljamidakhir'   => 3,
                'jadwalqsuts'        => null,
                'jadwalrespondenuts' => null,
                'jadwalqsuas'        => null,
                'jadwalrespondenuas' => null,
            ],
        ]);

        // ── 18. KELASANGKATAN ─────────────────────────────────────────────
        DB::table('kelasangkatan')->insertOrIgnore([
            ['kelasangkelasid' => 1, 'kelasangangkatan' => 2022],
        ]);

        // ── 19. SPP (tagihan semester) ────────────────────────────────────
        // Tagihan untuk seluruh semester mahasiswa Aditia Novirman
        DB::table('spp')->insertOrIgnore([
            ['sppmhsnobp' => '2210050', 'sppsem' => 20221, 'spptagihan' => 3500000],
            ['sppmhsnobp' => '2210050', 'sppsem' => 20222, 'spptagihan' => 3500000],
            ['sppmhsnobp' => '2210050', 'sppsem' => 20231, 'spptagihan' => 3500000],
            ['sppmhsnobp' => '2210050', 'sppsem' => 20232, 'spptagihan' => 3500000],
            ['sppmhsnobp' => '2210050', 'sppsem' => 20241, 'spptagihan' => 3500000],
            ['sppmhsnobp' => '2210050', 'sppsem' => 20242, 'spptagihan' => 3500000],
            ['sppmhsnobp' => '2210050', 'sppsem' => 20251, 'spptagihan' => 3500000],
            // Semester aktif 20252 — BELUM LUNAS (untuk uji coba fitur bayar)
            ['sppmhsnobp' => '2210050', 'sppsem' => 20252, 'spptagihan' => 3500000],
        ]);

        // ── 20. REGISTRASI (semester lama = sudah lunas) ──────────────────
        // Semester 20252 TIDAK ada registrasi → belum lunas → bisa test bayar
        $semesterLunas = [
            ['sem' => 20221, 'tgl' => '2022-09-10', 'nobukti' => 'JN/2022/09/000001'],
            ['sem' => 20222, 'tgl' => '2023-02-08', 'nobukti' => 'JN/2023/02/000001'],
            ['sem' => 20231, 'tgl' => '2023-09-12', 'nobukti' => 'JN/2023/09/000001'],
            ['sem' => 20232, 'tgl' => '2024-02-07', 'nobukti' => 'JN/2024/02/000001'],
            ['sem' => 20241, 'tgl' => '2024-09-09', 'nobukti' => 'JN/2024/09/000001'],
            ['sem' => 20242, 'tgl' => '2025-02-06', 'nobukti' => 'JN/2025/02/000001'],
        ];
        foreach ($semesterLunas as $sl) {
            DB::table('registrasi')->insertOrIgnore([
                [
                    'regmhsnobp'      => '2210050',
                    'regsem'          => $sl['sem'],
                    'regjumlahbayar'  => 3500000,
                    'regtanggalbayar' => $sl['tgl'],
                    'reguserinput'    => 'admin',
                    'regnobukti'      => $sl['nobukti'],
                ],
            ]);
        }

        // Ambil regid semester pertama untuk KRS
        $regid = DB::table('registrasi')
            ->where('regmhsnobp', '2210050')
            ->where('regsem', 20221)
            ->value('regid');

        // ── 21. KRS ───────────────────────────────────────────────────────
        if ($regid) {
            DB::table('krs')->insertOrIgnore([
                [
                    'krsregid'           => $regid,
                    'krsmhsnobp'         => '2210050',
                    'krskelasid'         => 1,
                    'krsnilai'           => 'A',
                    'krssem'             => 20221,
                    'krsjmlabsen'        => 0,
                    'krstanggalambil'    => now(),
                    'krsuserambil'       => '2210050',
                    'krstanggalhapus'    => null,
                    'krsuserhapus'       => null,
                    'krshapus'           => 0,
                    'krstanggalnilai'    => null,
                    'krsusernilai'       => null,
                    'krsbobot'           => 4,
                    'krsinputnilaimetode'=> 'portal',
                    'krsapproved'        => 1,
                    'krstglapproved'     => now(),
                    'krskomplain'        => 0,
                ],
            ]);
        }

        // ── 22. SETTINGBIAYA ──────────────────────────────────────────────
        DB::table('settingbiaya')->insertOrIgnore([
            [
                'prodi'        => 1,
                'angkatan'     => 2022,
                'kelas'        => 'R',
                'biaya'        => 3500000,
                'biaya1'       => 1750000,
                'biaya2'       => 1750000,
                'pembangunan1' => null,
                'pembangunan2' => null,
                'pembangunan3' => null,
                'pembangunan4' => null,
                'orientasi'    => null,
            ],
        ]);

        // ── 23. SETTING (konfigurasi aktif) ───────────────────────────────
        // Tabel setting tidak punya PK, pakai truncate+insert sekali
        if (DB::table('setting')->count() === 0) {
            DB::table('setting')->insert([
                'semaktif'         => 20252,
                'semkrsmulai'      => '2026-01-20 00:00:00',
                'semkrsselesai'    => '2026-02-10 23:59:59',
                'semubahkrsmulai'  => '2026-02-11 00:00:00',
                'semubahkrsselesai'=> '2026-02-15 23:59:59',
                'semnilaimulai'    => '2026-06-01 00:00:00',
                'semnilaiselesai'  => '2026-07-31 23:59:59',
                'portalisaktif'    => 1,
            ]);
        }

        // ── 24. BIAYAKOMPRE ───────────────────────────────────────────────
        DB::table('biayakompre')->insertOrIgnore([
            [
                'periode' => '2025/2026',
                'biaya1'  => 500000,
                'biaya2'  => 300000,
                'biaya3'  => null,
                'biaya4'  => null,
                'aktif'   => 1,
            ],
        ]);

        // ── 25. PERIODEBAYAR ──────────────────────────────────────────────
        DB::table('periodebayar')->insertOrIgnore([
            ['persemid' => 20252, 'perstt' => 'A'],
        ]);

        // ── 26. LABEL ─────────────────────────────────────────────────────
        DB::table('label')->insert([['labelnama' => 'A']]);

        // ── 27. MTKLAB ────────────────────────────────────────────────────
        DB::table('mtklab')->insertOrIgnore([
            ['mtklabteori' => 'MKB107226', 'mtklabprak' => null],
        ]);

        // ── 28. PEMBAYARAN_KOMPRE ─────────────────────────────────────────
        DB::table('pembayaran_kompre')->insertOrIgnore([
            [
                'pembkompremhsnobp'  => '2210050',
                'pembkompretgl'      => '2026-06-01',
                'pembkomprenobukti'  => 'KMP/2026/06/0001',
                'pembkomprejumlah'   => 500000,
                'pembkompreuserinput'=> 'admin',
                'pembkompreke'       => 1,
                'pembkompreperiode'  => '2025/2026',
            ],
        ]);

        // ── 29. PEMBAYARAN_WISUDA ─────────────────────────────────────────
        DB::table('pembayaran_wisuda')->insertOrIgnore([
            [
                'wispembmhsnobp'   => '2210050',
                'wispembtgl'       => '2026-06-15 10:00:00',
                'wispembnobukti'   => 'WIS/2026/06/0001',
                'wispembjumlah'    => 1200000,
                'wispembuserinput' => 'admin',
            ],
        ]);

        // ── 30. PENGUMUMAN ────────────────────────────────────────────────
        DB::table('pengumuman')->insertOrIgnore([
            [
                'judul'       => 'Pengisian KRS Semester Genap 2025/2026',
                'isi'         => '<p>Mahasiswa diwajibkan mengisi KRS mulai tanggal <strong>20 Januari 2026</strong> s/d <strong>10 Februari 2026</strong>. Hubungi Dosen PA Anda untuk persetujuan.</p>',
                'tgl_publish' => '2026-01-15',
                'aktif'       => 1,
                'user_id'     => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);

        // ── 31. BUKTI_PEMBAYARAN ──────────────────────────────────────────
        // Bukti bayar untuk semester lama (dikonfirmasi)
        // Semester aktif 20252 TIDAK ada bukti → bisa upload untuk uji coba
        $semBukti = [20221, 20222, 20231, 20232, 20241, 20242];
        foreach ($semBukti as $idx => $semBuktiId) {
            DB::table('bukti_pembayaran')->insertOrIgnore([
                [
                    'mhsnobp'        => '2210050',
                    'sppsem'         => $semBuktiId,
                    'jumlah_bayar'   => 3500000,
                    'file_path'      => null,
                    'file_compressed'=> null,
                    'status'         => 'dikonfirmasi',
                    'catatan'        => null,
                    'confirmed_by'   => 1,
                    'confirmed_at'   => $semesterLunas[$idx]['tgl'] . ' 09:00:00',
                    'created_at'     => $semesterLunas[$idx]['tgl'] . ' 08:00:00',
                    'updated_at'     => $semesterLunas[$idx]['tgl'] . ' 09:00:00',
                ],
            ]);
        }

        // ── 32. USER mahasiswa (untuk login API) ──────────────────────────
        \App\Models\User::firstOrCreate(
            ['mhsnobp' => '2210050'],
            [
                'name'     => 'ADITIA NOVIRMAN',
                'email'    => 'aditia@jayanusa.ac.id',
                'password' => Hash::make('01112002'),   // default: tgllahir ddmmyyyy
                'role'     => 'mahasiswa',
                'mhsnobp'  => '2210050',
            ]
        );

        $this->command->info('');
        $this->command->info('✅ KampusSeeder selesai!');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('Mahasiswa : ADITIA NOVIRMAN (NoBP: 2210050)');
        $this->command->info('Login API : nobp=2210050 | password=01112002');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}
