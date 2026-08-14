# API Documentation — Halo Jayanusa Mobile
> Backend: Laravel 12 + Sanctum   
> Format: JSON | Charset: UTF-8  
> Versi: 2.0.0 | Agustus 2026

---

## Daftar Isi

1. [Konvensi Umum](#1-konvensi-umum)
2. [Autentikasi](#2-autentikasi)
3. [Semester Aktif](#3-semester-aktif)
4. [Profil Mahasiswa](#4-profil-mahasiswa)
5. [KRS — Kartu Rencana Studi](#5-krs--kartu-rencana-studi)
6. [Daftar Kelas & Jadwal](#6-daftar-kelas--jadwal)
7. [Histori Nilai](#7-histori-nilai)
8. [SPP & Pembayaran](#8-spp--pembayaran)
9. [Pengumuman](#9-pengumuman)
10. [Kode Error & Penanganan](#10-kode-error--penanganan)
11. [Relasi Database](#11-relasi-database)
12. [Panduan Integrasi Flutter](#12-panduan-integrasi-flutter)

---

## 1. Konvensi Umum

### Base URL
```
```

### Header Wajib (semua request protected)
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

### Format Response Sukses
```json
{
  "success": true,
  "message": "Opsional",
  "data": { ... }
}
```

### Format Response Error
```json
{
  "success": false,
  "message": "Pesan error ringkas",
  "errors": {
    "field": ["detail validasi"]
  }
}
```

### Format Pagination
```json
{
  "success": true,
  "data": [ ... ],
  "meta": {
    "current_page": 1,
    "per_page": 10,
    "total": 25,
    "last_page": 3
  }
}
```

### Format Semester ID
```
YYYYS
Y = tahun akademik
S = 1 (Ganjil) | 2 (Genap)

Contoh:
20221 = Ganjil 2022/2023
20222 = Genap  2022/2023
20251 = Ganjil 2025/2026
20252 = Genap  2025/2026
```

---

## 2. Autentikasi

### POST `/login`

Login mahasiswa atau dosen. Tidak perlu token.

**Request Body**
```json
{
  "nobp": "2210050",
  "password": "01112002"
}
```

> Field name fleksibel: `nobp`, `nim`, `nidn`, `noBP`, atau `username` — semua diterima.  
> Password default mahasiswa: tanggal lahir format `ddmmyyyy`  
> Password default dosen: `password123`

| Field | Tipe | Wajib | Keterangan |
|---|---|---|---|
| `nobp` | string | ✅ | NoBP mahasiswa (7 digit) atau dosenid/nidn |
| `password` | string | ✅ | Password akun |

**Response 200 — Mahasiswa**
```json
{
  "success": true,
  "message": "Login berhasil.",
  "data": {
    "token": "25|vskq4mEjASmRSH...",
    "token_type": "Bearer",
    "user": {
      "nobp": "2210050",
      "nama": "ADITIA NOVIRMAN",
      "prodi": "Sistem Informasi",
      "role": "mahasiswa"
    }
  }
}
```

**Response 200 — Dosen**
```json
{
  "success": true,
  "message": "Login berhasil.",
  "data": {
    "token": "26|...",
    "token_type": "Bearer",
    "user": {
      "nobp": "DSN001",
      "nama": "Mike Febri Mayang Sari, M.Kom",
      "prodi": null,
      "role": "dosen",
      "nidn": "1234567890"
    }
  }
}
```

**Response 422 — Gagal**
```json
{
  "success": false,
  "message": "NoBP atau password salah.",
  "errors": {
    "nobp": ["NoBP atau password salah."]
  }
}
```

> Simpan `token` di SharedPreferences. Gunakan sebagai Bearer di semua request berikutnya.

---

### POST `/logout`

Revoke token aktif. 🔒 Protected.

**Response 200**
```json
{
  "success": true,
  "message": "Logout berhasil."
}
```

---

### GET `/me`

Info user yang sedang login. 🔒 Protected.

**Response 200 — Mahasiswa**
```json
{
  "success": true,
  "data": {
    "nobp": "2210050",
    "email": "aditia@jayanusa.ac.id",
    "nama": "ADITIA NOVIRMAN",
    "prodi": "Sistem Informasi",
    "status": "Aktif",
    "role": "mahasiswa"
  }
}
```

---

## 3. Semester Aktif

### GET `/semester/aktif`

Info semester yang sedang aktif. **Public** — tidak perlu token.

**Response 200**
```json
{
  "success": true,
  "data": {
    "sem_id": 20252,
    "nama": "Genap 2025/2026",
    "mulai": "01 Februari 2026",
    "selesai": "31 Juli 2026",
    "krs_mulai": "20 Januari 2026 00:00",
    "krs_selesai": "10 Februari 2026 23:59",
    "krs_open": false
  }
}
```

| Field | Tipe | Keterangan |
|---|---|---|
| `sem_id` | integer | Format YYYYS |
| `krs_open` | boolean | `true` = periode KRS sedang dibuka |

**Response 404**
```json
{
  "success": false,
  "message": "Tidak ada semester aktif saat ini."
}
```

---

## 4. Profil Mahasiswa

### GET `/profil`

Profil lengkap mahasiswa beserta info biaya kuliah. 🔒 Protected.

**Response 200**
```json
{
  "success": true,
  "data": {
    "no_bp": "2210050",
    "nik": "1301070110020003",
    "nama": "ADITIA NOVIRMAN",
    "email": "aditia@jayanusa.ac.id",

    "prodi": "Sistem Informasi",
    "angkatan": 2022,
    "tahun_kurikulum": 2022,
    "semester_awal_masuk": 20221,
    "tahun_ke": 4,
    "dosen_pa": "Mike Febri Mayang Sari, M.Kom",
    "status": "Aktif",

    "tempat_lahir": "Koto Pulai",
    "tanggal_lahir": "01 November 2002",
    "jenis_kelamin": "Laki-Laki",
    "alamat": "KOTO PULAI",
    "kelurahan": null,
    "kecamatan": null,
    "telp": "089517647957",
    "agama": "Islam",
    "asal_sekolah": null,

    "nama_ayah": "WAZARIATI ZARWAN",
    "nama_ibu": "YURNIDA",

    "jalur": "Reguler",
    "kelas_biaya": "Reguler",
    "kelas_kode": "R",

    "biaya": {
      "spp_penuh": 3500000,
      "cicilan_1": 1750000,
      "cicilan_2": 1750000,
      "pembangunan": null,
      "orientasi": null,
      "tahun_ke": 4
    }
  }
}
```

| Field `biaya` | Tipe | Keterangan |
|---|---|---|
| `spp_penuh` | float\|null | Biaya SPP per semester jika bayar penuh |
| `cicilan_1` | float\|null | Nominal cicilan pertama (biasanya 50%) |
| `cicilan_2` | float\|null | Nominal cicilan kedua (pelunasan) |
| `pembangunan` | float\|null | Uang pembangunan (tergantung tahun ke berapa, null jika tidak ada) |
| `orientasi` | float\|null | Biaya orientasi/PKKMB (biasanya hanya tahun 1) |
| `tahun_ke` | integer | Tahun ke berapa mahasiswa saat ini (1–4) |

> Sumber data biaya: tabel `settingbiaya` — dicocokkan berdasarkan `prodi + angkatan + kelas (R/E/K)`

---

## 5. KRS — Kartu Rencana Studi

### GET `/krs`

Lihat daftar KRS mahasiswa. 🔒 Protected.

**Query Parameters**

| Parameter | Tipe | Default | Keterangan |
|---|---|---|---|
| `sem_id` | integer | Semester aktif | ID semester yang ingin dilihat |

**Contoh**
```
GET /api/v1/krs
GET /api/v1/krs?sem_id=20251
```

**Response 200**
```json
{
  "success": true,
  "data": {
    "semester": "Genap 2025/2026",
    "sem_id": 20252,
    "total_sks": 6,
    "max_sks": 24,
    "dosen_pa": "Mike Febri Mayang Sari, M.Kom",
    "bisa_isi_krs": true,
    "pesan_pembayaran": null,
    "items": [
        {
        "krs_id": 238100,
        "kelas_id": 5001,
        "kode_kelas": "20251-1-142",
        "label_kelas": "A",
        "kode_mk": "MKB107226",
        "nama_mk": "PRAKTEK KERJA LAPANGAN",
        "sks": 2,
        "semester_mk": 7,
        "dosen": "Mike Febri Mayang Sari, M.Kom",
        "hari": "Minggu",
        "jam_mulai": "09:30",
        "jam_selesai": "11:10",
        "jam_label": "09:30 – 11:10",
        "ruangan": "Lokal 3",
        "jadwal": [
          {
            "hari": "Minggu",
            "jam_mulai": "09:30",
            "jam_selesai": "11:10",
            "jam_label": "09:30 – 11:10",
            "ruangan": "Lokal 3",
            "dosen": "Mike Febri Mayang Sari, M.Kom"
          }
        ],
        "status_krs": "Approved",
        "nilai": "A",
        "bobot": 4,
        "keterangan": "Lulus",
        "jumlah_absen": 0
      }
    ]
  }
}
```

| Field | Tipe | Keterangan |
|---|---|---|
| `bisa_isi_krs` | boolean | `true` jika sudah bayar (cicilan 1 pun cukup) |
| `pesan_pembayaran` | string\|null | Pesan jika belum bayar, null jika sudah |
| `status_krs` | string | `Approved` / `Pending` / `Dibatalkan` |
| `nilai` | string\|null | A/B/C/D/E, null = belum ada nilai |
| `keterangan` | string | `Lulus` (A-C) / `Gagal` (D-E) / `-` |
| `jam_awal` | integer\|null | ID jam mengajar (bukan format HH:mm) |
| `jadwal` | array | Semua jadwal kelas (bisa lebih dari 1) |

---

### POST `/krs`

Tambah KRS — ambil kelas baru. 🔒 Protected.

**Syarat:**
1. Periode KRS sedang terbuka (`krs_open = true`)
2. Sudah bayar SPP semester ini (cicilan 1 sudah dikonfirmasi admin)
3. Kelas belum penuh
4. Belum mengambil kelas yang sama
5. Total SKS tidak melebihi 24

**Request Body**
```json
{
  "kelas_id": 5001
}
```

**Response 201**
```json
{
  "success": true,
  "message": "KRS berhasil ditambahkan. Menunggu persetujuan Dosen PA."
}
```

**Response 422 — Kemungkinan error:**
```json
{ "success": false, "message": "Periode pengisian KRS belum dibuka atau sudah tutup." }
{ "success": false, "message": "Kelas sudah penuh." }
{ "success": false, "message": "Anda sudah mengambil kelas ini." }
{ "success": false, "message": "Total SKS melebihi batas maksimal 24 SKS. SKS saat ini: 22, SKS kelas ini: 4." }
{ "success": false, "message": "Anda belum melakukan pembayaran SPP semester ini." }
```

---

### DELETE `/krs/{id}`

Batalkan KRS. Hanya saat periode KRS terbuka. 🔒 Protected.

```
DELETE /api/v1/krs/238100
```

**Response 200**
```json
{
  "success": true,
  "message": "KRS berhasil dibatalkan."
}
```

---

## 6. Daftar Kelas & Jadwal

### GET `/kelas`

Daftar kelas tersedia untuk semester aktif, difilter otomatis berdasarkan prodi dan angkatan mahasiswa. 🔒 Protected.

**Query Parameters**

| Parameter | Tipe | Keterangan |
|---|---|---|
| `semester` | integer | Filter semester MK ke-berapa (1–8) |
| `sem_id` | integer | Override semester (default: semester aktif) |

**Contoh**
```
GET /api/v1/kelas
GET /api/v1/kelas?semester=7
GET /api/v1/kelas?semester=3&sem_id=20252
```

**Response 200**
```json
{
  "success": true,
  "data": [
    {
      "semester_mk": 7,
      "kelas": [
        {
          "kelas_id": 5001,
          "kode_kelas": "20252-1-005",
          "label_kelas": "A",
          "kode_mk": "MKB107226",
          "nama_mk": "PRAKTEK KERJA LAPANGAN",
          "nama_mk_inggris": "Field Work Practice",
          "sks": 2,
          "semester_mk": 7,
          "kapasitas": 30,
          "jumlah_mahasiswa": 19,
          "is_penuh": false,
          "dosen": "Mike Febri Mayang Sari, M.Kom",
          "hari": "Minggu",
          "jam_mulai": "09:30",
          "jam_selesai": "11:10",
          "jam_label": "09:30 – 11:10",
          "ruangan": "Lokal 3",
          "jadwal": [
            {
              "hari": "Minggu",
              "jam_mulai": "09:30",
              "jam_selesai": "11:10",
              "jam_label": "09:30 – 11:10",
              "ruangan": "Lokal 3",
              "dosen": "Mike Febri Mayang Sari, M.Kom"
            }
          ],
          "keterangan": null,
          "prasyarat": []
        }
      ]
    }
  ]
}
```

| Field | Tipe | Keterangan |
|---|---|---|
| `kelas_id` | integer | Gunakan ini untuk POST `/krs` |
| `semester_mk` | integer | Semester MK di kurikulum (1–8) |
| `is_penuh` | boolean | `true` = tidak bisa diambil lagi |
| `jam_mulai` | string | Format `HH:mm`, contoh: `09:30` |
| `jam_selesai` | string | Format `HH:mm`, contoh: `11:10` |
| `jam_label` | string | Siap tampil: `"09:30 – 11:10"` |
| `jadwal` | array | Semua jadwal (bisa lebih dari 1 per minggu) |
| `prasyarat` | array | Kode MK prasyarat (bisa kosong `[]`) |

> **Tabel konversi jam:** ID jam 1 = 07:00, setiap sesi 50 menit.
> Backend sudah konversi otomatis ke `HH:mm` — React Native tinggal tampilkan `jam_label`.

---

## 7. Histori Nilai

### GET `/nilai`

Histori nilai lengkap semua semester, dikelompokkan per semester. 🔒 Protected.

**Response 200**
```json
{
  "success": true,
  "data": {
    "no_bp": "2210050",
    "nama": "ADITIA NOVIRMAN",
    "prodi": "Sistem Informasi",
    "total_sks": 20,
    "ipk": 3.60,
    "semesters": [
      {
        "sem_id": 20221,
        "semester": "Ganjil 2022/2023",
        "total_sks": 18,
        "total_mutu": 64,
        "ip": 3.56,
        "matakuliah": [
          {
            "krs_id": 100001,
            "kode_kelas": "20221-1-001",
            "kode_mk": "MKB101101",
            "nama_mk": "PRAKTIKUM ALGORITMA DAN STRUKTUR DATA 1",
            "sks": 1,
            "nilai": "A",
            "bobot": 4,
            "mutu": 4,
            "keterangan": "Lulus",
            "dosen": "Ahmad Fikri Fajri, S.Kom, M.Kom",
            "jumlah_absen": 0
          }
        ]
      }
    ]
  }
}
```

| Field `matakuliah` | Tipe | Keterangan |
|---|---|---|
| `nilai` | string | A / B / C / D / E |
| `bobot` | integer | A=4, B=3, C=2, D=1, E=0 |
| `mutu` | integer | SKS × Bobot |
| `keterangan` | string | `Lulus` (A-C) atau `Gagal` (D-E) |
| `jumlah_absen` | integer | Jumlah ketidakhadiran |

**Tabel Konversi Nilai**
| Nilai | Bobot | Status |
|---|---|---|
| A | 4 | Lulus |
| B | 3 | Lulus |
| C | 2 | Lulus |
| D | 1 | Gagal |
| E | 0 | Gagal |

---

### GET `/nilai/summary`

Ringkasan IP per semester + IPK. Cocok untuk tampilan chart/grafik. 🔒 Protected.

**Response 200**
```json
{
  "success": true,
  "data": {
    "total_sks_lulus": 20,
    "ipk": 3.60,
    "per_semester": [
      {
        "sem_id": 20221,
        "semester": "Ganjil 2022/2023",
        "total_sks": 18,
        "ip": 3.56
      },
      {
        "sem_id": 20222,
        "semester": "Genap 2022/2023",
        "total_sks": 2,
        "ip": 4.0
      }
    ]
  }
}
```

---

## 8. SPP & Pembayaran

### Struktur Biaya Kuliah

Biaya diambil dari tabel `settingbiaya` berdasarkan kombinasi **prodi + angkatan + kelas (R/E/K)**:

| Field DB | Keterangan |
|---|---|
| `biaya` | SPP penuh per semester |
| `biaya1` | Cicilan 1 (biasanya 50%) |
| `biaya2` | Cicilan 2 / pelunasan |
| `pembangunan1–4` | Uang gedung/pembangunan per tahun |
| `orientasi` | Biaya PKKMB / orientasi (tahun 1) |

**Kelas Biaya (`mhskel`)**
| Kode | Nama | Keterangan |
|---|---|---|
| `R` | Reguler | Biaya normal |
| `E` | Reguler Malam | Biaya normal (kelas malam) |
| `K` | KIP Kuliah | Gratis SPP, hanya uang kemahasiswaan |

---

### GET `/spp`

Semua tagihan SPP mahasiswa seluruh semester. 🔒 Protected.

**Response 200**
```json
{
  "success": true,
  "data": [
    {
      "spp_id": 1,
      "sem_id": 20252,
      "semester": "Genap 2025/2026",
      "tagihan": 3500000,
      "cicilan_info": {
        "biaya_penuh": 3500000,
        "cicilan_1": 1750000,
        "cicilan_2": 1750000
      },
      "status_lunas": "Lunas",
      "status_cicilan": "lunas",
      "tanggal_bayar": "05 Februari 2026",
      "no_bukti": "JN/2026/02/000001",
      "jumlah_bayar": 3500000,
      "total_dikonfirmasi": 3500000,
      "sisa_tagihan": 0,
      "status_upload": "dikonfirmasi",
      "tipe_upload": "penuh"
    }
  ]
}
```

| Field | Tipe | Keterangan |
|---|---|---|
| `tagihan` | float | Nominal tagihan dari tabel `spp.spptagihan` |
| `cicilan_info` | object\|null | Info cicilan dari `settingbiaya` |
| `status_lunas` | string | `Lunas` / `Belum Lunas` |
| `status_cicilan` | string | `lunas` / `cicilan1` / `pending` / `belum` |
| `sisa_tagihan` | float | Sisa yang belum dibayar |
| `status_upload` | string\|null | Status bukti terakhir |
| `tipe_upload` | string\|null | Tipe upload terakhir |

**Nilai `status_cicilan`**
| Nilai | Artinya |
|---|---|
| `lunas` | Sudah lunas penuh (ada `registrasi` sungguhan, bukan AUTO) |
| `cicilan1` | Cicilan 1 dikonfirmasi admin, menunggu cicilan 2/pelunasan |
| `pending` | Ada upload sedang menunggu konfirmasi admin |
| `belum` | Belum ada pembayaran sama sekali |

> **Catatan:** Registrasi dengan `regnobukti` berformat `AUTO-*` atau `regjumlahbayar = 0`
> adalah registrasi sementara yang dibuat otomatis saat mahasiswa isi KRS.
> Ini **bukan** tanda sudah lunas. Status lunas hanya dari registrasi yang dibuat admin.

---

### GET `/spp/aktif`

Tagihan SPP semester yang sedang aktif saja. 🔒 Protected.

**Response 200** — sama struktur dengan satu item dari GET `/spp`

**Response 404**
```json
{
  "success": false,
  "message": "Data tagihan semester aktif tidak ditemukan."
}
```

---

### POST `/spp/upload`

Upload bukti pembayaran SPP. File dikompresi otomatis oleh server. 🔒 Protected.

> Content-Type: `multipart/form-data`

**Request Form Data**

| Field | Tipe | Wajib | Keterangan |
|---|---|---|---|
| `file` | image | ✅ | Format: jpg/jpeg/png/webp, maks 5MB |
| `jumlah_bayar` | number | ✅ | Nominal yang dibayarkan (Rupiah) |
| `sem_id` | integer | ❌ | Default: semester aktif |
| `tipe_bayar` | string | ❌ | `penuh` / `cicilan1` / `cicilan2` / `cicilan3`. Default: `penuh` |

**Aturan Cicilan:**
- `cicilan2` hanya bisa diupload jika `cicilan1` sudah dikonfirmasi admin
- Satu tipe cicilan hanya bisa ada satu yang `pending` sekaligus
- Tidak bisa upload jika semester sudah `Lunas`

**Response 201**
```json
{
  "success": true,
  "message": "Bukti pembayaran berhasil diupload. Menunggu konfirmasi admin."
}
```

**Response 422 — Kemungkinan error:**
```json
{ "success": false, "message": "Tagihan SPP tidak ditemukan untuk semester ini." }
{ "success": false, "message": "Pembayaran semester ini sudah dikonfirmasi (Lunas)." }
{ "success": false, "message": "Sudah ada bukti pembayaran penuh yang sedang menunggu konfirmasi admin." }
{ "success": false, "message": "Cicilan 2 hanya dapat dibayarkan setelah Cicilan 1 dikonfirmasi oleh admin." }
```

**Contoh Flutter/Dart**
```dart
var request = http.MultipartRequest(
  'POST',
  Uri.parse('$baseUrl/spp/upload'),
);
request.headers['Authorization'] = 'Bearer $token';
request.headers['Accept'] = 'application/json';
request.fields['jumlah_bayar'] = '1750000';
request.fields['tipe_bayar'] = 'cicilan1';
request.files.add(await http.MultipartFile.fromPath('file', imagePath));
var response = await request.send();
```

---

### GET `/spp/{semId}/status`

Status detail pembayaran untuk semester tertentu. 🔒 Protected.

```
GET /api/v1/spp/20252/status
```

**Response 200**
```json
{
  "success": true,
  "data": {
    "sem_id": 20252,
    "semester": "Genap 2025/2026",
    "status_lunas": "Lunas",
    "tanggal_bayar": "05 Februari 2026",
    "no_bukti": "JN/2026/02/000001",
    "jumlah_bayar": 3500000,
    "tagihan_total": 3500000,
    "total_dikonfirmasi": 3500000,
    "sisa_tagihan": 0,
    "upload_terakhir": {
      "id": 7,
      "status": "dikonfirmasi",
      "jumlah": 3500000,
      "catatan": null,
      "file_url": "http://jayanusabackend.test/storage/bukti-pembayaran/compressed/uuid.jpg",
      "uploaded_at": "03 Februari 2026 14:00",
      "confirmed_at": "05 Februari 2026 09:00"
    }
  }
}
```

| Field `upload_terakhir` | Keterangan |
|---|---|
| `status` | `pending` / `dikonfirmasi` / `ditolak` |
| `catatan` | Alasan penolakan jika `ditolak`, null jika tidak |
| `file_url` | URL preview bukti (versi compressed) |

---

## 9. Pengumuman

### GET `/pengumuman`

Daftar pengumuman aktif, terbaru di atas. 🔒 Protected.

**Query Parameters**

| Parameter | Tipe | Default | Keterangan |
|---|---|---|---|
| `page` | integer | 1 | Nomor halaman |

**Response 200**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "judul": "Pengisian KRS Semester Genap 2025/2026",
      "isi": "<p>Mahasiswa diwajibkan mengisi KRS...</p>",
      "tgl_publish": "2026-01-15",
      "aktif": true,
      "created_at": "2026-01-14T10:00:00.000000Z",
      "updated_at": "2026-01-14T10:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 10,
    "total": 4,
    "last_page": 1
  }
}
```

> ⚠️ Field `isi` berisi HTML. Gunakan `flutter_html` atau `WebView` untuk render.

---

### GET `/pengumuman/{id}`

Detail satu pengumuman. 🔒 Protected.

```
GET /api/v1/pengumuman/1
```

**Response 200**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "judul": "Pengisian KRS Semester Genap 2025/2026",
    "isi": "<p>Mahasiswa diwajibkan mengisi KRS mulai tanggal <strong>20 Januari 2026</strong>...</p>",
    "tgl_publish": "15 Januari 2026",
    "created_at": "14 Januari 2026 10:00"
  }
}
```

---

### POST `/pengumuman`

Buat pengumuman baru (untuk dosen/admin via mobile). 🔒 Protected.

**Request Body**
```json
{
  "judul": "Jadwal UAS Semester Genap",
  "isi": "<p>UAS dilaksanakan mulai tanggal 1 Juli 2026...</p>"
}
```

**Response 201**
```json
{
  "success": true,
  "message": "Pengumuman berhasil dipublikasikan.",
  "data": {
    "id": 5,
    "judul": "Jadwal UAS Semester Genap",
    "isi": "<p>UAS dilaksanakan mulai tanggal 1 Juli 2026...</p>",
    "tgl_publish": "14 Agustus 2026"
  }
}
```

---

## 10. Kode Error & Penanganan

### HTTP Status Codes

| Kode | Arti | Tindakan di Flutter |
|---|---|---|
| `200` | OK | Tampilkan data |
| `201` | Created | Tampilkan pesan sukses |
| `401` | Unauthorized | Hapus token → redirect ke login |
| `404` | Not Found | Tampilkan pesan "data tidak ditemukan" |
| `422` | Validation Error | Tampilkan pesan dari `message` |
| `500` | Server Error | Tampilkan pesan error umum |

### Response 401
```json
{
  "success": false,
  "message": "Sesi habis atau tidak terautentikasi. Silakan login kembali."
}
```

### Response 422
```json
{
  "success": false,
  "message": "NoBP atau password salah.",
  "errors": {
    "nobp": ["NoBP atau password salah."]
  }
}
```

---

## 11. Relasi Database

### Alur Data Pembayaran
```
mahasiswa
  ├── mhskel (R/E/K)
  ├── mhsangkatan
  └── mhsprodiid
         │
         ▼
settingbiaya (prodi + angkatan + kelas)
  ├── biaya        → SPP penuh per semester
  ├── biaya1       → Cicilan 1
  ├── biaya2       → Cicilan 2
  └── pembangunan1-4, orientasi

spp (tagihan per mahasiswa per semester)
  ├── sppmhsnobp → mahasiswa
  ├── sppsem     → sem
  └── spptagihan → nominal tagihan aktual

bukti_pembayaran (upload dari mahasiswa)
  ├── mhsnobp  → mahasiswa
  ├── sppsem   → sem
  ├── status   → pending | dikonfirmasi | ditolak
  └── tipe_bayar → penuh | cicilan1 | cicilan2 | cicilan3

registrasi (tanda LUNAS — dibuat admin setelah konfirmasi)
  ├── regmhsnobp → mahasiswa
  ├── regsem     → sem
  └── regjumlahbayar, regtanggalbayar, regnobukti
```

### Alur Data KRS & Nilai
```
mahasiswa
  └── krsList (krs)
        ├── krskelasid → kelas
        │     ├── kelaskurid → kurikulum
        │     │     └── kurmtkid → matakuliah
        │     │           ├── mtknama, mtksks
        │     │           └── prasyarat (kurmtkidprasyarat 1-3)
        │     ├── jadwalList (kelasjadwal)
        │     │     ├── jadwaldosenid → dosen (nama_lengkap)
        │     │     ├── jadwalruangid → ruang (runama)
        │     │     ├── jadwalhari
        │     │     ├── jadwaljamidawal
        │     │     └── jadwaljamidakhir
        │     └── angkatanList (kelasangkatan)
        │           └── kelasangangkatan → filter per angkatan
        ├── krsnilai (A/B/C/D/E)
        ├── krsbobot (4/3/2/1/0)
        ├── krsapproved (0/1)
        └── krshapus (0/1 soft delete)
```

---

## 12. Panduan Integrasi Flutter

### Simpan & Ambil Token
```dart
// Simpan setelah login
final prefs = await SharedPreferences.getInstance();
await prefs.setString('token', token);
await prefs.setString('role', role);
await prefs.setString('nobp', nobp);

// Ambil token
final token = prefs.getString('token') ?? '';
```

### Interceptor HTTP (Dio)
```dart
dio.options.headers = {
  'Authorization': 'Bearer $token',
  'Accept': 'application/json',
};

// Handle 401 — auto logout
dio.interceptors.add(InterceptorsWrapper(
  onError: (e, handler) async {
    if (e.response?.statusCode == 401) {
      await prefs.clear();
      Get.offAllNamed('/login'); // atau Navigator.pushReplacementNamed
    }
    return handler.next(e);
  },
));
```

### Urutan Alur Normal di Aplikasi
```
1.  POST /login                     → dapat token + info user
2.  GET  /me                        → verifikasi token + info lengkap
3.  GET  /semester/aktif            → cek semester & status KRS
4.  GET  /profil                    → tampilkan profil + biaya kuliah
5.  GET  /spp/aktif                 → cek tagihan semester ini
6.  GET  /spp/{semId}/status        → status pembayaran detail
    (jika belum lunas)
7.  POST /spp/upload                → upload bukti pembayaran
    (setelah dikonfirmasi admin)
8.  GET  /krs                       → lihat KRS aktif
9.  GET  /kelas?semester=X          → lihat kelas tersedia
10. POST /krs { kelas_id }          → ambil kelas
11. GET  /nilai                     → histori nilai lengkap
12. GET  /nilai/summary             → IP & IPK untuk chart
13. GET  /pengumuman                → daftar pengumuman
14. POST /logout                    → logout
```

### Data Testing
```
Base URL  : http://jayanusabackend.test/api/v1
NoBP      : 2210050
Password  : 01112002
Role      : mahasiswa
Semester  : 20252 (Genap 2025/2026)

Dosen     : DSN001 (alias: D001)
Password  : password123

Admin     : admin@jayanusa.ac.id
Password  : admin123
```

### Status Data Testing (Mahasiswa 2210050)

| Item | Status | Keterangan |
|---|---|---|
| Semester aktif | 20252 (Genap 2025/2026) | `krs_open = true` |
| KRS buka | s/d 31 Des 2026 | Terbuka lebar untuk testing |
| SPP Sem 1–7 | ✅ Lunas | Ada registrasi + bukti dikonfirmasi |
| SPP Sem 8 (20252) | ❌ Belum Lunas | Untuk test fitur upload bukti |
| KRS Sem 1–7 | ✅ Lengkap + nilai | A/B/C per MK |
| KRS Sem 8 | ✅ 3 MK aktif | Skripsi, Pilihan 2, Seminar Hasil |
| IPK | 3.57 | Dari 7 semester, 118 SKS |
| Biaya cicilan | cicilan_1=Rp1.750.000 | Dari settingbiaya prodi+angkatan+kelas |

### Kurikulum SI 2022 (Mahasiswa 2210050)

| Sem | Semester | Jumlah MK | SKS |
|---|---|---|---|
| 1 | Ganjil 2022/2023 (20221) | 7 MK | 17 SKS |
| 2 | Genap 2022/2023 (20222) | 7 MK | 16 SKS |
| 3 | Ganjil 2023/2024 (20231) | 7 MK | 16 SKS |
| 4 | Genap 2023/2024 (20232) | 7 MK | 17 SKS |
| 5 | Ganjil 2024/2025 (20241) | 7 MK | 17 SKS |
| 6 | Genap 2024/2025 (20242) | 7 MK | 17 SKS |
| 7 | Ganjil 2025/2026 (20251) | 6 MK | 16 SKS |
| 8 | **Genap 2025/2026 (20252)** | 3 MK | 11 SKS |

### Ruang yang Tersedia
| ID | Nama |
|---|---|
| 1–4 | Lokal 1–4 |
| 5–6 | Lab Komputer 1–2 |
| 7 | Lab Jaringan |
| 8 | Lab Multimedia |
| 9 | Aula |
| 10 | Lokal 5 |

---

*Dokumentasi ini disusun berdasarkan struktur database kampus `dbajayanusa` dan implementasi controller yang ada.*  
*Update: Agustus 2026 — v2.0 (perbaikan biaya cicilan, jadwal lengkap, relasi database)*
