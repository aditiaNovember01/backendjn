# PROJECT.md — Halo Jayanusa Backend
> API Backend + Admin Panel untuk Aplikasi Mobile "Halo Jayanusa"
> Institut Teknologi & Bisnis Jayanusa
> Stack: Laravel 12 + Filament 3 + MySQL
> Versi: 1.0.0 | Juli 2026

---

## GAMBARAN UMUM

Proyek ini adalah backend untuk aplikasi mobile **Halo Jayanusa** yang melayani kebutuhan akademik mahasiswa Institut Jayanusa. Backend ini terdiri dari dua bagian utama:

1. **REST API** — Endpoint untuk aplikasi mobile (mahasiswa)
2. **Admin Panel (Filament)** — Dashboard admin untuk konfirmasi pembayaran, manajemen pengumuman, dan laporan

---

## ARSITEKTUR SISTEM

```
┌─────────────────────────────────────────────┐
│           Aplikasi Mobile (Flutter)          │
│  Mahasiswa · Dosen · (future: Pimpinan)      │
└──────────────┬──────────────────────────────┘
               │ REST API (JSON)
               │ Bearer Token (Sanctum)
┌──────────────▼──────────────────────────────┐
│         Laravel 12 Backend                   │
│                                              │
│  ┌──────────────────┐  ┌──────────────────┐  │
│  │   API Routes      │  │  Admin Panel     │  │
│  │  /api/v1/...      │  │  Filament 3      │  │
│  │                  │  │  /admin/...      │  │
│  └──────────────────┘  └──────────────────┘  │
│                                              │
│  ┌──────────────────────────────────────────┐ │
│  │         Database MySQL                   │ │
│  │  admin_dbsiaj (existing schema)          │ │
│  └──────────────────────────────────────────┘ │
└─────────────────────────────────────────────┘
```

---

## STACK TEKNOLOGI

| Komponen | Teknologi | Versi |
|---|---|---|
| Framework | Laravel | 12.x |
| Admin Panel | Filament | 3.x |
| Autentikasi API | Laravel Sanctum | 4.x |
| Database | MySQL / MariaDB | 10.6+ |
| PHP | PHP | 8.2+ |
| Mobile Client | Flutter | (terpisah) |

---

## STRUKTUR DATABASE (Existing Schema)

Database yang digunakan adalah `admin_dbsiaj` (skema existing dari portal Jayanusa). **Tidak ada perubahan struktur database** — hanya membuat Eloquent Model yang memetakan ke tabel yang sudah ada.

### Tabel Utama yang Digunakan

| Tabel | Deskripsi | Model Eloquent |
|---|---|---|
| `mahasiswa` | Data profil mahasiswa | `Mahasiswa` |
| `krs` | Kartu Rencana Studi | `Krs` |
| `kelas` | Daftar kelas per semester | `Kelas` |
| `kelasjadwal` | Jadwal per kelas (hari/jam/dosen/ruang) | `KelasJadwal` |
| `kelasangkatan` | Angkatan yang boleh ambil suatu kelas | `KelasAngkatan` |
| `matakuliah` | Master mata kuliah | `MataKuliah` |
| `kurikulum` | Mapping MK ke prodi & tahun | `Kurikulum` |
| `registrasi` | Bukti pembayaran SPP per semester | `Registrasi` |
| `spp` | Tagihan SPP per mahasiswa per semester | `Spp` |
| `settingbiaya` | Aturan biaya per prodi/angkatan/kelas | `SettingBiaya` |
| `sem` | Master semester akademik | `Sem` |
| `prodi` | Program studi | `Prodi` |
| `dosen` | Data dosen | `Dosen` |
| `ruang` | Master ruangan | `Ruang` |
| `agama` | Referensi agama | `Agama` |
| `jalur` | Jalur masuk mahasiswa | `Jalur` | (reguler , kip)
| `stat` | Status mahasiswa (Aktif/Cuti/dll) | `Stat` |
| `setting` | Konfigurasi global (semester aktif, periode KRS) | `Setting` |
| `pengumuman` | Pengumuman kampus *(tabel baru)* | `Pengumuman` |
| `users` | Akun admin/dosen untuk login | `User` (Laravel default) |

### Field Penting `mahasiswa`

| Field DB | Arti | Contoh |
|---|---|---|
| `mhsnobp` | NoBP / NIM (PK, 7 digit) | `2210050` |
| `mhsnama` | Nama lengkap | `ADITIA NOVIRMAN` |
| `mhsprodiid` | FK → prodi | `1` |
| `mhsangkatan` | Tahun angkatan | `2022` |
| `mhssemidmasuk` | Semester awal masuk (YYYYS) | `20221` |
| `mhsnik` | NIK/No KTP | `1301070110020003` |
| `mhstmplhr` | Tempat lahir | `KOTO PULAI` |
| `mhstgllahir` | Tanggal lahir (date) | `2002-11-01` |
| `mhsjkl` | Jenis kelamin (L/P) | `L` |
| `mhsalamat` | Alamat | `KOTO PULAI` |
| `mhstelp` | No HP | `089517647957` |
| `mhsagamaid` | FK → agama | `1` |
| `mhsortu` | Nama ayah | `WAZARIATI ZARWAN` |
| `mhsibu` | Nama ibu | `YURNIDA` |
| `mhskel` | Kelas biaya: R/E/K | `R` |
| `mhsstatid` | FK → stat (Aktif/Cuti) | `1` |
| `mhsemail` | Email | `aditia@gmail.com` |

### Field Penting `krs`

| Field DB | Arti |
|---|---|
| `krsid` | PK auto increment |
| `krsmhsnobp` | FK → mahasiswa |
| `krskelasid` | FK → kelas |
| `krssem` | FK → sem |
| `krsnilai` | Nilai huruf (A/B/C/D/E) |
| `krsbobot` | Bobot nilai (4/3/2/1/0) |
| `krsapproved` | 0=Pending, 1=Approved |
| `krshapus` | Soft delete flag |

### Field Penting `registrasi` (Pembayaran)

| Field DB | Arti |
|---|---|
| `regid` | PK auto increment |
| `regmhsnobp` | FK → mahasiswa |
| `regsem` | FK → sem |
| `regjumlahbayar` | Jumlah yang dibayar |
| `regtanggalbayar` | Tanggal bayar |
| `regnobukti` | Nomor bukti pembayaran |

---

## FITUR API (Mobile — Mahasiswa)

### Autentikasi
| Method | Endpoint | Deskripsi |
|---|---|---|
| POST | `/api/v1/login` | Login mahasiswa dengan NoBP + password |
| POST | `/api/v1/logout` | Logout (revoke token) |
| GET | `/api/v1/me` | Info user yang sedang login |

### Profil Mahasiswa
| Method | Endpoint | Deskripsi |
|---|---|---|
| GET | `/api/v1/profil` | Lihat profil mahasiswa (read only) |

### KRS
| Method | Endpoint | Deskripsi |
|---|---|---|
| GET | `/api/v1/krs` | Lihat KRS aktif semester ini |
| GET | `/api/v1/krs/{semId}` | Lihat KRS semester tertentu |
| POST | `/api/v1/krs` | Tambah KRS (ambil kelas) |
| DELETE | `/api/v1/krs/{krsId}` | Batal ambil KRS |

### Daftar Kelas
| Method | Endpoint | Deskripsi |
|---|---|---|
| GET | `/api/v1/kelas` | Daftar kelas tersedia untuk semester aktif |
| GET | `/api/v1/kelas?semester={sem}` | Filter per semester MK |

### Histori Nilai
| Method | Endpoint | Deskripsi |
|---|---|---|
| GET | `/api/v1/nilai` | Histori nilai semua semester |
| GET | `/api/v1/nilai/summary` | Ringkasan IP per semester + IPK |

### Pembayaran / SPP
| Method | Endpoint | Deskripsi |
|---|---|---|
| GET | `/api/v1/spp` | Daftar tagihan SPP semua semester |
| GET | `/api/v1/spp/aktif` | Tagihan semester aktif |
| POST | `/api/v1/spp/upload` | Upload bukti pembayaran (multipart/form-data) |
| GET | `/api/v1/spp/{sppId}/status` | Cek status konfirmasi pembayaran |

### Pengumuman
| Method | Endpoint | Deskripsi |
|---|---|---|
| GET | `/api/v1/pengumuman` | Daftar pengumuman aktif |
| GET | `/api/v1/pengumuman/{id}` | Detail pengumuman |

### Semester
| Method | Endpoint | Deskripsi |
|---|---|---|
| GET | `/api/v1/semester/aktif` | Info semester yang sedang aktif |

---

## FITUR ADMIN PANEL (Filament 3)

URL: `/admin`
Autentikasi: Email + Password (tabel `users` Laravel)

### Dashboard
- Widget: Total mahasiswa aktif
- Widget: Pembayaran pending (menunggu konfirmasi)
- Widget: Total pengumuman aktif
- Widget: Statistik KRS semester aktif

### Resource: Konfirmasi Pembayaran *(Fitur Utama Admin)*
- List semua bukti pembayaran yang diupload mahasiswa
- Filter: Status (Pending / Dikonfirmasi / Ditolak), Semester, Prodi
- Action: **Konfirmasi** → membuat record di tabel `registrasi`
- Action: **Tolak** → update status dengan keterangan
- Tampilkan: NoBP, Nama, Semester, Tagihan, Jumlah Bayar, Bukti (image), Tanggal Upload

### Resource: Pengumuman
- CRUD pengumuman (judul, isi, tanggal publish, status aktif)
- Rich text editor untuk isi pengumuman
- Toggle aktif/nonaktif

### Resource: Mahasiswa *(Read Only)*
- List mahasiswa dengan filter prodi, angkatan, status
- View detail profil (tidak bisa edit dari admin panel ini)

### Resource: Laporan
- Laporan pembayaran per semester (ekspor Excel/PDF)
- Laporan KRS per semester

---

## STRUKTUR FOLDER PROJECT

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       └── V1/
│   │           ├── AuthController.php
│   │           ├── ProfilController.php
│   │           ├── KrsController.php
│   │           ├── KelasController.php
│   │           ├── NilaiController.php
│   │           ├── SppController.php
│   │           └── PengumumanController.php
│   ├── Middleware/
│   │   └── EnsureMahasiswaActive.php
│   └── Resources/
│       └── Api/
│           ├── MahasiswaResource.php
│           ├── KrsResource.php
│           ├── KelasResource.php
│           ├── NilaiResource.php
│           ├── SppResource.php
│           └── PengumumanResource.php
├── Models/
│   ├── Mahasiswa.php
│   ├── Krs.php
│   ├── Kelas.php
│   ├── KelasJadwal.php
│   ├── KelasAngkatan.php
│   ├── MataKuliah.php
│   ├── Kurikulum.php
│   ├── Registrasi.php
│   ├── Spp.php
│   ├── SettingBiaya.php
│   ├── Sem.php
│   ├── Prodi.php
│   ├── Dosen.php
│   ├── Ruang.php
│   ├── Agama.php
│   ├── Jalur.php
│   ├── Stat.php
│   ├── Setting.php
│   ├── Pengumuman.php
│   └── User.php
├── Filament/
│   ├── Resources/
│   │   ├── PembayaranResource.php          ← Konfirmasi pembayaran (main)
│   │   ├── PengumumanResource.php
│   │   ├── MahasiswaResource.php
│   │   └── LaporanResource.php
│   └── Widgets/
│       ├── StatsOverviewWidget.php
│       └── PembayaranPendingWidget.php
└── Providers/
    └── AppServiceProvider.php

database/
├── migrations/
│   ├── ..._create_pengumuman_table.php     ← Tabel baru (pengumuman)
│   └── ..._create_bukti_pembayaran_table.php ← Upload bukti
└── seeders/

routes/
├── api.php                                 ← Semua endpoint API v1
└── web.php                                 ← Filament (auto-registered)

storage/
└── app/public/bukti-pembayaran/            ← Storage upload bukti bayar
```

---

## TABEL BARU (Migration Laravel)

Dua tabel baru yang perlu dibuat (tidak ada di schema existing):

### `pengumuman`
```sql
CREATE TABLE `pengumuman` (
  `id`          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `judul`       VARCHAR(200) NOT NULL,
  `isi`         TEXT NOT NULL,
  `tgl_publish` DATE,
  `aktif`       TINYINT(1) DEFAULT 1,
  `user_id`     BIGINT UNSIGNED,             -- FK → users (admin yang buat)
  `created_at`  TIMESTAMP,
  `updated_at`  TIMESTAMP
);
```

### `bukti_pembayaran`
```sql
CREATE TABLE `bukti_pembayaran` (
  `id`            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `mhsnobp`       CHAR(7) NOT NULL,           -- FK → mahasiswa
  `sppsem`        INT(5) NOT NULL,            -- FK → sem
  `jumlah_bayar`  DOUBLE NOT NULL,
  `file_path`     VARCHAR(255),               -- path file bukti
  `status`        ENUM('pending','dikonfirmasi','ditolak') DEFAULT 'pending',
  `catatan`       VARCHAR(200),               -- catatan penolakan
  `confirmed_by`  BIGINT UNSIGNED,            -- FK → users (admin)
  `confirmed_at`  TIMESTAMP NULL,
  `created_at`    TIMESTAMP,
  `updated_at`    TIMESTAMP
);
```

---

## KONEKSI DATABASE

File `.env` — sesuaikan dengan database kampus existing:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=admin_dbsiaj
DB_USERNAME=root
DB_PASSWORD=

APP_URL=http://jayanusabackend.test

SANCTUM_STATEFUL_DOMAINS=localhost:3000,localhost:8081
```

---

## AUTENTIKASI

### Mahasiswa (API)
- Login menggunakan **NoBP** sebagai username dan **password** (default: tanggal lahir format `ddmmyyyy`)
- Token: Laravel Sanctum Bearer Token
- Middleware: `auth:sanctum` untuk semua route yang dilindungi

### Admin (Filament)
- Login menggunakan **email + password** (tabel `users` Laravel)
- Role: `admin` (single role, tidak ada multi-role di fase ini)

---

## KONVENSI RESPONSE API

### Success
```json
{
  "success": true,
  "message": "Data berhasil diambil",
  "data": { ... }
}
```

### Error
```json
{
  "success": false,
  "message": "Pesan error",
  "errors": { ... }
}
```

### Pagination
```json
{
  "success": true,
  "data": [...],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7
  }
}
```

---

## RENCANA IMPLEMENTASI (Tahapan)

### Fase 1 — Setup & Core ✅ (Saat ini)
- [x] Laravel 12 fresh install
- [ ] Install & konfigurasi Filament 3
- [ ] Install Laravel Sanctum
- [ ] Konfigurasi koneksi ke `admin_dbsiaj`
- [ ] Buat semua Eloquent Model (mapping ke tabel existing)
- [ ] Migration tabel baru (`pengumuman`, `bukti_pembayaran`)

### Fase 2 — API Autentikasi
- [ ] `POST /api/v1/login` — login mahasiswa
- [ ] `POST /api/v1/logout`
- [ ] `GET /api/v1/me`
- [ ] Middleware autentikasi

### Fase 3 — API Mahasiswa
- [ ] `GET /api/v1/profil`
- [ ] `GET /api/v1/krs` + `POST /api/v1/krs` + `DELETE`
- [ ] `GET /api/v1/kelas`
- [ ] `GET /api/v1/nilai`
- [ ] `GET /api/v1/spp` + `POST /api/v1/spp/upload`
- [ ] `GET /api/v1/pengumuman`

### Fase 4 — Admin Panel Filament
- [ ] Setup Filament 3 + admin user
- [ ] Dashboard widget (stats)
- [ ] Resource: Konfirmasi Pembayaran (list + approve + reject)
- [ ] Resource: Pengumuman (CRUD)
- [ ] Resource: Mahasiswa (read only)

### Fase 5 — Polish & Deploy
- [ ] Validasi & error handling lengkap
- [ ] API documentation (Postman/Swagger)
- [ ] Laporan ekspor (Excel)
- [ ] Rate limiting
- [ ] Deployment ke server

---

## 10. DETAIL PERANCANGAN IMPLEMENTASI

### 10.1 Alur Upload & Kompresi Bukti Pembayaran

```
Mobile App
    │
    │  POST /api/v1/spp/upload
    │  Content-Type: multipart/form-data
    │  { file: <image>, jumlah_bayar: 3500000, sem_id: 20251 }
    │
    ▼
SppController::uploadBukti()
    │
    ├─ Validasi: image, max 5MB, format jpg/png/webp
    ├─ Cek SPP exist untuk semester tersebut
    ├─ Cek belum Lunas (tidak ada di tabel registrasi)
    ├─ Cek tidak ada upload pending sebelumnya
    │
    ▼
ImageCompressionService::store()
    │
    ├─ Simpan original  → storage/app/public/bukti-pembayaran/original/{uuid}.ext
    ├─ Kompres via GD   → resize max 1200px lebar, quality 65 JPEG
    └─ Simpan compressed → storage/app/public/bukti-pembayaran/compressed/{uuid}.jpg
    │
    ▼
BuktiPembayaran::create()
    status = 'pending'
    file_path = 'bukti-pembayaran/original/{uuid}.ext'
    file_compressed = 'bukti-pembayaran/compressed/{uuid}.jpg'
```

### 10.2 Alur Konfirmasi Pembayaran (Admin Filament)

```
Admin membuka /admin/bukti-pembayarans
    │
    ├─ Tab "Pending" menampilkan semua upload yang belum dikonfirmasi
    ├─ Badge merah di sidebar menunjukkan jumlah pending
    │
    ▼
Admin klik tombol [Konfirmasi]
    │
    ├─ Modal konfirmasi muncul (nama + jumlah)
    │
    ▼
BuktiPembayaranResource Action::konfirmasi
    │
    ├─ DB::transaction:
    │   ├─ Registrasi::updateOrCreate()  ← tanda mahasiswa LUNAS
    │   │   { regmhsnobp, regsem, regjumlahbayar, regtanggalbayar, regnobukti }
    │   └─ BuktiPembayaran::update()
    │       { status='dikonfirmasi', confirmed_by=admin_id, confirmed_at=now() }
    │
    └─ Notifikasi sukses muncul di panel

Admin klik tombol [Tolak]
    │
    ├─ Form modal: isi alasan penolakan (required)
    │
    ▼
    ├─ BuktiPembayaran::update()
    │   { status='ditolak', catatan=alasan, confirmed_by, confirmed_at }
    │
    └─ Mahasiswa bisa upload ulang (tidak ada pending lagi)
```

### 10.3 Alur Autentikasi Mahasiswa (API)

```
POST /api/v1/login
Body: { "nobp": "2210050", "password": "01112002" }
    │
    ├─ Cari User where mhsnobp = nobp AND role = 'mahasiswa'
    ├─ Hash::check(password, user.password)
    ├─ Validasi mahasiswa masih exist di tabel mahasiswa
    ├─ Revoke semua token lama (single-session)
    ├─ createToken('mobile-app')
    │
    └─ Response: { token, token_type, user: {nobp, nama, prodi} }

Semua endpoint protected:
    Authorization: Bearer {token}
    Middleware: auth:sanctum
```

### 10.4 Struktur Tabel Baru

#### `pengumuman`
| Field | Type | Keterangan |
|---|---|---|
| `id` | bigint PK | Auto increment |
| `judul` | varchar(200) | Judul pengumuman |
| `isi` | text | Isi (HTML dari rich editor) |
| `tgl_publish` | date nullable | Tanggal mulai tampil |
| `aktif` | tinyint(1) | 1=aktif, 0=nonaktif |
| `user_id` | bigint FK→users | Admin yang membuat |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

#### `bukti_pembayaran`
| Field | Type | Keterangan |
|---|---|---|
| `id` | bigint PK | Auto increment |
| `mhsnobp` | char(7) | FK → mahasiswa |
| `sppsem` | int | FK → sem.semid |
| `jumlah_bayar` | double | Nominal yang dibayar |
| `file_path` | varchar(255) | Path file original |
| `file_compressed` | varchar(255) | Path file setelah kompresi |
| `status` | enum | pending/dikonfirmasi/ditolak |
| `catatan` | varchar(200) | Alasan tolak |
| `confirmed_by` | bigint FK→users | Admin yang konfirmasi |
| `confirmed_at` | timestamp | Waktu konfirmasi |
| `created_at` | timestamp | Waktu upload |
| `updated_at` | timestamp | |

#### Perubahan `users` (migration tambahan)
| Field tambahan | Type | Keterangan |
|---|---|---|
| `mhsnobp` | char(7) nullable unique | Link ke mahasiswa |
| `role` | enum(admin,mahasiswa) | Akses kontrol |

### 10.5 Kompresi Gambar — Detail Teknis

File: `app/Services/ImageCompressionService.php`

- Library: **GD** (bawaan PHP, tidak perlu install extra)
- Format input: JPEG, PNG, WEBP
- Format output: selalu **JPEG** (konsistensi ukuran file)
- Resize: max lebar **1200px** (aspek rasio dipertahankan)
- Quality JPEG: **65** (estimasi ~70-80% lebih kecil dari original)
- PNG transparan: di-flatten ke background putih sebelum simpan ke JPEG
- Path storage: `storage/app/public/bukti-pembayaran/`
  - `original/` — file asli dari mahasiswa
  - `compressed/` — file hasil kompresi untuk preview admin

### 10.6 Filament Admin Panel — Navigation

```
/admin
├── Dashboard
│   ├── Widget: AccountWidget (nama admin)
│   ├── Widget: StatsOverviewWidget (5 statistik)
│   └── Widget: PembayaranPendingWidget (tabel pending full-width)
│
├── [1] Konfirmasi Pembayaran  ← UTAMA
│   ├── Tab: Semua / Pending (badge) / Dikonfirmasi / Ditolak
│   ├── Filter: Status, Semester, Prodi
│   ├── Action: Konfirmasi (approve → buat registrasi)
│   ├── Action: Tolak (isi alasan)
│   └── Action: Lihat Bukti (buka image tab baru)
│
├── [2] Pengumuman
│   ├── List + Create + Edit + Delete
│   ├── Rich text editor untuk isi
│   └── Toggle aktif/nonaktif
│
└── [3] Data Mahasiswa (Read Only)
    ├── List dengan filter prodi/angkatan/status
    └── View detail (infolist: pribadi + akademik + ortu)
```

### 10.7 Environment & Konfigurasi

```env
# DB existing kampus
DB_CONNECTION=mysql
DB_DATABASE=dbajayanusa

# Sanctum (sudah terinstall v4.3.2)
SANCTUM_STATEFUL_DOMAINS=localhost:3000,localhost:8081

# Storage (sudah dilink)
FILESYSTEM_DISK=local
# Akses publik via /storage/...
```

### 10.8 Cara Buat Admin User

```bash
php artisan make:filament-user
# Masukkan: Name, Email, Password
# Login di: http://jayanusabackend.test/admin
```

### 10.9 Cara Buat User Mahasiswa (untuk testing API)

```bash
php artisan tinker
```
```php
// Buat user mahasiswa untuk testing
\App\Models\User::create([
    'name'     => 'ADITIA NOVIRMAN',
    'email'    => 'aditia@jayanusa.test',
    'password' => bcrypt('01112002'),  // default: tgllahir ddmmyyyy
    'mhsnobp'  => '2210050',
    'role'     => 'mahasiswa',
]);
```

---

## 11. CHECKLIST STATUS IMPLEMENTASI

### Fase 1 — Setup & Core ✅
- [x] Laravel 12 fresh install
- [x] Filament 3.3 terinstall
- [x] Laravel Sanctum 4.3.2 terinstall
- [x] Route API terdaftar (`api/v1/...`) — 17 routes
- [x] `storage:link` dikonfigurasi
- [x] Migration dijalankan (7 tabel)

### Migration Status ✅
- [x] `create_users_table` — tambah kolom mhsnobp, role
- [x] `create_cache_table`
- [x] `create_jobs_table`
- [x] `create_pengumuman_table` — tabel baru
- [x] `create_bukti_pembayaran_table` — tabel baru
- [x] `add_sanctum_to_users_table`
- [x] `create_personal_access_tokens_table`

### Models ✅ (15 model)
- [x] Mahasiswa, Prodi, Agama, Jalur, Stat
- [x] Sem, Setting, SettingBiaya
- [x] MataKuliah, Kurikulum, Dosen, Ruang
- [x] Kelas, KelasJadwal, KelasAngkatan
- [x] Krs, Spp, Registrasi
- [x] Pengumuman, BuktiPembayaran
- [x] User (update: FilamentUser + HasApiTokens + role)

### API Controllers ✅ (7 controller)
- [x] AuthController — login, logout, me
- [x] ProfilController — show
- [x] KrsController — index, store, destroy
- [x] KelasController — index
- [x] NilaiController — index, summary
- [x] SppController — index, aktif, uploadBukti, statusPembayaran
- [x] PengumumanController — index, show
- [x] SemesterController — aktif

### Services ✅
- [x] ImageCompressionService — GD compress + resize

### Filament Resources ✅
- [x] BuktiPembayaranResource — list/view + aksi konfirmasi/tolak
- [x] PengumumanResource — CRUD + toggle aktif
- [x] MahasiswaResource — list/view (read only)

### Filament Widgets ✅
- [x] StatsOverviewWidget — 5 stat cards
- [x] PembayaranPendingWidget — tabel pending di dashboard

### Fase Berikutnya
- [ ] Seeder: buat admin user default + user mahasiswa test
- [ ] API testing via Postman
- [ ] Rate limiting pada endpoint login
- [ ] Ekspor laporan Excel (menggunakan maatwebsite/excel)
- [ ] Notifikasi push (FCM) saat pembayaran dikonfirmasi

---

*Update terakhir: Juli 2026 — Fase 1 & 2 selesai, siap testing*


---

## CATATAN PENTING

1. **Database existing tidak dimodifikasi** — semua tabel lama (`mahasiswa`, `krs`, `kelas`, dll) hanya dibaca via Eloquent tanpa migration destructive.

2. **Primary key non-standard** — tabel existing menggunakan nama seperti `mhsnobp`, `kelasid`, `krsid` (bukan `id`). Semua Model harus mendefinisikan `$primaryKey` secara eksplisit.

3. **Charset latin1** — tabel existing menggunakan `latin1`. Pastikan koneksi database di `config/database.php` menggunakan charset yang kompatibel atau gunakan `utf8mb4` dengan charset override per model jika perlu.

4. **Timestamps non-standard** — sebagian besar tabel existing tidak menggunakan kolom `created_at`/`updated_at` standar Laravel. Set `$timestamps = false` di model yang bersangkutan.

5. **Password mahasiswa** — perlu disepakati: apakah password disimpan di tabel `mahasiswa` atau di tabel `users` terpisah. Rekomendasi: buat kolom `password` di tabel `users` dengan `mhsnobp` sebagai identifier.

6. **Upload bukti pembayaran** — file disimpan di `storage/app/public/bukti-pembayaran/`. Jalankan `php artisan storage:link` untuk akses publik.

---

*Dokumen ini menjadi acuan pengembangan backend Halo Jayanusa. Update setiap ada perubahan arsitektur atau endpoint.*
