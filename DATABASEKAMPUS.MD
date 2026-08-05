# JAYANUSA_DATA.md — Struktur Data Kampus Institut Jayanusa
> Referensi dari portal.jayanusa.ac.id
> Digunakan sebagai acuan dummy data & tipe data di aplikasi mobile
> Versi: 1.0.0 | Juli 2026

---

## SCOPE APLIKASI MOBILE (Mahasiswa)

Berdasarkan use case & tampilan portal, fitur mahasiswa di mobile:

| No | Fitur | Keterangan |
|---|---|---|
| 1 | Lihat Profil Mahasiswa | Read only, tidak bisa edit dari mobile |
| 2 | KRS — Lihat KRS aktif | Daftar MK yang sudah diambil |
| 3 | KRS — Tambah KRS | Pilih dari daftar kelas tersedia |
| 4 | Daftar Kelas | Lihat kelas yang tersedia per semester |
| 5 | Histori Nilai | Transkrip nilai semua semester |
| 6 | Bayar Uang Kuliah | Upload & cek status pembayaran SPP |
| 7 | Pengumuman (Info Terbaru) | Baca pengumuman kampus |
| **TIDAK ADA** | Input nilai | Hanya admin/dosen |
| **TIDAK ADA** | Tambah mata kuliah | Hanya admin |
| **TIDAK ADA** | Ubah jadwal | Hanya admin |

---

## 1. DATA MAHASISWA (Profil)

Referensi: `portal.jayanusa.ac.id/mhs/profil`

| Field | Contoh Data | Catatan |
|---|---|---|
| NoBP | 2210050 | Nomor Buku Pokok (NIM/NPM di kampus ini) |
| NIK (No KTP) | 1301070110020003 | Wajib diisi untuk pelaporan DIKTI |
| Nama | ADITIA NOVIRMAN | Huruf kapital |
| Program Studi | Sistem Informasi | |
| Tahun Masuk | 2022 | |
| Semester Awal Masuk | 20221 | Format: YYYYS (1=Ganjil, 2=Genap) |
| Tempat/Tanggal Lahir | Koto pulai / 1 November 2002 | |
| Jenis Kelamin | Laki-Laki | |
| Alamat | KOTO PULAI | |
| Telp./No Hp | 089517647957 | |
| Agama | Islam | |
| Nama Ayah | WAZARIATI ZARWAN | |
| Nama Ibu | YURNIDA | |
| Biaya Kuliah | 3.515.000 | SPP per semester |
| Pembimbing Akademik | Mike Febri Mayang Sari, M.Kom | Dosen PA |
| Status | Aktif | Aktif / Non-Aktif / Cuti |

---

## 2. DATA KRS (Kartu Rencana Studi)

Referensi: `portal.jayanusa.ac.id/krs`

### Header KRS
| Field | Contoh | Catatan |
|---|---|---|
| NoBP | 2210050 | |
| Nama | ADITIA NOVIRMAN | |
| Program Studi | Sistem Informasi | |
| Dosen PA | Mike Febri Mayang Sari, M.Kom | Pembimbing Akademik |
| Total SKS | 6 | SKS yang sudah diambil semester ini |
| Max SKS | 24 | Maksimal SKS yang boleh diambil |

### Tabel KRS (per MK)
| Field | Contoh | Catatan |
|---|---|---|
| No | 1, 2, 3... | Nomor urut |
| Kelas | 20252-1-005 (A) | Format: YYYYS-Semester-Kelas |
| Kode MK | MKB107226 | Kode mata kuliah |
| Nama Matakuliah | PRAKTEK KERJA LAPANGAN | |
| SKS | 2 | Jumlah SKS |
| Jadwal — Dosen | Ir. Darwati, MM | |
| Jadwal — Lokal | - | Nama ruangan |
| Jadwal — Hari | Minggu | |
| Jadwal — Jam | ---- | Bisa kosong |
| Status KRS | Approved | Approved / Pending / Rejected |
| Aksi | Approved | Status dari dosen PA |

---

## 3. DATA DAFTAR KELAS (Tersedia untuk Tambah KRS)

Referensi: `portal.jayanusa.ac.id/kelas` & `portal.jayanusa.ac.id/krs/addkrs`

### Tampilan per Semester
| Field | Contoh | Catatan |
|---|---|---|
| Semester | 2 | Semester mata kuliah |
| No | 1, 2, 3... | |
| Kelas | 20252-1-001 [A] | |
| Matakuliah | ALGORITMA DAN STRUKTUR DATA 2 | |
| SKS | 2 | |
| Sem | 2 | Semester MK |
| Angkatan | 2004, 2007, 2008... | Angkatan yang bisa ambil |
| NoBP Min/Max | 0 s.d 9999999 | Range NoBP yang diizinkan |
| Kapasitas | 30 | Kapasitas kelas |
| Kel | A | Kelompok kelas |
| Jml MHS | 19 | Jumlah mahasiswa terdaftar |
| Keterangan | (kosong) | |
| Jadwal — Dosen | Ahmad Fikri Fajri, S.Kom, M.Kom | |
| Jadwal — Hari | - (atau Senin dst) | |
| Jadwal — Ruang | Lokal 1 / Lab 2 | |
| Jadwal — Jam | 07.00-07.00 | Format HH.mm-HH.mm |
| Pilih (tombol) | + / Penuh | Tombol tambah atau label "Penuh" |

---

## 4. HISTORI NILAI

Referensi: `portal.jayanusa.ac.id/historinilai`

### Header
| Field | Contoh | Catatan |
|---|---|---|
| No Bp | 22100050 | |
| Nama | ADITIA NOVIRMAN | |
| Pembimbing Akademik | Mike Febri Mayang Sari, M.Kom | |

### Tabel Nilai (per Semester)
| Field | Contoh | Catatan |
|---|---|---|
| Semester | Ganjil 2022/2023 | Format: Ganjil/Genap YYYY/YYYY |
| No | 1, 2, 3... | |
| Kode | MKB101101 | Kode MK |
| Nama Matakuliah | PRAKTIKUM ALGORITMA DAN STRUKTUR DATA 1 | |
| SKS | 2, 1 | |
| Nilai | A, B, C, D, E | Nilai huruf |
| Bobot | 3, 4 | Nilai bobot (A=4, B=3, C=2 dst) |
| Mutu | 8, 6 | SKS × Bobot |
| Ket. | Lulus / Gagal | |
| Total SKS | 20 | Per semester |
| Total Mutu | 64 | Per semester |
| IP | 3.2 | Per semester |

---

## 5. PEMBAYARAN (SPP / Uang Kuliah)

Referensi: Berdasarkan field `Biaya Kuliah` di profil + tabel `settingbiaya`

### Struktur Biaya Kuliah per Angkatan

| Angkatan | Jalur | Biaya/Semester | Keterangan |
|---|---|---|---|
| 2024+ (BP 24) | Reguler | Rp 4.100.000 | Biaya penuh |
| 2022 - 2023 (BP 22-23) | Reguler | Rp 3.500.000 | Biaya penuh |
| Semua angkatan | KIP Kuliah | Rp 15.000 | Uang kemahasiswaan saja |
| Semua angkatan | Reguler E (Kelas Malam) | Rp 3.500.000 | Kelas reguler malam |

> Referensi DB: tabel `settingbiaya` — field `biaya` (reguler), `biaya1` (cicilan 1), `biaya2` (cicilan 2)
> Referensi DB: tabel `mahasiswa` — field `mhskel` (R=Reguler, E=Reguler Malam, K=KIP)
> Referensi DB: tabel `spp` — menyimpan tagihan per semester per mahasiswa

### Sistem Cicilan
Berdasarkan tabel `settingbiaya`:
- `biaya` = Biaya penuh
- `biaya1` = Cicilan pertama (biasanya 50%)
- `biaya2` = Cicilan kedua (sisa)

### Tabel Tagihan SPP

| Field | Contoh | Sumber DB |
|---|---|---|
| NoBP | 2210050 | `spp.sppmhsnobp` |
| Nama | ADITIA NOVIRMAN | JOIN `mahasiswa` |
| Semester | Ganjil 2025/2026 | JOIN `sem.semnama` |
| Tagihan | 3.500.000 | `spp.spptagihan` |
| Status | Lunas / Belum Lunas | Cek dari `registrasi` |
| Tanggal Bayar | 10 Agustus 2025 | `registrasi.regtanggalbayar` |
| No Bukti | JN/2025/08/001234 | `registrasi.regnobukti` |
| Jumlah Bayar | 3.500.000 | `registrasi.regjumlahbayar` |

---

## 6. NAVIGASI PORTAL (Menu Mahasiswa)

Referensi: Dashboard portal.jayanusa.ac.id/mhs

| No | Menu | URL | Mobile |
|---|---|---|---|
| 1 | Home | /mhs | ✅ Dashboard |
| 2 | Kartu Rencana Studi | /krs | ✅ KRSScreen |
| 3 | Daftar Kelas | /kelas | ✅ via Tambah KRS |
| 4 | Hasil Studi | /hasilstudi | ✅ NilaiScreen (summary) |
| 5 | Histori Nilai | /historinilai | ✅ NilaiScreen (detail) |
| 6 | Profil | /mhs/profil | ✅ ProfileScreen |
| 7 | Quisioner | /quisioner | ⏳ Fase berikutnya |
| 8 | Ganti Password | /gantipassword | ✅ UbahPasswordScreen |

---

## 7. FORMAT & KONVENSI DATA

### Format Kode Kelas
```
20252-1-005 (A)
│││││ │ │││ └── Kelompok kelas: A, B, C...
│││││ │ └────── Nomor urut kelas
│││││ └──────── Semester: 1=Ganjil, 2=Genap
└────────────── Tahun Akademik: 2025-2 (Genap 2025)
```

### Format Semester Akademik
```
20221 = Tahun 2022, Semester 1 (Ganjil)
20222 = Tahun 2022, Semester 2 (Genap)
20251 = Tahun 2025, Semester 1 (Ganjil)
```

### Nilai & Bobot
| Nilai | Bobot | Keterangan |
|---|---|---|
| A | 4 | Sangat Baik |
| B | 3 | Baik |
| C | 2 | Cukup |
| D | 1 | Kurang |
| E | 0 | Gagal |

### Status KRS
| Status | Keterangan |
|---|---|
| Approved | Disetujui dosen PA |
| Pending | Menunggu persetujuan |
| Rejected | Ditolak |

---

## 8. PERUBAHAN YANG DIBUTUHKAN DI DUMMY DATA

Berdasarkan analisis portal, berikut yang perlu diupdate di `src/data/dummy.ts`:

### MahasiswaProfile — Field yang perlu ditambah:
- `noBP` (ganti dari `nim` → `noBP` sesuai portal)
- `nik` — NIK/No KTP
- `tahunMasuk` — tahun masuk (2022)
- `semesterAwalMasuk` — 20221
- `tempatLahir` + `tanggalLahir`
- `jenisKelamin`
- `alamat`
- `agama`
- `namaAyah`, `namaIbu`
- `biayaKuliah` — nominal SPP per semester
- `dosenPA` — nama pembimbing akademik
- `statusMahasiswa` — Aktif / Non-Aktif / Cuti

### MataKuliah (Kelas) — Field yang perlu ditambah:
- `kodeKelas` — format 20252-1-001 [A]
- `kapasitas` — maks mahasiswa
- `jumlahMahasiswa` — yang sudah daftar
- `ruangan` — Lokal 1, Lab 2, dst
- `angkatan` — list angkatan yang bisa ambil

### NilaiItem — Field yang perlu ditambah/ubah:
- `bobot` — nilai bobot (A=4, B=3 dst)
- `mutu` — SKS × bobot
- `keterangan` — Lulus / Gagal

### KRSItem — Field yang perlu disesuaikan:
- `kodeKelas` — format 20252-1-005 (A)
- `ruangan` / `lokal` — nama ruangan
- `dosenPA` — di header KRS

---

## 9. RENCANA PERUBAHAN DUMMY DATA (Prioritas)

```
Tahap 1 (Saat ini): Update MahasiswaProfile sesuai field portal
Tahap 2:            Update NilaiItem — tambah bobot, mutu, keterangan Lulus/Gagal
Tahap 3:            Update KRSItem — sesuaikan field dengan portal
Tahap 4:            Update DaftarKelas — tambah kapasitas, jumlah MHS, ruangan
Tahap 5:            Update Pembayaran — sesuaikan dengan biayaKuliah dari profil
```
/*
SQLyog Enterprise
MySQL - 10.6.12-MariaDB-1:10.6.12+maria~ubu2004 : Database - admin_dbsiaj
*********************************************************************
*/
  DATABASE KAMPUS ( )
/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `agama` */

DROP TABLE IF EXISTS `agama`;

CREATE TABLE `agama` (
  `agamaid` int(11) NOT NULL,
  `agamanama` char(30) DEFAULT NULL,
  PRIMARY KEY (`agamaid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Table structure for table `biayakompre` */

DROP TABLE IF EXISTS `biayakompre`;

CREATE TABLE `biayakompre` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `periode` varchar(30) DEFAULT NULL,
  `biaya1` double DEFAULT NULL,
  `biaya2` double DEFAULT NULL,
  `biaya3` double DEFAULT NULL,
  `biaya4` double DEFAULT NULL,
  `aktif` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Table structure for table `dosen` */

DROP TABLE IF EXISTS `dosen`;

CREATE TABLE `dosen` (
  `dosenid` char(10) NOT NULL,
  `dosennama` char(100) DEFAULT NULL,
  `dosenalamat` char(50) DEFAULT NULL,
  `dosentelp` char(20) DEFAULT NULL,
  `dosengelardepan` char(15) DEFAULT NULL,
  `dosengelarbelakang` char(15) DEFAULT NULL,
  `dosenjpid` int(11) DEFAULT NULL,
  `dosenprodiid` int(11) DEFAULT NULL,
  `dosennidn` char(20) DEFAULT NULL,
  `dosenstatus` int(1) DEFAULT NULL,
  PRIMARY KEY (`dosenid`),
  KEY `fkdosen1` (`dosenprodiid`),
  KEY `idosen1` (`dosennama`),
  CONSTRAINT `fkdosen1` FOREIGN KEY (`dosenprodiid`) REFERENCES `prodi` (`prodiid`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Table structure for table `fakultas` */

DROP TABLE IF EXISTS `fakultas`;

CREATE TABLE `fakultas` (
  `fakid` int(11) NOT NULL,
  `faknama` char(50) DEFAULT NULL,
  `fakpim` varchar(50) DEFAULT NULL,
  `fakwapim` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`fakid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Table structure for table `jalur` */

DROP TABLE IF EXISTS `jalur`;

CREATE TABLE `jalur` (
  `jalurid` int(11) NOT NULL,
  `jalurnama` char(30) DEFAULT NULL,
  PRIMARY KEY (`jalurid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Table structure for table `kel` */

DROP TABLE IF EXISTS `kel`;

CREATE TABLE `kel` (
  `kelid` char(1) NOT NULL,
  `kelnama` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`kelid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Table structure for table `kelas` */

DROP TABLE IF EXISTS `kelas`;

CREATE TABLE `kelas` (
  `kelasid` bigint(20) NOT NULL AUTO_INCREMENT,
  `kelaskode` char(11) DEFAULT NULL,
  `kelaskurid` bigint(20) DEFAULT NULL,
  `kelasprodiid` int(1) DEFAULT NULL,
  `kelassem` int(11) DEFAULT NULL,
  `kelasmax` int(11) DEFAULT 30,
  `kelastanggalinput` timestamp NULL DEFAULT NULL,
  `kelasuserinput` char(20) DEFAULT NULL,
  `kelastanggalubah` datetime DEFAULT NULL,
  `kelasuserubah` char(20) DEFAULT NULL,
  `kelasnobpmin` varchar(7) DEFAULT '0',
  `kelasnobpmax` varchar(7) DEFAULT '9999999',
  `kelaskel` char(1) DEFAULT 'R',
  `kelaslabel` varchar(2) DEFAULT '-',
  `kelasnilai` int(11) DEFAULT NULL,
  `kelasket` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`kelasid`),
  UNIQUE KEY `kelaskode` (`kelaskode`),
  KEY `fkkelas1` (`kelaskurid`),
  KEY `ikelas1` (`kelaskode`),
  KEY `kelasprodiid` (`kelasprodiid`),
  KEY `kelas_ibfk_1` (`kelassem`),
  CONSTRAINT `fkkelas1` FOREIGN KEY (`kelaskurid`) REFERENCES `kurikulum` (`kurid`) ON UPDATE CASCADE,
  CONSTRAINT `kelas_ibfk_1` FOREIGN KEY (`kelassem`) REFERENCES `sem` (`semid`) ON UPDATE CASCADE,
  CONSTRAINT `kelas_ibfk_2` FOREIGN KEY (`kelasprodiid`) REFERENCES `prodi` (`prodiid`) ON UPDATE CASCADE,
  CONSTRAINT `kelas_ibfk_3` FOREIGN KEY (`kelasprodiid`) REFERENCES `prodi` (`prodiid`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8172 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Table structure for table `kelasangkatan` */

DROP TABLE IF EXISTS `kelasangkatan`;

CREATE TABLE `kelasangkatan` (
  `kelasangkelasid` bigint(20) DEFAULT NULL,
  `kelasangangkatan` int(4) DEFAULT NULL,
  UNIQUE KEY `kelasangkelasid` (`kelasangkelasid`,`kelasangangkatan`),
  CONSTRAINT `kelasangkatan_ibfk_1` FOREIGN KEY (`kelasangkelasid`) REFERENCES `kelas` (`kelasid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Table structure for table `kelasjadwal` */

DROP TABLE IF EXISTS `kelasjadwal`;

CREATE TABLE `kelasjadwal` (
  `jadwalid` bigint(20) NOT NULL AUTO_INCREMENT,
  `jadwalkelasid` bigint(20) DEFAULT NULL,
  `jadwalruangid` int(11) DEFAULT NULL,
  `jadwaljamidawal` int(1) DEFAULT NULL,
  `jadwalhari` char(10) DEFAULT NULL,
  `jadwaldosenid` char(10) DEFAULT NULL,
  `jadwaltanggalinput` timestamp NULL DEFAULT NULL,
  `jadwaluserinput` char(20) DEFAULT NULL,
  `jadwaljamidakhir` int(1) DEFAULT NULL,
  `jadwalqsuts` int(11) DEFAULT NULL,
  `jadwalrespondenuts` int(11) DEFAULT NULL,
  `jadwalqsuas` int(11) DEFAULT NULL,
  `jadwalrespondenuas` int(11) DEFAULT NULL,
  PRIMARY KEY (`jadwalid`),
  KEY `fkjadwal2` (`jadwaldosenid`),
  KEY `ikj1` (`jadwalhari`),
  KEY `ikj2` (`jadwaljamidawal`),
  KEY `ikj3` (`jadwaljamidakhir`),
  KEY `ikj4` (`jadwalkelasid`),
  CONSTRAINT `fkjadwal1` FOREIGN KEY (`jadwalkelasid`) REFERENCES `kelas` (`kelasid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fkjadwal2` FOREIGN KEY (`jadwaldosenid`) REFERENCES `dosen` (`dosenid`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8078 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Table structure for table `kelompok` */

DROP TABLE IF EXISTS `kelompok`;

CREATE TABLE `kelompok` (
  `kelid` char(10) NOT NULL,
  `kelnama` char(50) DEFAULT NULL,
  PRIMARY KEY (`kelid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Table structure for table `krs` */

DROP TABLE IF EXISTS `krs`;

CREATE TABLE `krs` (
  `krsid` bigint(20) NOT NULL AUTO_INCREMENT,
  `krsregid` bigint(20) NOT NULL,
  `krsmhsnobp` char(7) DEFAULT NULL,
  `krskelasid` bigint(20) DEFAULT NULL,
  `krsnilai` char(1) DEFAULT '',
  `krssem` int(11) DEFAULT NULL,
  `krsjmlabsen` int(11) DEFAULT 0,
  `krstanggalambil` datetime DEFAULT NULL,
  `krsuserambil` char(20) DEFAULT NULL,
  `krstanggalhapus` datetime DEFAULT NULL,
  `krsuserhapus` char(20) DEFAULT NULL,
  `krshapus` int(1) DEFAULT 0,
  `krstanggalnilai` datetime DEFAULT NULL,
  `krsusernilai` char(20) DEFAULT NULL,
  `krsbobot` int(1) DEFAULT 0,
  `krsinputnilaimetode` varchar(10) DEFAULT 'portal',
  `krsapproved` int(1) DEFAULT 0,
  `krstglapproved` datetime DEFAULT NULL,
  `krskomplain` int(1) DEFAULT 0,
  PRIMARY KEY (`krsid`),
  KEY `fkkrs1` (`krsmhsnobp`),
  KEY `fkkrs2` (`krskelasid`),
  KEY `krsregid` (`krsregid`),
  KEY `ikrs1` (`krssem`),
  CONSTRAINT `fkkrs1` FOREIGN KEY (`krsmhsnobp`) REFERENCES `mahasiswa` (`mhsnobp`) ON UPDATE CASCADE,
  CONSTRAINT `fkkrs2` FOREIGN KEY (`krskelasid`) REFERENCES `kelas` (`kelasid`) ON UPDATE CASCADE,
  CONSTRAINT `krs_ibfk_1` FOREIGN KEY (`krsregid`) REFERENCES `registrasi` (`regid`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=238153 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Table structure for table `kurikulum` */

DROP TABLE IF EXISTS `kurikulum`;

CREATE TABLE `kurikulum` (
  `kurid` bigint(20) NOT NULL AUTO_INCREMENT,
  `kurmtkid` char(10) DEFAULT NULL,
  `kurprodiid` int(11) DEFAULT NULL,
  `kurtahun` int(11) DEFAULT NULL,
  `kursem` int(2) DEFAULT 0,
  `kurmtkidprasyarat` char(10) DEFAULT NULL,
  `kurmtkidprasyarat2` varchar(10) DEFAULT NULL,
  `kurmtkidprasyarat3` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`kurid`),
  UNIQUE KEY `kurmtkid` (`kurmtkid`,`kurprodiid`,`kurtahun`),
  KEY `fkkur1` (`kurmtkid`),
  KEY `fkkur2` (`kurprodiid`),
  KEY `kurmtkidprasyarat` (`kurmtkidprasyarat`),
  CONSTRAINT `fkkur1` FOREIGN KEY (`kurmtkid`) REFERENCES `matakuliah` (`mtkid`) ON UPDATE CASCADE,
  CONSTRAINT `fkkur2` FOREIGN KEY (`kurprodiid`) REFERENCES `prodi` (`prodiid`) ON UPDATE CASCADE,
  CONSTRAINT `kurikulum_ibfk_1` FOREIGN KEY (`kurmtkidprasyarat`) REFERENCES `matakuliah` (`mtkid`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1048 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Table structure for table `label` */

DROP TABLE IF EXISTS `label`;

CREATE TABLE `label` (
  `labelnama` varchar(2) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Table structure for table `mahasiswa` */

DROP TABLE IF EXISTS `mahasiswa`;

CREATE TABLE `mahasiswa` (
  `mhsnobp` char(7) NOT NULL,
  `mhsnama` char(50) NOT NULL,
  `mhsalamat` char(200) DEFAULT NULL,
  `mhsangkatan` int(11) NOT NULL,
  `mhsprodiid` int(11) NOT NULL,
  `mhsagamaid` int(11) DEFAULT NULL,
  `mhsjalurid` int(11) DEFAULT NULL,
  `mhsstatid` int(11) NOT NULL,
  `mhstgllhr` varchar(20) DEFAULT NULL,
  `mhstmplhr` char(50) DEFAULT NULL,
  `mhsjkl` char(1) DEFAULT NULL,
  `mhsortu` char(50) DEFAULT NULL,
  `mhsibu` varchar(50) DEFAULT NULL,
  `mhstelp` char(50) DEFAULT NULL,
  `mhstahunkur` int(4) DEFAULT NULL,
  `mhskel` char(1) DEFAULT 'R',
  `mhsasalsekolah` varchar(100) DEFAULT NULL,
  `mhssemidmasuk` int(5) DEFAULT NULL,
  `mhsnik` varchar(50) DEFAULT NULL,
  `mhsnisn` char(10) DEFAULT '0',
  `mhsumberbiayaid` int(11) DEFAULT NULL,
  `mhsemail` varchar(100) DEFAULT '',
  `mhstgllahir` date DEFAULT NULL,
  `mhskelurahan` varchar(100) DEFAULT NULL,
  `mhskecamatan` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`mhsnobp`),
  KEY `fkmhs1` (`mhsprodiid`),
  KEY `fkmhs2` (`mhsagamaid`),
  KEY `fkmhs3` (`mhsstatid`),
  KEY `fkmhs4` (`mhsjalurid`),
  KEY `imhs1` (`mhsnama`),
  KEY `imhs2` (`mhsangkatan`),
  KEY `mhstahunkur` (`mhstahunkur`),
  KEY `mhskel` (`mhskel`),
  KEY `mhsumberbiayaid` (`mhsumberbiayaid`),
  CONSTRAINT `fkmhs1` FOREIGN KEY (`mhsprodiid`) REFERENCES `prodi` (`prodiid`) ON UPDATE CASCADE,
  CONSTRAINT `fkmhs2` FOREIGN KEY (`mhsagamaid`) REFERENCES `agama` (`agamaid`) ON UPDATE CASCADE,
  CONSTRAINT `fkmhs3` FOREIGN KEY (`mhsstatid`) REFERENCES `stat` (`statid`) ON UPDATE CASCADE,
  CONSTRAINT `fkmhs4` FOREIGN KEY (`mhsjalurid`) REFERENCES `jalur` (`jalurid`) ON UPDATE CASCADE,
  CONSTRAINT `mahasiswa_ibfk_1` FOREIGN KEY (`mhstahunkur`) REFERENCES `tahunkur` (`tahun`) ON UPDATE CASCADE,
  CONSTRAINT `mahasiswa_ibfk_2` FOREIGN KEY (`mhskel`) REFERENCES `kel` (`kelid`) ON UPDATE CASCADE,
  CONSTRAINT `mahasiswa_ibfk_3` FOREIGN KEY (`mhsumberbiayaid`) REFERENCES `sumberbiayakul` (`sumberid`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Table structure for table `matakuliah` */

DROP TABLE IF EXISTS `matakuliah`;

CREATE TABLE `matakuliah` (
  `mtkid` char(10) NOT NULL,
  `mtknama` char(100) DEFAULT NULL,
  `mtkasing` char(100) DEFAULT NULL,
  `mtksks` int(11) DEFAULT NULL,
  `mtkdesc` char(100) DEFAULT NULL,
  `mtkkelid` char(10) DEFAULT NULL,
  `mtkuserinput` varchar(30) DEFAULT NULL,
  `mtktglinput` datetime DEFAULT NULL,
  `mtkuserubah` varchar(30) DEFAULT NULL,
  `mtktglubah` datetime DEFAULT NULL,
  PRIMARY KEY (`mtkid`),
  KEY `mtkkelid` (`mtkkelid`),
  CONSTRAINT `matakuliah_ibfk_1` FOREIGN KEY (`mtkkelid`) REFERENCES `kelompok` (`kelid`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Table structure for table `mtklab` */

DROP TABLE IF EXISTS `mtklab`;

CREATE TABLE `mtklab` (
  `mtklabid` int(11) NOT NULL AUTO_INCREMENT,
  `mtklabteori` char(10) DEFAULT NULL,
  `mtklabprak` char(10) DEFAULT NULL,
  PRIMARY KEY (`mtklabid`),
  KEY `imtklabteori` (`mtklabteori`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Table structure for table `pembayaran_kompre` */

DROP TABLE IF EXISTS `pembayaran_kompre`;

CREATE TABLE `pembayaran_kompre` (
  `pembkompreid` bigint(20) NOT NULL AUTO_INCREMENT,
  `pembkompremhsnobp` char(7) DEFAULT NULL,
  `pembkompretgl` date DEFAULT NULL,
  `pembkomprenobukti` varchar(100) DEFAULT NULL,
  `pembkomprejumlah` double DEFAULT NULL,
  `pembkompreuserinput` varchar(50) DEFAULT NULL,
  `pembkompreke` int(11) DEFAULT NULL,
  `pembkompreperiode` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`pembkompreid`),
  KEY `pembkompremhsnobp` (`pembkompremhsnobp`),
  CONSTRAINT `pembayaran_kompre_ibfk_1` FOREIGN KEY (`pembkompremhsnobp`) REFERENCES `mahasiswa` (`mhsnobp`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1324 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Table structure for table `pembayaran_wisuda` */

DROP TABLE IF EXISTS `pembayaran_wisuda`;

CREATE TABLE `pembayaran_wisuda` (
  `wispembid` bigint(20) NOT NULL AUTO_INCREMENT,
  `wispembmhsnobp` char(7) DEFAULT NULL,
  `wispembtgl` datetime DEFAULT NULL,
  `wispembnobukti` varchar(50) DEFAULT NULL,
  `wispembjumlah` double DEFAULT NULL,
  `wispembuserinput` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`wispembid`),
  KEY `wispembmhsnobp` (`wispembmhsnobp`),
  CONSTRAINT `pembayaran_wisuda_ibfk_1` FOREIGN KEY (`wispembmhsnobp`) REFERENCES `mahasiswa` (`mhsnobp`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=828 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Table structure for table `periodebayar` */

DROP TABLE IF EXISTS `periodebayar`;

CREATE TABLE `periodebayar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `persemid` int(5) DEFAULT NULL,
  `perstt` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Table structure for table `prodi` */

DROP TABLE IF EXISTS `prodi`;

CREATE TABLE `prodi` (
  `prodiid` int(11) NOT NULL,
  `prodinama` char(50) DEFAULT NULL,
  `prodinamaasing` varchar(50) DEFAULT NULL,
  `prodifakid` int(11) DEFAULT NULL,
  `proditanggalsk` date DEFAULT NULL,
  `prodinosk` char(40) DEFAULT NULL,
  `prodijpid` int(11) DEFAULT NULL,
  `prodipejabat` char(50) DEFAULT NULL,
  `prodipejabat2` varchar(30) DEFAULT NULL,
  `prodipejabat3` varchar(30) DEFAULT NULL,
  `prodikodeps` char(6) DEFAULT NULL,
  `prodikodejenjang` char(1) DEFAULT NULL,
  `prodiptid` int(11) DEFAULT 1,
  `prodinbkode` int(11) DEFAULT 1,
  PRIMARY KEY (`prodiid`),
  KEY `fkprodi1` (`prodifakid`),
  KEY `xprodi2` (`prodiptid`),
  KEY `xprodi3` (`prodinbkode`),
  CONSTRAINT `fkprodi1` FOREIGN KEY (`prodifakid`) REFERENCES `fakultas` (`fakid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Table structure for table `registrasi` */

DROP TABLE IF EXISTS `registrasi`;

CREATE TABLE `registrasi` (
  `regid` bigint(20) NOT NULL AUTO_INCREMENT,
  `regmhsnobp` char(7) DEFAULT NULL,
  `regsem` int(11) DEFAULT NULL,
  `regjumlahbayar` int(11) DEFAULT NULL,
  `regtanggalbayar` date DEFAULT NULL,
  `reguserinput` char(20) DEFAULT NULL,
  `regtanggalinsert` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `regnobukti` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`regid`),
  UNIQUE KEY `regmhsnobp` (`regmhsnobp`,`regsem`),
  KEY `fkreg1` (`regmhsnobp`),
  KEY `registrasi_ibfk_1` (`regsem`),
  CONSTRAINT `fkreg1` FOREIGN KEY (`regmhsnobp`) REFERENCES `mahasiswa` (`mhsnobp`) ON UPDATE CASCADE,
  CONSTRAINT `registrasi_ibfk_1` FOREIGN KEY (`regsem`) REFERENCES `sem` (`semid`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22210 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Table structure for table `ruang` */

DROP TABLE IF EXISTS `ruang`;

CREATE TABLE `ruang` (
  `ruid` int(11) NOT NULL AUTO_INCREMENT,
  `runama` char(20) DEFAULT NULL,
  PRIMARY KEY (`ruid`),
  KEY `iruang1` (`runama`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Table structure for table `sem` */

DROP TABLE IF EXISTS `sem`;

CREATE TABLE `sem` (
  `semid` int(5) NOT NULL DEFAULT 0,
  `semnama` varchar(18) DEFAULT NULL,
  `semmulai` datetime DEFAULT NULL,
  `semselesai` datetime DEFAULT NULL,
  `semtglkrsmulai` datetime DEFAULT NULL,
  `semtglkrsselesai` datetime DEFAULT NULL,
  `semtglnilaimulai` datetime DEFAULT NULL,
  `semtglnilaiselesai` datetime DEFAULT NULL,
  `semsksbaru` int(2) DEFAULT NULL,
  `semsksbss` int(2) DEFAULT NULL,
  `semangkatanbaru` int(4) DEFAULT NULL,
  `semlalu` int(5) DEFAULT NULL,
  `semaktif` int(1) DEFAULT 0,
  `semtglkomplain` date DEFAULT NULL,
  PRIMARY KEY (`semid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Table structure for table `setting` */

DROP TABLE IF EXISTS `setting`;

CREATE TABLE `setting` (
  `semaktif` int(11) DEFAULT NULL,
  `semkrsmulai` datetime DEFAULT NULL,
  `semkrsselesai` datetime DEFAULT NULL,
  `semubahkrsmulai` datetime DEFAULT NULL,
  `semubahkrsselesai` datetime DEFAULT NULL,
  `semnilaimulai` datetime DEFAULT NULL,
  `semnilaiselesai` datetime DEFAULT NULL,
  `portalisaktif` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Table structure for table `settingbiaya` */

DROP TABLE IF EXISTS `settingbiaya`;

CREATE TABLE `settingbiaya` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prodi` int(11) DEFAULT NULL,
  `angkatan` int(11) DEFAULT NULL,
  `kelas` char(1) DEFAULT NULL,
  `biaya` double DEFAULT NULL,
  `biaya1` double DEFAULT NULL,
  `biaya2` double DEFAULT NULL,
  `pembangunan1` double DEFAULT NULL,
  `pembangunan2` double DEFAULT NULL,
  `pembangunan3` double DEFAULT NULL,
  `pembangunan4` double DEFAULT NULL,
  `orientasi` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Table structure for table `spp` */

DROP TABLE IF EXISTS `spp`;

CREATE TABLE `spp` (
  `sppid` bigint(20) NOT NULL AUTO_INCREMENT,
  `sppmhsnobp` char(7) DEFAULT NULL,
  `sppsem` int(5) DEFAULT NULL,
  `spptagihan` double DEFAULT NULL,
  PRIMARY KEY (`sppid`),
  KEY `sppmhsnobp` (`sppmhsnobp`),
  KEY `sppsem` (`sppsem`),
  CONSTRAINT `spp_ibfk_1` FOREIGN KEY (`sppmhsnobp`) REFERENCES `mahasiswa` (`mhsnobp`) ON UPDATE CASCADE,
  CONSTRAINT `spp_ibfk_2` FOREIGN KEY (`sppsem`) REFERENCES `sem` (`semid`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Table structure for table `sumberbiayakul` */

DROP TABLE IF EXISTS `sumberbiayakul`;

CREATE TABLE `sumberbiayakul` (
  `sumberid` int(11) NOT NULL AUTO_INCREMENT,
  `sumbernama` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`sumberid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
