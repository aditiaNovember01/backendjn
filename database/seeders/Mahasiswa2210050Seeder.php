<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder lengkap untuk mahasiswa ADITIA NOVIRMAN (NoBP: 2210050)
 * Berisi: Mata kuliah sem 1-8, jadwal, kelas, SPP, registrasi, KRS + nilai
 *
 * Kurikulum SI 2022 — 8 Semester (total ~144 SKS)
 */
class Mahasiswa2210050Seeder extends Seeder
{
    // Konstanta mahasiswa
    const NOBP       = '2210050';
    const PRODI_ID   = 1;
    const ANGKATAN   = 2022;
    const TAHUNKUR   = 2022;
    const DOSEN1     = 'DSN001';
    const DOSEN2     = 'DSN002';
    const DOSEN3     = 'DSN003';
    const DOSEN4     = 'DSN004';
    const DOSEN5     = 'DSN005';

    public function run(): void
    {
        $this->command->info('');
        $this->command->info('▶ Seeding data mahasiswa 2210050...');

        $this->seedRuang();
        $this->seedDosen();
        $this->seedMataKuliah();
        $this->seedKurikulum();
        $this->seedSettingBiaya();
        $this->seedSem();
        $this->seedSetting();
        $this->seedMahasiswa();
        $this->seedKelasAndJadwal();
        $this->seedSppAndRegistrasi();
        $this->seedKrsAndNilai();
        $this->seedUser();

        $this->command->info('');
        $this->command->info('✅ Seeder selesai!');
        $this->printSummary();
    }

    // ── Ruang ─────────────────────────────────────────────────────────────
    private function seedRuang(): void
    {
        DB::table('ruang')->insertOrIgnore([
            ['ruid' => 1,  'runama' => 'Lokal 1'],
            ['ruid' => 2,  'runama' => 'Lokal 2'],
            ['ruid' => 3,  'runama' => 'Lokal 3'],
            ['ruid' => 4,  'runama' => 'Lokal 4'],
            ['ruid' => 5,  'runama' => 'Lab Komputer 1'],
            ['ruid' => 6,  'runama' => 'Lab Komputer 2'],
            ['ruid' => 7,  'runama' => 'Lab Jaringan'],
            ['ruid' => 8,  'runama' => 'Lab Multimedia'],
            ['ruid' => 9,  'runama' => 'Aula'],
            ['ruid' => 10, 'runama' => 'Lokal 5'],
        ]);
        $this->command->info('  ✓ Ruang');
    }

    // ── Dosen ─────────────────────────────────────────────────────────────
    private function seedDosen(): void
    {
        DB::table('dosen')->insertOrIgnore([
            ['dosenid' => self::DOSEN1, 'dosennama' => 'Mike Febri Mayang Sari',   'dosenalamat' => 'Padang',    'dosentelp' => '081234567890', 'dosengelardepan' => null,  'dosengelarbelakang' => 'M.Kom',      'dosenjpid' => 1, 'dosenprodiid' => 1, 'dosennidn' => '1001018801', 'dosenstatus' => 1],
            ['dosenid' => self::DOSEN2, 'dosennama' => 'Ahmad Fikri Fajri',        'dosenalamat' => 'Padang',    'dosentelp' => '081234567891', 'dosengelardepan' => null,  'dosengelarbelakang' => 'S.Kom, M.Kom', 'dosenjpid' => 1, 'dosenprodiid' => 1, 'dosennidn' => '1002018802', 'dosenstatus' => 1],
            ['dosenid' => self::DOSEN3, 'dosennama' => 'Darwati',                  'dosenalamat' => 'Padang',    'dosentelp' => '081234567892', 'dosengelardepan' => 'Ir.', 'dosengelarbelakang' => 'MM',         'dosenjpid' => 1, 'dosenprodiid' => 1, 'dosennidn' => '1003018803', 'dosenstatus' => 1],
            ['dosenid' => self::DOSEN4, 'dosennama' => 'Rini Gustina Helmi',       'dosenalamat' => 'Padang',    'dosentelp' => '081234567893', 'dosengelardepan' => null,  'dosengelarbelakang' => 'M.Kom',      'dosenjpid' => 1, 'dosenprodiid' => 1, 'dosennidn' => '1004018804', 'dosenstatus' => 1],
            ['dosenid' => self::DOSEN5, 'dosennama' => 'Rehan Jaya Pratama',       'dosenalamat' => 'Padang',    'dosentelp' => '081234567894', 'dosengelardepan' => null,  'dosengelarbelakang' => 'S.Kom, M.T', 'dosenjpid' => 1, 'dosenprodiid' => 1, 'dosennidn' => '1005018805', 'dosenstatus' => 1],
        ]);
        $this->command->info('  ✓ Dosen (5 dosen)');
    }

    // ── Mata Kuliah — Kurikulum SI 2022 ───────────────────────────────────
    private function seedMataKuliah(): void
    {
        $now = now();
        // Sem 1
        DB::table('matakuliah')->insertOrIgnore([
            ['mtkid' => 'MKB101101', 'mtknama' => 'ALGORITMA DAN PEMROGRAMAN 1',        'mtkasing' => 'Algorithm & Programming 1',     'mtksks' => 3, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB101102', 'mtknama' => 'MATEMATIKA DISKRIT',                 'mtkasing' => 'Discrete Mathematics',          'mtksks' => 3, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB101103', 'mtknama' => 'SISTEM INFORMASI',                   'mtkasing' => 'Information Systems',           'mtksks' => 3, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB101104', 'mtknama' => 'BAHASA INGGRIS TEKNIK',              'mtkasing' => 'Technical English',             'mtksks' => 2, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB101105', 'mtknama' => 'PENGANTAR TEKNOLOGI INFORMASI',      'mtkasing' => 'Introduction to IT',            'mtksks' => 2, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB101106', 'mtknama' => 'KALKULUS 1',                         'mtkasing' => 'Calculus 1',                    'mtksks' => 3, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB101107', 'mtknama' => 'PRAKTIKUM ALGORITMA 1',              'mtkasing' => 'Algorithm Practicum 1',         'mtksks' => 1, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
        ]);
        // Sem 2
        DB::table('matakuliah')->insertOrIgnore([
            ['mtkid' => 'MKB102101', 'mtknama' => 'ALGORITMA DAN PEMROGRAMAN 2',        'mtkasing' => 'Algorithm & Programming 2',     'mtksks' => 3, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB102102', 'mtknama' => 'STRUKTUR DATA',                      'mtkasing' => 'Data Structures',               'mtksks' => 3, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB102103', 'mtknama' => 'BASIS DATA 1',                       'mtkasing' => 'Database 1',                    'mtksks' => 3, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB102104', 'mtknama' => 'JARINGAN KOMPUTER',                  'mtkasing' => 'Computer Networks',             'mtksks' => 3, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB102105', 'mtknama' => 'KALKULUS 2',                         'mtkasing' => 'Calculus 2',                    'mtksks' => 2, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB102106', 'mtknama' => 'PRAKTIKUM ALGORITMA 2',              'mtkasing' => 'Algorithm Practicum 2',         'mtksks' => 1, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB102107', 'mtknama' => 'PRAKTIKUM BASIS DATA 1',             'mtkasing' => 'Database Practicum 1',          'mtksks' => 1, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
        ]);
        // Sem 3
        DB::table('matakuliah')->insertOrIgnore([
            ['mtkid' => 'MKB103101', 'mtknama' => 'PEMROGRAMAN WEB 1',                  'mtkasing' => 'Web Programming 1',             'mtksks' => 3, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB103102', 'mtknama' => 'BASIS DATA 2',                       'mtkasing' => 'Database 2',                    'mtksks' => 3, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB103103', 'mtknama' => 'SISTEM OPERASI',                     'mtkasing' => 'Operating Systems',             'mtksks' => 3, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB103104', 'mtknama' => 'ANALISIS SISTEM INFORMASI',          'mtkasing' => 'IS Analysis',                   'mtksks' => 3, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB103105', 'mtknama' => 'STATISTIKA',                         'mtkasing' => 'Statistics',                    'mtksks' => 2, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB103106', 'mtknama' => 'PRAKTIKUM BASIS DATA 2',             'mtkasing' => 'Database Practicum 2',          'mtksks' => 1, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB103107', 'mtknama' => 'PRAKTIKUM WEB 1',                    'mtkasing' => 'Web Practicum 1',               'mtksks' => 1, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
        ]);
        // Sem 4
        DB::table('matakuliah')->insertOrIgnore([
            ['mtkid' => 'MKB104101', 'mtknama' => 'PEMROGRAMAN WEB 2',                  'mtkasing' => 'Web Programming 2',             'mtksks' => 3, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB104102', 'mtknama' => 'REKAYASA PERANGKAT LUNAK',           'mtkasing' => 'Software Engineering',          'mtksks' => 3, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB104103', 'mtknama' => 'PERANCANGAN SISTEM INFORMASI',       'mtkasing' => 'IS Design',                     'mtksks' => 3, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB104104', 'mtknama' => 'KECERDASAN BUATAN',                  'mtkasing' => 'Artificial Intelligence',       'mtksks' => 3, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB104105', 'mtknama' => 'MANAJEMEN PROYEK TI',                'mtkasing' => 'IT Project Management',         'mtksks' => 2, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB104106', 'mtknama' => 'PRAKTIKUM WEB 2',                    'mtkasing' => 'Web Practicum 2',               'mtksks' => 1, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB104107', 'mtknama' => 'ETIKA PROFESI',                      'mtkasing' => 'Professional Ethics',           'mtksks' => 2, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
        ]);
        // Sem 5
        DB::table('matakuliah')->insertOrIgnore([
            ['mtkid' => 'MKB105101', 'mtknama' => 'PEMROGRAMAN MOBILE',                 'mtkasing' => 'Mobile Programming',           'mtksks' => 3, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB105102', 'mtknama' => 'KEAMANAN SISTEM INFORMASI',          'mtkasing' => 'IS Security',                   'mtksks' => 3, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB105103', 'mtknama' => 'SISTEM PENDUKUNG KEPUTUSAN',         'mtkasing' => 'Decision Support Systems',      'mtksks' => 3, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB105104', 'mtknama' => 'DATA MINING',                        'mtkasing' => 'Data Mining',                   'mtksks' => 3, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB105105', 'mtknama' => 'AUDIT SISTEM INFORMASI',             'mtkasing' => 'IS Audit',                      'mtksks' => 2, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB105106', 'mtknama' => 'PRAKTIKUM MOBILE',                   'mtkasing' => 'Mobile Practicum',              'mtksks' => 1, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB105107', 'mtknama' => 'E-COMMERCE',                         'mtkasing' => 'E-Commerce',                    'mtksks' => 2, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
        ]);
        // Sem 6
        DB::table('matakuliah')->insertOrIgnore([
            ['mtkid' => 'MKB106101', 'mtknama' => 'PEMROGRAMAN BERORIENTASI OBJEK',     'mtkasing' => 'Object Oriented Programming',   'mtksks' => 3, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB106102', 'mtknama' => 'CLOUD COMPUTING',                    'mtkasing' => 'Cloud Computing',               'mtksks' => 3, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB106103', 'mtknama' => 'TATA KELOLA TI',                     'mtkasing' => 'IT Governance',                 'mtksks' => 3, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB106104', 'mtknama' => 'INTERAKSI MANUSIA KOMPUTER',         'mtkasing' => 'Human Computer Interaction',    'mtksks' => 3, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB106105', 'mtknama' => 'KEWIRAUSAHAAN',                      'mtkasing' => 'Entrepreneurship',              'mtksks' => 2, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB106106', 'mtknama' => 'PRAKTIKUM OOP',                      'mtkasing' => 'OOP Practicum',                 'mtksks' => 1, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB106107', 'mtknama' => 'METODOLOGI PENELITIAN',              'mtkasing' => 'Research Methodology',          'mtksks' => 2, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
        ]);
        // Sem 7
        DB::table('matakuliah')->insertOrIgnore([
            ['mtkid' => 'MKB107101', 'mtknama' => 'SISTEM ENTERPRISE',                  'mtkasing' => 'Enterprise Systems',            'mtksks' => 3, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB107102', 'mtknama' => 'MANAJEMEN RISIKO TI',                'mtkasing' => 'IT Risk Management',            'mtksks' => 3, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB107103', 'mtknama' => 'SEMINAR PROPOSAL',                   'mtkasing' => 'Research Proposal Seminar',     'mtksks' => 2, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB107104', 'mtknama' => 'PENGEMBANGAN APLIKASI WEB',          'mtkasing' => 'Web App Development',           'mtksks' => 3, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB107105', 'mtknama' => 'PILIHAN 1',                          'mtkasing' => 'Elective 1',                    'mtksks' => 3, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB107226', 'mtknama' => 'PRAKTEK KERJA LAPANGAN',             'mtkasing' => 'Field Work Practice',           'mtksks' => 2, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
        ]);
        // Sem 8
        DB::table('matakuliah')->insertOrIgnore([
            ['mtkid' => 'MKB108101', 'mtknama' => 'SKRIPSI',                            'mtkasing' => 'Thesis',                        'mtksks' => 6, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB108102', 'mtknama' => 'PILIHAN 2',                          'mtkasing' => 'Elective 2',                    'mtksks' => 3, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
            ['mtkid' => 'MKB108103', 'mtknama' => 'SEMINAR HASIL',                      'mtkasing' => 'Result Seminar',                'mtksks' => 2, 'mtkdesc' => null, 'mtkkelid' => 'MKB', 'mtkuserinput' => 'admin', 'mtktglinput' => $now],
        ]);

        $this->command->info('  ✓ Mata Kuliah (43 MK, sem 1-8)');
    }

    // ── Kurikulum — mapping MK ke prodi, tahun, semester ──────────────────
    private function seedKurikulum(): void
    {
        $kurikulumData = [
            // [mtkid, kursem]
            // Semester 1
            ['MKB101101', 1], ['MKB101102', 1], ['MKB101103', 1],
            ['MKB101104', 1], ['MKB101105', 1], ['MKB101106', 1], ['MKB101107', 1],
            // Semester 2
            ['MKB102101', 2], ['MKB102102', 2], ['MKB102103', 2],
            ['MKB102104', 2], ['MKB102105', 2], ['MKB102106', 2], ['MKB102107', 2],
            // Semester 3
            ['MKB103101', 3], ['MKB103102', 3], ['MKB103103', 3],
            ['MKB103104', 3], ['MKB103105', 3], ['MKB103106', 3], ['MKB103107', 3],
            // Semester 4
            ['MKB104101', 4], ['MKB104102', 4], ['MKB104103', 4],
            ['MKB104104', 4], ['MKB104105', 4], ['MKB104106', 4], ['MKB104107', 4],
            // Semester 5
            ['MKB105101', 5], ['MKB105102', 5], ['MKB105103', 5],
            ['MKB105104', 5], ['MKB105105', 5], ['MKB105106', 5], ['MKB105107', 5],
            // Semester 6
            ['MKB106101', 6], ['MKB106102', 6], ['MKB106103', 6],
            ['MKB106104', 6], ['MKB106105', 6], ['MKB106106', 6], ['MKB106107', 6],
            // Semester 7
            ['MKB107101', 7], ['MKB107102', 7], ['MKB107103', 7],
            ['MKB107104', 7], ['MKB107105', 7], ['MKB107226', 7],
            // Semester 8
            ['MKB108101', 8], ['MKB108102', 8], ['MKB108103', 8],
        ];

        foreach ($kurikulumData as [$mtkid, $sem]) {
            DB::table('kurikulum')->insertOrIgnore([
                [
                    'kurmtkid'           => $mtkid,
                    'kurprodiid'         => self::PRODI_ID,
                    'kurtahun'           => self::TAHUNKUR,
                    'kursem'             => $sem,
                    'kurmtkidprasyarat'  => null,
                    'kurmtkidprasyarat2' => null,
                    'kurmtkidprasyarat3' => null,
                ],
            ]);
        }
        $this->command->info('  ✓ Kurikulum (48 entri, sem 1-8)');
    }

    // ── Setting biaya ─────────────────────────────────────────────────────
    private function seedSettingBiaya(): void
    {
        DB::table('settingbiaya')->insertOrIgnore([
            [
                'prodi'        => self::PRODI_ID,
                'angkatan'     => self::ANGKATAN,
                'kelas'        => 'R',
                'biaya'        => 3500000,   // SPP penuh
                'biaya1'       => 1750000,   // Cicilan 1 (50%)
                'biaya2'       => 1750000,   // Cicilan 2 (50%)
                'pembangunan1' => 1500000,   // Uang gedung tahun 1
                'pembangunan2' => null,      // Tahun 2 dst tidak ada
                'pembangunan3' => null,
                'pembangunan4' => null,
                'orientasi'    => 300000,    // Biaya PKKMB/orientasi
            ],
        ]);
        $this->command->info('  ✓ SettingBiaya');
    }

    // ── Semester (sem 1-8 mahasiswa angkatan 2022) ────────────────────────
    private function seedSem(): void
    {
        DB::table('sem')->insertOrIgnore([
            ['semid' => 20221, 'semnama' => 'Ganjil 2022/2023', 'semmulai' => '2022-09-01', 'semselesai' => '2023-01-31', 'semtglkrsmulai' => '2022-08-15 00:00:00', 'semtglkrsselesai' => '2022-09-05 23:59:59', 'semtglnilaimulai' => '2023-01-01', 'semtglnilaiselesai' => '2023-01-31', 'semsksbaru' => 20, 'semsksbss' => 24, 'semangkatanbaru' => 2022, 'semlalu' => null,  'semaktif' => 0],
            ['semid' => 20222, 'semnama' => 'Genap 2022/2023',  'semmulai' => '2023-02-01', 'semselesai' => '2023-07-31', 'semtglkrsmulai' => '2023-01-20 00:00:00', 'semtglkrsselesai' => '2023-02-10 23:59:59', 'semtglnilaimulai' => '2023-06-01', 'semtglnilaiselesai' => '2023-07-31', 'semsksbaru' => 20, 'semsksbss' => 24, 'semangkatanbaru' => 2022, 'semlalu' => 20221, 'semaktif' => 0],
            ['semid' => 20231, 'semnama' => 'Ganjil 2023/2024', 'semmulai' => '2023-09-01', 'semselesai' => '2024-01-31', 'semtglkrsmulai' => '2023-08-15 00:00:00', 'semtglkrsselesai' => '2023-09-05 23:59:59', 'semtglnilaimulai' => '2024-01-01', 'semtglnilaiselesai' => '2024-01-31', 'semsksbaru' => 20, 'semsksbss' => 24, 'semangkatanbaru' => 2023, 'semlalu' => 20222, 'semaktif' => 0],
            ['semid' => 20232, 'semnama' => 'Genap 2023/2024',  'semmulai' => '2024-02-01', 'semselesai' => '2024-07-31', 'semtglkrsmulai' => '2024-01-20 00:00:00', 'semtglkrsselesai' => '2024-02-10 23:59:59', 'semtglnilaimulai' => '2024-06-01', 'semtglnilaiselesai' => '2024-07-31', 'semsksbaru' => 20, 'semsksbss' => 24, 'semangkatanbaru' => 2023, 'semlalu' => 20231, 'semaktif' => 0],
            ['semid' => 20241, 'semnama' => 'Ganjil 2024/2025', 'semmulai' => '2024-09-01', 'semselesai' => '2025-01-31', 'semtglkrsmulai' => '2024-08-15 00:00:00', 'semtglkrsselesai' => '2024-09-05 23:59:59', 'semtglnilaimulai' => '2025-01-01', 'semtglnilaiselesai' => '2025-01-31', 'semsksbaru' => 20, 'semsksbss' => 24, 'semangkatanbaru' => 2024, 'semlalu' => 20232, 'semaktif' => 0],
            ['semid' => 20242, 'semnama' => 'Genap 2024/2025',  'semmulai' => '2025-02-01', 'semselesai' => '2025-07-31', 'semtglkrsmulai' => '2025-01-20 00:00:00', 'semtglkrsselesai' => '2025-02-10 23:59:59', 'semtglnilaimulai' => '2025-06-01', 'semtglnilaiselesai' => '2025-07-31', 'semsksbaru' => 20, 'semsksbss' => 24, 'semangkatanbaru' => 2024, 'semlalu' => 20241, 'semaktif' => 0],
            ['semid' => 20251, 'semnama' => 'Ganjil 2025/2026', 'semmulai' => '2025-09-01', 'semselesai' => '2026-01-31', 'semtglkrsmulai' => '2025-08-15 00:00:00', 'semtglkrsselesai' => '2025-09-05 23:59:59', 'semtglnilaimulai' => '2026-01-01', 'semtglnilaiselesai' => '2026-01-31', 'semsksbaru' => 20, 'semsksbss' => 24, 'semangkatanbaru' => 2025, 'semlalu' => 20242, 'semaktif' => 0],
            // ← Semester AKTIF untuk testing
            ['semid' => 20252, 'semnama' => 'Genap 2025/2026',  'semmulai' => '2026-02-01', 'semselesai' => '2026-07-31', 'semtglkrsmulai' => '2026-08-01 00:00:00', 'semtglkrsselesai' => '2026-12-31 23:59:59', 'semtglnilaimulai' => '2026-06-01', 'semtglnilaiselesai' => '2026-07-31', 'semsksbaru' => 20, 'semsksbss' => 24, 'semangkatanbaru' => 2025, 'semlalu' => 20251, 'semaktif' => 1],
        ]);
        $this->command->info('  ✓ Semester (8 semester, sem aktif: 20252, KRS TERBUKA)');
    }

    // ── Setting global ────────────────────────────────────────────────────
    private function seedSetting(): void
    {
        if (DB::table('setting')->count() === 0) {
            DB::table('setting')->insert([
                'semaktif'          => 20252,
                // KRS DIBUKA LEBAR untuk testing (jauh ke depan)
                'semkrsmulai'       => '2026-08-01 00:00:00',
                'semkrsselesai'     => '2026-12-31 23:59:59',
                'semubahkrsmulai'   => '2027-01-01 00:00:00',
                'semubahkrsselesai' => '2027-01-15 23:59:59',
                'semnilaimulai'     => '2026-06-01 00:00:00',
                'semnilaiselesai'   => '2026-07-31 23:59:59',
                'portalisaktif'     => 1,
            ]);
        } else {
            // Update agar KRS terbuka
            DB::table('setting')->update([
                'semaktif'        => 20252,
                'semkrsmulai'     => '2026-08-01 00:00:00',
                'semkrsselesai'   => '2026-12-31 23:59:59',
            ]);
        }
        $this->command->info('  ✓ Setting (semaktif=20252, KRS terbuka s/d 2026-12-31)');
    }

    // ── Mahasiswa ─────────────────────────────────────────────────────────
    private function seedMahasiswa(): void
    {
        // Pastikan referensi tabel tersedia
        DB::table('agama')->insertOrIgnore([['agamaid' => 1, 'agamanama' => 'Islam']]);
        DB::table('jalur')->insertOrIgnore([['jalurid' => 1, 'jalurnama' => 'Reguler']]);
        DB::table('kel')->insertOrIgnore([
            ['kelid' => 'R', 'kelnama' => 'Reguler'],
            ['kelid' => 'E', 'kelnama' => 'Reguler Malam'],
            ['kelid' => 'K', 'kelnama' => 'KIP Kuliah'],
        ]);
        DB::table('stat')->insertOrIgnore([
            ['statid' => 1, 'statnama' => 'Aktif'],
            ['statid' => 2, 'statnama' => 'Non-Aktif'],
            ['statid' => 3, 'statnama' => 'Cuti'],
        ]);
        DB::table('sumberbiayakul')->insertOrIgnore([
            ['sumberid' => 1, 'sumbernama' => 'Mandiri'],
            ['sumberid' => 2, 'sumbernama' => 'KIP Kuliah'],
        ]);
        DB::table('tahunkur')->insertOrIgnore([['tahun' => 2022]]);
        DB::table('kelompok')->insertOrIgnore([['kelid' => 'MKB', 'kelnama' => 'Mata Kuliah Bidang Studi']]);
        DB::table('fakultas')->insertOrIgnore([['fakid' => 1, 'faknama' => 'Fakultas Teknologi Informasi', 'fakpim' => 'Dr. Hendra, M.Kom', 'fakwapim' => 'Ir. Santi, M.T']]);
        DB::table('prodi')->insertOrIgnore([
            ['prodiid' => 1, 'prodinama' => 'Sistem Informasi', 'prodinamaasing' => 'Information Systems', 'prodifakid' => 1, 'proditanggalsk' => '2010-01-01', 'prodinosk' => '001/SK/2010', 'prodijpid' => 1, 'prodipejabat' => 'Mike Febri Mayang Sari, M.Kom', 'prodikodeps' => 'SI', 'prodikodejenjang' => 'S', 'prodiptid' => 1, 'prodinbkode' => 1],
        ]);

        DB::table('mahasiswa')->insertOrIgnore([[
            'mhsnobp'         => self::NOBP,
            'mhsnama'         => 'ADITIA NOVIRMAN',
            'mhsalamat'       => 'KOTO PULAI',
            'mhsangkatan'     => self::ANGKATAN,
            'mhsprodiid'      => self::PRODI_ID,
            'mhsagamaid'      => 1,
            'mhsjalurid'      => 1,
            'mhsstatid'       => 1,
            'mhstgllhr'       => '1 November 2002',
            'mhstmplhr'       => 'Koto Pulai',
            'mhsjkl'          => 'L',
            'mhsortu'         => 'WAZARIATI ZARWAN',
            'mhsibu'          => 'YURNIDA',
            'mhstelp'         => '089517647957',
            'mhstahunkur'     => self::TAHUNKUR,
            'mhskel'          => 'R',
            'mhsasalsekolah'  => 'SMAN 1 Koto Pulai',
            'mhssemidmasuk'   => 20221,
            'mhsnik'          => '1301070110020003',
            'mhsnisn'         => '0',
            'mhsumberbiayaid' => 1,
            'mhsemail'        => 'aditia@jayanusa.ac.id',
            'mhstgllahir'     => '2002-11-01',
            'mhskelurahan'    => 'Koto Pulai',
            'mhskecamatan'    => 'Lubuk Kilangan',
        ]]);
        $this->command->info('  ✓ Mahasiswa (2210050 - ADITIA NOVIRMAN)');
    }

    // ── Kelas & Jadwal (1 kelas per MK per semester) ──────────────────────
    private function seedKelasAndJadwal(): void
    {
        // Map semesterMK → semid aktual + dosen + ruang + hari + jam
        $semMap = [
            1 => ['semid' => 20221, 'dosen' => self::DOSEN2, 'ruang' => 1,  'hari' => 'Senin',   'jamawal' => 1, 'jamakhir' => 3],
            2 => ['semid' => 20222, 'dosen' => self::DOSEN2, 'ruang' => 1,  'hari' => 'Selasa',  'jamawal' => 1, 'jamakhir' => 3],
            3 => ['semid' => 20231, 'dosen' => self::DOSEN3, 'ruang' => 2,  'hari' => 'Rabu',    'jamawal' => 1, 'jamakhir' => 3],
            4 => ['semid' => 20232, 'dosen' => self::DOSEN3, 'ruang' => 2,  'hari' => 'Kamis',   'jamawal' => 1, 'jamakhir' => 3],
            5 => ['semid' => 20241, 'dosen' => self::DOSEN4, 'ruang' => 5,  'hari' => 'Jumat',   'jamawal' => 1, 'jamakhir' => 3],
            6 => ['semid' => 20242, 'dosen' => self::DOSEN4, 'ruang' => 5,  'hari' => 'Sabtu',   'jamawal' => 1, 'jamakhir' => 3],
            7 => ['semid' => 20251, 'dosen' => self::DOSEN1, 'ruang' => 3,  'hari' => 'Minggu',  'jamawal' => 1, 'jamakhir' => 3],
            8 => ['semid' => 20252, 'dosen' => self::DOSEN1, 'ruang' => 3,  'hari' => 'Senin',   'jamawal' => 4, 'jamakhir' => 6],
        ];

        // MK per semester dengan variasi hari/ruang/dosen
        $mkPerSem = [
            1 => [
                ['mtkid' => 'MKB101101', 'hari' => 'Senin',   'ruang' => 1, 'dosen' => self::DOSEN2, 'jamawal' => 1, 'jamakhir' => 3],
                ['mtkid' => 'MKB101102', 'hari' => 'Senin',   'ruang' => 2, 'dosen' => self::DOSEN3, 'jamawal' => 4, 'jamakhir' => 6],
                ['mtkid' => 'MKB101103', 'hari' => 'Selasa',  'ruang' => 1, 'dosen' => self::DOSEN4, 'jamawal' => 1, 'jamakhir' => 3],
                ['mtkid' => 'MKB101104', 'hari' => 'Selasa',  'ruang' => 2, 'dosen' => self::DOSEN5, 'jamawal' => 4, 'jamakhir' => 5],
                ['mtkid' => 'MKB101105', 'hari' => 'Rabu',    'ruang' => 1, 'dosen' => self::DOSEN1, 'jamawal' => 1, 'jamakhir' => 2],
                ['mtkid' => 'MKB101106', 'hari' => 'Rabu',    'ruang' => 2, 'dosen' => self::DOSEN2, 'jamawal' => 4, 'jamakhir' => 6],
                ['mtkid' => 'MKB101107', 'hari' => 'Kamis',   'ruang' => 5, 'dosen' => self::DOSEN2, 'jamawal' => 1, 'jamakhir' => 1],
            ],
            2 => [
                ['mtkid' => 'MKB102101', 'hari' => 'Senin',   'ruang' => 1, 'dosen' => self::DOSEN2, 'jamawal' => 1, 'jamakhir' => 3],
                ['mtkid' => 'MKB102102', 'hari' => 'Senin',   'ruang' => 2, 'dosen' => self::DOSEN3, 'jamawal' => 4, 'jamakhir' => 6],
                ['mtkid' => 'MKB102103', 'hari' => 'Selasa',  'ruang' => 1, 'dosen' => self::DOSEN4, 'jamawal' => 1, 'jamakhir' => 3],
                ['mtkid' => 'MKB102104', 'hari' => 'Selasa',  'ruang' => 7, 'dosen' => self::DOSEN5, 'jamawal' => 4, 'jamakhir' => 6],
                ['mtkid' => 'MKB102105', 'hari' => 'Rabu',    'ruang' => 2, 'dosen' => self::DOSEN3, 'jamawal' => 1, 'jamakhir' => 2],
                ['mtkid' => 'MKB102106', 'hari' => 'Rabu',    'ruang' => 5, 'dosen' => self::DOSEN2, 'jamawal' => 4, 'jamakhir' => 4],
                ['mtkid' => 'MKB102107', 'hari' => 'Kamis',   'ruang' => 6, 'dosen' => self::DOSEN4, 'jamawal' => 1, 'jamakhir' => 1],
            ],
            3 => [
                ['mtkid' => 'MKB103101', 'hari' => 'Senin',   'ruang' => 1, 'dosen' => self::DOSEN2, 'jamawal' => 1, 'jamakhir' => 3],
                ['mtkid' => 'MKB103102', 'hari' => 'Senin',   'ruang' => 2, 'dosen' => self::DOSEN4, 'jamawal' => 4, 'jamakhir' => 6],
                ['mtkid' => 'MKB103103', 'hari' => 'Selasa',  'ruang' => 1, 'dosen' => self::DOSEN3, 'jamawal' => 1, 'jamakhir' => 3],
                ['mtkid' => 'MKB103104', 'hari' => 'Selasa',  'ruang' => 2, 'dosen' => self::DOSEN1, 'jamawal' => 4, 'jamakhir' => 6],
                ['mtkid' => 'MKB103105', 'hari' => 'Rabu',    'ruang' => 3, 'dosen' => self::DOSEN5, 'jamawal' => 1, 'jamakhir' => 2],
                ['mtkid' => 'MKB103106', 'hari' => 'Rabu',    'ruang' => 6, 'dosen' => self::DOSEN4, 'jamawal' => 4, 'jamakhir' => 4],
                ['mtkid' => 'MKB103107', 'hari' => 'Kamis',   'ruang' => 5, 'dosen' => self::DOSEN2, 'jamawal' => 1, 'jamakhir' => 1],
            ],
            4 => [
                ['mtkid' => 'MKB104101', 'hari' => 'Senin',   'ruang' => 1, 'dosen' => self::DOSEN2, 'jamawal' => 1, 'jamakhir' => 3],
                ['mtkid' => 'MKB104102', 'hari' => 'Senin',   'ruang' => 2, 'dosen' => self::DOSEN3, 'jamawal' => 4, 'jamakhir' => 6],
                ['mtkid' => 'MKB104103', 'hari' => 'Selasa',  'ruang' => 3, 'dosen' => self::DOSEN1, 'jamawal' => 1, 'jamakhir' => 3],
                ['mtkid' => 'MKB104104', 'hari' => 'Selasa',  'ruang' => 2, 'dosen' => self::DOSEN4, 'jamawal' => 4, 'jamakhir' => 6],
                ['mtkid' => 'MKB104105', 'hari' => 'Rabu',    'ruang' => 1, 'dosen' => self::DOSEN5, 'jamawal' => 1, 'jamakhir' => 2],
                ['mtkid' => 'MKB104106', 'hari' => 'Rabu',    'ruang' => 5, 'dosen' => self::DOSEN2, 'jamawal' => 4, 'jamakhir' => 4],
                ['mtkid' => 'MKB104107', 'hari' => 'Kamis',   'ruang' => 4, 'dosen' => self::DOSEN3, 'jamawal' => 1, 'jamakhir' => 2],
            ],
            5 => [
                ['mtkid' => 'MKB105101', 'hari' => 'Senin',   'ruang' => 5, 'dosen' => self::DOSEN2, 'jamawal' => 1, 'jamakhir' => 3],
                ['mtkid' => 'MKB105102', 'hari' => 'Senin',   'ruang' => 2, 'dosen' => self::DOSEN4, 'jamawal' => 4, 'jamakhir' => 6],
                ['mtkid' => 'MKB105103', 'hari' => 'Selasa',  'ruang' => 3, 'dosen' => self::DOSEN1, 'jamawal' => 1, 'jamakhir' => 3],
                ['mtkid' => 'MKB105104', 'hari' => 'Selasa',  'ruang' => 5, 'dosen' => self::DOSEN3, 'jamawal' => 4, 'jamakhir' => 6],
                ['mtkid' => 'MKB105105', 'hari' => 'Rabu',    'ruang' => 2, 'dosen' => self::DOSEN5, 'jamawal' => 1, 'jamakhir' => 2],
                ['mtkid' => 'MKB105106', 'hari' => 'Rabu',    'ruang' => 6, 'dosen' => self::DOSEN2, 'jamawal' => 4, 'jamakhir' => 4],
                ['mtkid' => 'MKB105107', 'hari' => 'Kamis',   'ruang' => 1, 'dosen' => self::DOSEN4, 'jamawal' => 1, 'jamakhir' => 2],
            ],
            6 => [
                ['mtkid' => 'MKB106101', 'hari' => 'Senin',   'ruang' => 5, 'dosen' => self::DOSEN2, 'jamawal' => 1, 'jamakhir' => 3],
                ['mtkid' => 'MKB106102', 'hari' => 'Senin',   'ruang' => 3, 'dosen' => self::DOSEN4, 'jamawal' => 4, 'jamakhir' => 6],
                ['mtkid' => 'MKB106103', 'hari' => 'Selasa',  'ruang' => 2, 'dosen' => self::DOSEN3, 'jamawal' => 1, 'jamakhir' => 3],
                ['mtkid' => 'MKB106104', 'hari' => 'Selasa',  'ruang' => 8, 'dosen' => self::DOSEN5, 'jamawal' => 4, 'jamakhir' => 6],
                ['mtkid' => 'MKB106105', 'hari' => 'Rabu',    'ruang' => 1, 'dosen' => self::DOSEN1, 'jamawal' => 1, 'jamakhir' => 2],
                ['mtkid' => 'MKB106106', 'hari' => 'Rabu',    'ruang' => 6, 'dosen' => self::DOSEN2, 'jamawal' => 4, 'jamakhir' => 4],
                ['mtkid' => 'MKB106107', 'hari' => 'Kamis',   'ruang' => 2, 'dosen' => self::DOSEN4, 'jamawal' => 1, 'jamakhir' => 2],
            ],
            7 => [
                ['mtkid' => 'MKB107101', 'hari' => 'Jumat',   'ruang' => 2, 'dosen' => self::DOSEN3, 'jamawal' => 1, 'jamakhir' => 3],
                ['mtkid' => 'MKB107102', 'hari' => 'Jumat',   'ruang' => 3, 'dosen' => self::DOSEN4, 'jamawal' => 4, 'jamakhir' => 6],
                ['mtkid' => 'MKB107103', 'hari' => 'Sabtu',   'ruang' => 9, 'dosen' => self::DOSEN1, 'jamawal' => 1, 'jamakhir' => 2],
                ['mtkid' => 'MKB107104', 'hari' => 'Sabtu',   'ruang' => 5, 'dosen' => self::DOSEN2, 'jamawal' => 4, 'jamakhir' => 6],
                ['mtkid' => 'MKB107105', 'hari' => 'Minggu',  'ruang' => 1, 'dosen' => self::DOSEN5, 'jamawal' => 1, 'jamakhir' => 3],
                ['mtkid' => 'MKB107226', 'hari' => 'Minggu',  'ruang' => 3, 'dosen' => self::DOSEN1, 'jamawal' => 4, 'jamakhir' => 5],
            ],
            8 => [
                ['mtkid' => 'MKB108101', 'hari' => 'Senin',   'ruang' => 9, 'dosen' => self::DOSEN1, 'jamawal' => 1, 'jamakhir' => 6],
                ['mtkid' => 'MKB108102', 'hari' => 'Selasa',  'ruang' => 2, 'dosen' => self::DOSEN3, 'jamawal' => 1, 'jamakhir' => 3],
                ['mtkid' => 'MKB108103', 'hari' => 'Rabu',    'ruang' => 9, 'dosen' => self::DOSEN1, 'jamawal' => 1, 'jamakhir' => 2],
            ],
        ];

       
        $semIdMap = [1=>20221, 2=>20222, 3=>20231, 4=>20232, 5=>20241, 6=>20242, 7=>20251, 8=>20252];

        $kelasId  = 100; // ID awal kelas (hindari konflik dengan seeder lama)
        $jadwalId = 100;

        foreach ($mkPerSem as $semMk => $mklist) {
            $semId = $semIdMap[$semMk];

            foreach ($mklist as $mk) {
                // Ambil kurid
                $kurid = DB::table('kurikulum')
                    ->where('kurmtkid', $mk['mtkid'])
                    ->where('kurprodiid', self::PRODI_ID)
                    ->value('kurid');

                if (!$kurid) continue;

                $kode = $semId . '-' . self::PRODI_ID . '-' . str_pad($kelasId, 3, '0', STR_PAD_LEFT);

                DB::table('kelas')->insertOrIgnore([[
                    'kelasid'           => $kelasId,
                    'kelaskode'         => $kode,
                    'kelaskurid'        => $kurid,
                    'kelasprodiid'      => self::PRODI_ID,
                    'kelassem'          => $semId,
                    'kelasmax'          => 30,
                    'kelastanggalinput' => now(),
                    'kelasuserinput'    => 'admin',
                    'kelasnobpmin'      => '0',
                    'kelasnobpmax'      => '9999999',
                    'kelaskel'          => 'R',
                    'kelaslabel'        => 'A',
                    'kelasnilai'        => null,
                    'kelasket'          => null,
                ]]);

                DB::table('kelasjadwal')->insertOrIgnore([[
                    'jadwalid'           => $jadwalId,
                    'jadwalkelasid'      => $kelasId,
                    'jadwalruangid'      => $mk['ruang'],
                    'jadwaljamidawal'    => $mk['jamawal'],
                    'jadwalhari'         => $mk['hari'],
                    'jadwaldosenid'      => $mk['dosen'],
                    'jadwaltanggalinput' => now(),
                    'jadwaluserinput'    => 'admin',
                    'jadwaljamidakhir'   => $mk['jamakhir'],
                ]]);

                DB::table('kelasangkatan')->insertOrIgnore([[
                    'kelasangkelasid'  => $kelasId,
                    'kelasangangkatan' => self::ANGKATAN,
                ]]);

                $kelasId++;
                $jadwalId++;
            }
        }
        $this->command->info('  ✓ Kelas & Jadwal (48 kelas, 48 jadwal)');
    }

    // ── SPP & Registrasi ─────────────────────────────────────────────────
    private function seedSppAndRegistrasi(): void
    {
        // Tagihan SPP sem 1-8 (sem 8 = aktif, belum lunas untuk testing)
        $sppData = [
            [20221, 3500000], [20222, 3500000], [20231, 3500000], [20232, 3500000],
            [20241, 3500000], [20242, 3500000], [20251, 3500000], [20252, 3500000],
        ];
        foreach ($sppData as [$semId, $tagihan]) {
            DB::table('spp')->insertOrIgnore([
                ['sppmhsnobp' => self::NOBP, 'sppsem' => $semId, 'spptagihan' => $tagihan],
            ]);
        }

        // Registrasi (lunas) sem 1-7 — sem 8 (20252) SENGAJA tidak ada agar bisa test upload bukti
        $regData = [
            [20221, '2022-09-10', 'JN/2022/09/000001'],
            [20222, '2023-02-08', 'JN/2023/02/000001'],
            [20231, '2023-09-12', 'JN/2023/09/000001'],
            [20232, '2024-02-07', 'JN/2024/02/000001'],
            [20241, '2024-09-09', 'JN/2024/09/000001'],
            [20242, '2025-02-06', 'JN/2025/02/000001'],
            [20251, '2025-09-10', 'JN/2025/09/000001'],
        ];
        foreach ($regData as [$semId, $tgl, $nobukti]) {
            DB::table('registrasi')->insertOrIgnore([[
                'regmhsnobp'      => self::NOBP,
                'regsem'          => $semId,
                'regjumlahbayar'  => 3500000,
                'regtanggalbayar' => $tgl,
                'reguserinput'    => 'admin',
                'regnobukti'      => $nobukti,
            ]]);
        }

        // bukti_pembayaran dikonfirmasi sem 1-7
        foreach ($regData as [$semId, $tgl, $nobukti]) {
            DB::table('bukti_pembayaran')->insertOrIgnore([[
                'mhsnobp'         => self::NOBP,
                'sppsem'          => $semId,
                'jumlah_bayar'    => 3500000,
                'tipe_bayar'      => 'penuh',
                'file_path'       => null,
                'file_compressed' => null,
                'status'          => 'dikonfirmasi',
                'catatan'         => null,
                'confirmed_by'    => 1,
                'confirmed_at'    => $tgl . ' 09:00:00',
                'created_at'      => $tgl . ' 08:00:00',
                'updated_at'      => $tgl . ' 09:00:00',
            ]]);
        }

        $this->command->info('  ✓ SPP (8 tagihan) + Registrasi (7 lunas, sem 8 belum lunas)');
    }

    // ── KRS & Nilai sem 1-7 + KRS sem 8 (aktif, tanpa nilai) ─────────────
    private function seedKrsAndNilai(): void
    {
        // Nilai per semester: realistis naik dari sem 1
        $nilaiPerSem = [
            1 => ['MKB101101'=>'B','MKB101102'=>'B','MKB101103'=>'A','MKB101104'=>'A','MKB101105'=>'A','MKB101106'=>'C','MKB101107'=>'A'],
            2 => ['MKB102101'=>'B','MKB102102'=>'B','MKB102103'=>'A','MKB102104'=>'B','MKB102105'=>'B','MKB102106'=>'A','MKB102107'=>'A'],
            3 => ['MKB103101'=>'A','MKB103102'=>'B','MKB103103'=>'B','MKB103104'=>'A','MKB103105'=>'B','MKB103106'=>'A','MKB103107'=>'A'],
            4 => ['MKB104101'=>'A','MKB104102'=>'B','MKB104103'=>'A','MKB104104'=>'B','MKB104105'=>'A','MKB104106'=>'A','MKB104107'=>'A'],
            5 => ['MKB105101'=>'A','MKB105102'=>'A','MKB105103'=>'B','MKB105104'=>'B','MKB105105'=>'A','MKB105106'=>'A','MKB105107'=>'A'],
            6 => ['MKB106101'=>'A','MKB106102'=>'B','MKB106103'=>'A','MKB106104'=>'A','MKB106105'=>'A','MKB106106'=>'A','MKB106107'=>'B'],
            7 => ['MKB107101'=>'A','MKB107102'=>'A','MKB107103'=>'A','MKB107104'=>'A','MKB107105'=>'B','MKB107226'=>'A'],
        ];

        $bobotMap  = ['A'=>4,'B'=>3,'C'=>2,'D'=>1,'E'=>0];
        $semIdMap  = [1=>20221,2=>20222,3=>20231,4=>20232,5=>20241,6=>20242,7=>20251,8=>20252];

        $krsIdCounter = 5000; // mulai dari ID tinggi agar tidak bentrok

        foreach ($nilaiPerSem as $semMk => $nilaiList) {
            $semId = $semIdMap[$semMk];

            // Ambil regid untuk semester ini
            $regid = DB::table('registrasi')
                ->where('regmhsnobp', self::NOBP)
                ->where('regsem', $semId)
                ->value('regid');

            if (!$regid) continue;

            foreach ($nilaiList as $mtkid => $nilai) {
                // Cari kelasid berdasarkan kurid + semester
                $kurid = DB::table('kurikulum')
                    ->where('kurmtkid', $mtkid)
                    ->where('kurprodiid', self::PRODI_ID)
                    ->value('kurid');

                $kelasid = DB::table('kelas')
                    ->where('kelaskurid', $kurid)
                    ->where('kelassem', $semId)
                    ->value('kelasid');

                if (!$kelasid) continue;

                DB::table('krs')->insertOrIgnore([[
                    'krsid'              => $krsIdCounter,
                    'krsregid'           => $regid,
                    'krsmhsnobp'         => self::NOBP,
                    'krskelasid'         => $kelasid,
                    'krsnilai'           => $nilai,
                    'krssem'             => $semId,
                    'krsjmlabsen'        => rand(0, 2),
                    'krstanggalambil'    => now()->subMonths((8 - $semMk) * 6),
                    'krsuserambil'       => self::NOBP,
                    'krshapus'           => 0,
                    'krsbobot'           => $bobotMap[$nilai],
                    'krsinputnilaimetode'=> 'portal',
                    'krsapproved'        => 1,
                    'krstglapproved'     => now()->subMonths((8 - $semMk) * 6),
                    'krskomplain'        => 0,
                ]]);

                $krsIdCounter++;
            }
        }

        // KRS sem 8 (20252) — aktif, belum ada nilai, belum approved
        // Semester 8 masih berjalan, mahasiswa sudah isi KRS
        $semId8 = 20252;
        $reg8   = DB::table('registrasi')
            ->where('regmhsnobp', self::NOBP)
            ->where('regsem', $semId8)
            ->value('regid');

        // Sem 8 belum lunas, tapi kita buat registrasi sementara untuk KRS (auto-create)
        if (!$reg8) {
            $reg8 = DB::table('registrasi')->insertGetId([
                'regmhsnobp'      => self::NOBP,
                'regsem'          => $semId8,
                'regjumlahbayar'  => 0,
                'regtanggalbayar' => now()->toDateString(),
                'reguserinput'    => self::NOBP,
                'regnobukti'      => 'AUTO-' . self::NOBP . '-' . $semId8,
            ]);
        }

        $mk8List = ['MKB108101', 'MKB108102', 'MKB108103'];
        foreach ($mk8List as $mtkid) {
            $kurid = DB::table('kurikulum')
                ->where('kurmtkid', $mtkid)
                ->where('kurprodiid', self::PRODI_ID)
                ->value('kurid');

            $kelasid = DB::table('kelas')
                ->where('kelaskurid', $kurid)
                ->where('kelassem', $semId8)
                ->value('kelasid');

            if (!$kelasid) continue;

            DB::table('krs')->insertOrIgnore([[
                'krsid'              => $krsIdCounter,
                'krsregid'           => $reg8,
                'krsmhsnobp'         => self::NOBP,
                'krskelasid'         => $kelasid,
                'krsnilai'           => '',
                'krssem'             => $semId8,
                'krsjmlabsen'        => 0,
                'krstanggalambil'    => now(),
                'krsuserambil'       => self::NOBP,
                'krshapus'           => 0,
                'krsbobot'           => 0,
                'krsinputnilaimetode'=> 'portal',
                'krsapproved'        => 1,
                'krstglapproved'     => now(),
                'krskomplain'        => 0,
            ]]);

            $krsIdCounter++;
        }

        $this->command->info('  ✓ KRS + Nilai (sem 1-7 lengkap + sem 8 aktif tanpa nilai)');
    }

    // ── User login ────────────────────────────────────────────────────────
    private function seedUser(): void
    {
        // User mahasiswa
        \App\Models\User::firstOrCreate(
            ['mhsnobp' => self::NOBP],
            [
                'name'     => 'ADITIA NOVIRMAN',
                'email'    => 'aditia@jayanusa.ac.id',
                'password' => Hash::make('01112002'),
                'role'     => 'mahasiswa',
                'mhsnobp'  => self::NOBP,
            ]
        );

        // User dosen (DSN001) — cek via dosenid agar tidak duplicate
        if (!\App\Models\User::where('dosenid', self::DOSEN1)->exists()) {
            \App\Models\User::create([
                'name'     => 'Mike Febri Mayang Sari',
                'email'    => 'mike@jayanusa.ac.id',
                'password' => Hash::make('password123'),
                'role'     => 'dosen',
                'dosenid'  => self::DOSEN1,
            ]);
        }

        // Admin
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@jayanusa.ac.id'],
            [
                'name'     => 'Administrator',
                'email'    => 'admin@jayanusa.ac.id',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
            ]
        );

        $this->command->info('  ✓ Users (mahasiswa + dosen + admin)');
    }

    // ── Ringkasan akhir ───────────────────────────────────────────────────
    private function printSummary(): void
    {
        $this->command->info('');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('  LOGIN API (Mobile App)');
        $this->command->info('  ┌─────────────────────────────────────────────────');
        $this->command->info('  │  Role      : Mahasiswa');
        $this->command->info('  │  NoBP      : 2210050');
        $this->command->info('  │  Password  : 01112002');
        $this->command->info('  ├─────────────────────────────────────────────────');
        $this->command->info('  │  Role      : Dosen');
        $this->command->info('  │  DosenID   : DSN001');
        $this->command->info('  │  Password  : password123');
        $this->command->info('  ├─────────────────────────────────────────────────');
        $this->command->info('  │  Role      : Admin Panel');
        $this->command->info('  │  Email     : admin@jayanusa.ac.id');
        $this->command->info('  │  Password  : admin123');
        $this->command->info('  └─────────────────────────────────────────────────');
        $this->command->info('');
        $this->command->info('  STATUS DATA');
        $this->command->info('  ┌─────────────────────────────────────────────────');
        $this->command->info('  │  Mata Kuliah  : 46 MK (sem 1-8)');
        $this->command->info('  │  Kelas/Jadwal : 48 kelas, 48 jadwal');
        $this->command->info('  │  SPP          : 8 tagihan (@ Rp 3.500.000)');
        $this->command->info('  │  Lunas        : Sem 1-7 ✓ | Sem 8 BELUM LUNAS');
        $this->command->info('  │  KRS + Nilai  : Sem 1-7 lengkap | Sem 8 aktif');
        $this->command->info('  │  Sem Aktif    : 20252 (Genap 2025/2026)');
        $this->command->info('  │  KRS Buka     : s/d 31 Des 2026');
        $this->command->info('  └─────────────────────────────────────────────────');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}
