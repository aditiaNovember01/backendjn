# API Documentation — Halo Jayanusa Mobile
> Backend: Laravel 12 + Sanctum  
> Base URL: `http://127.0.0.1:8000/api/v1`  
> Format: JSON  
> Versi: 1.0.0 | Juli 2026

---

## Daftar Isi

1. [Konvensi Umum](#konvensi-umum)
2. [Autentikasi](#autentikasi)
3. [Semester](#semester)
4. [Profil Mahasiswa](#profil-mahasiswa)
5. [KRS](#krs)
6. [Daftar Kelas](#daftar-kelas)
7. [Histori Nilai](#histori-nilai)
8. [SPP / Pembayaran](#spp--pembayaran)
9. [Pengumuman](#pengumuman)
10. [Kode Error](#kode-error)

---

## Konvensi Umum

### Base URL
```
http://127.0.0.1:8000/api/v1
```
> Untuk production ganti dengan domain server.

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
  "message": "Deskripsi opsional",
  "data": { ... }
}
```

### Format Response Error
```json
{
  "success": false,
  "message": "Pesan error",
  "errors": {
    "field": ["detail error"]
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

---

## Autentikasi

### POST `/login`

Login mahasiswa menggunakan NoBP dan password.

> **Public** — tidak perlu token

**Request Body**
```json
{
  "nobp": "2210050",
  "password": "01112002"
}
```

| Field | Tipe | Wajib | Keterangan |
|---|---|---|---|
| `nobp` | string | ✅ | Nomor Buku Pokok (maks 7 karakter) |
| `password` | string | ✅ | Default: tanggal lahir format `ddmmyyyy` |

**Response 200 — Berhasil**
```json
{
  "success": true,
  "message": "Login berhasil.",
  "data": {
    "token": "1|abcdefghijklmnopqrstuvwxyz123456",
    "token_type": "Bearer",
    "user": {
      "nobp": "2210050",
      "nama": "ADITIA NOVIRMAN",
      "prodi": "Sistem Informasi"
    }
  }
}
```

**Response 422 — Gagal**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "nobp": ["NoBP atau password salah."]
  }
}
```

> 💡 Simpan `token` di local storage app. Gunakan sebagai Bearer token di semua request selanjutnya.

---

### POST `/logout`

Logout dan hapus token aktif.

> **🔒 Protected**

**Request**: tidak perlu body

**Response 200**
```json
{
  "success": true,
  "message": "Logout berhasil."
}
```

---

### GET `/me`

Info user yang sedang login.

> **🔒 Protected**

**Response 200**
```json
{
  "success": true,
  "data": {
    "nobp": "2210050",
    "email": "aditia@jayanusa.ac.id",
    "nama": "ADITIA NOVIRMAN",
    "prodi": "Sistem Informasi",
    "status": "Aktif"
  }
}
```

---

## Semester

### GET `/semester/aktif`

Info semester akademik yang sedang aktif.

> **Public** — tidak perlu token

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
| `sem_id` | integer | Format YYYYS (20251=Ganjil 2025, 20252=Genap 2025) |
| `krs_open` | boolean | `true` jika periode KRS sedang buka |

**Response 404**
```json
{
  "success": false,
  "message": "Tidak ada semester aktif saat ini."
}
```

---

## Profil Mahasiswa

### GET `/profil`

Lihat profil lengkap mahasiswa yang sedang login. **Read only** dari mobile.

> **🔒 Protected**

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
    "tahun_masuk": 2022,
    "semester_awal_masuk": 20221,
    "tempat_lahir": "Koto Pulai",
    "tanggal_lahir": "01 November 2002",
    "jenis_kelamin": "Laki-Laki",
    "alamat": "KOTO PULAI",
    "telp": "089517647957",
    "agama": "Islam",
    "nama_ayah": "WAZARIATI ZARWAN",
    "nama_ibu": "YURNIDA",
    "jalur": "Reguler",
    "kelas_biaya": "Reguler",
    "biaya_kuliah": 3500000,
    "status": "Aktif"
  }
}
```

| Field | Tipe | Keterangan |
|---|---|---|
| `no_bp` | string | Nomor Buku Pokok / NIM |
| `semester_awal_masuk` | integer | Format YYYYS |
| `kelas_biaya` | string | Reguler / Reguler Malam / KIP Kuliah |
| `biaya_kuliah` | number | SPP per semester dalam Rupiah |

---

## KRS

### GET `/krs`

Lihat daftar KRS mahasiswa semester aktif.

> **🔒 Protected**

**Query Parameters**

| Parameter | Tipe | Wajib | Keterangan |
|---|---|---|---|
| `sem_id` | integer | ❌ | ID semester (default: semester aktif) |

**Contoh Request**
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
    "total_sks": 2,
    "max_sks": 24,
    "items": [
      {
        "krs_id": 1,
        "kode_kelas": "20252-1-005 (A)",
        "kode_mk": "MKB107226",
        "nama_mk": "PRAKTEK KERJA LAPANGAN",
        "sks": 2,
        "dosen": "Mike Febri Mayang Sari, M.Kom",
        "hari": "Minggu",
        "ruangan": "Lokal 1",
        "status_krs": "Approved",
        "nilai": "A",
        "bobot": 4,
        "keterangan": "Lulus"
      }
    ]
  }
}
```

| Field `items` | Tipe | Keterangan |
|---|---|---|
| `status_krs` | string | `Approved` / `Pending` / `Dibatalkan` |
| `nilai` | string\|null | A / B / C / D / E, null jika belum ada nilai |
| `bobot` | integer | A=4, B=3, C=2, D=1, E=0 |
| `keterangan` | string | `Lulus` / `Gagal` / `-` |

---

### POST `/krs`

Tambah KRS — ambil kelas baru.

> **🔒 Protected**

**Request Body**
```json
{
  "kelas_id": 1
}
```

| Field | Tipe | Wajib | Keterangan |
|---|---|---|---|
| `kelas_id` | integer | ✅ | ID kelas dari endpoint `/kelas` |

**Response 201 — Berhasil**
```json
{
  "success": true,
  "message": "KRS berhasil ditambahkan. Menunggu persetujuan Dosen PA."
}
```

**Response 422 — Validasi Gagal**
```json
{
  "success": false,
  "message": "Periode pengisian KRS belum dibuka atau sudah tutup."
}
```

**Kemungkinan pesan error 422:**
- `Periode pengisian KRS belum dibuka atau sudah tutup.`
- `Kelas sudah penuh.`
- `Anda sudah mengambil kelas ini.`
- `Total SKS melebihi batas maksimal 24 SKS. SKS saat ini: X, SKS kelas ini: Y.`
- `Anda belum melakukan pembayaran semester ini. KRS tidak dapat diambil.`

---

### DELETE `/krs/{id}`

Batalkan KRS. Hanya bisa dilakukan saat periode KRS masih buka.

> **🔒 Protected**

**Path Parameter**

| Parameter | Tipe | Keterangan |
|---|---|---|
| `id` | integer | `krs_id` dari response GET `/krs` |

**Contoh Request**
```
DELETE /api/v1/krs/1
```

**Response 200**
```json
{
  "success": true,
  "message": "KRS berhasil dibatalkan."
}
```

**Response 422**
```json
{
  "success": false,
  "message": "Periode KRS sudah tutup. Tidak bisa membatalkan KRS."
}
```

---

## Daftar Kelas

### GET `/kelas`

Daftar kelas yang tersedia untuk semester aktif, sesuai prodi dan angkatan mahasiswa yang login.

> **🔒 Protected**

**Query Parameters**

| Parameter | Tipe | Wajib | Keterangan |
|---|---|---|---|
| `semester` | integer | ❌ | Filter semester MK ke-berapa (1–8) |

**Contoh Request**
```
GET /api/v1/kelas
GET /api/v1/kelas?semester=7
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
          "kelas_id": 1,
          "kode_kelas": "20252-1-005 [A]",
          "kode_mk": "MKB107226",
          "nama_mk": "PRAKTEK KERJA LAPANGAN",
          "sks": 2,
          "semester_mk": 7,
          "kapasitas": 30,
          "jumlah_mahasiswa": 1,
          "is_penuh": false,
          "dosen": "Mike Febri Mayang Sari, M.Kom",
          "hari": "Minggu",
          "ruangan": "Lokal 1",
          "keterangan": null
        }
      ]
    }
  ]
}
```

| Field | Tipe | Keterangan |
|---|---|---|
| `semester_mk` | integer | Semester mata kuliah di kurikulum (1–8) |
| `kelas_id` | integer | Gunakan nilai ini untuk POST `/krs` |
| `is_penuh` | boolean | `true` jika `jumlah_mahasiswa >= kapasitas` |

---

## Histori Nilai

### GET `/nilai`

Histori nilai lengkap semua semester, dikelompokkan per semester.

> **🔒 Protected**

**Response 200**
```json
{
  "success": true,
  "data": {
    "no_bp": "2210050",
    "nama": "ADITIA NOVIRMAN",
    "prodi": "Sistem Informasi",
    "total_sks": 2,
    "ipk": 4.0,
    "semesters": [
      {
        "sem_id": 20252,
        "semester": "Genap 2025/2026",
        "total_sks": 2,
        "total_mutu": 8,
        "ip": 4.0,
        "matakuliah": [
          {
            "kode_mk": "MKB107226",
            "nama_mk": "PRAKTEK KERJA LAPANGAN",
            "sks": 2,
            "nilai": "A",
            "bobot": 4,
            "mutu": 8,
            "keterangan": "Lulus"
          }
        ]
      }
    ]
  }
}
```

| Field | Tipe | Keterangan |
|---|---|---|
| `ipk` | float | IPK keseluruhan |
| `ip` | float | IP per semester |
| `mutu` | integer | SKS × Bobot |
| `keterangan` | string | `Lulus` (nilai A-C) / `Gagal` (nilai D-E) |

**Tabel Konversi Nilai**
| Nilai | Bobot | Keterangan |
|---|---|---|
| A | 4 | Lulus |
| B | 3 | Lulus |
| C | 2 | Lulus |
| D | 1 | Gagal |
| E | 0 | Gagal |

---

### GET `/nilai/summary`

Ringkasan IP per semester dan IPK. Cocok untuk tampilan chart/grafik di mobile.

> **🔒 Protected**

**Response 200**
```json
{
  "success": true,
  "data": {
    "total_sks_lulus": 2,
    "ipk": 4.0,
    "per_semester": [
      {
        "sem_id": 20252,
        "semester": "Genap 2025/2026",
        "total_sks": 2,
        "ip": 4.0
      }
    ]
  }
}
```

---

## SPP / Pembayaran

### GET `/spp`

Daftar semua tagihan SPP mahasiswa seluruh semester.

> **🔒 Protected**

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
      "status_lunas": "Lunas",
      "tanggal_bayar": "05 Februari 2026",
      "no_bukti": "JN/2026/02/000001",
      "jumlah_bayar": 3500000,
      "status_upload": "dikonfirmasi"
    }
  ]
}
```

| Field | Tipe | Keterangan |
|---|---|---|
| `tagihan` | number | Nominal tagihan SPP dalam Rupiah |
| `status_lunas` | string | `Lunas` / `Belum Lunas` |
| `status_upload` | string\|null | `pending` / `dikonfirmasi` / `ditolak` / null |

---

### GET `/spp/aktif`

Tagihan SPP semester yang sedang aktif saja.

> **🔒 Protected**

**Response 200** — sama dengan satu item dari GET `/spp`

**Response 404**
```json
{
  "success": false,
  "message": "Data tagihan semester aktif tidak ditemukan."
}
```

---

### POST `/spp/upload`

Upload bukti pembayaran SPP. File akan dikompresi otomatis oleh server.

> **🔒 Protected**  
> Content-Type: `multipart/form-data`

**Request Form Data**

| Field | Tipe | Wajib | Keterangan |
|---|---|---|---|
| `file` | file (image) | ✅ | Format: jpg/jpeg/png/webp, maks 5MB |
| `jumlah_bayar` | number | ✅ | Nominal yang dibayarkan |
| `sem_id` | integer | ❌ | ID semester (default: semester aktif) |

**Contoh Flutter/Dart**
```dart
var request = http.MultipartRequest(
  'POST',
  Uri.parse('$baseUrl/api/v1/spp/upload'),
);
request.headers['Authorization'] = 'Bearer $token';
request.fields['jumlah_bayar'] = '3500000';
request.files.add(await http.MultipartFile.fromPath('file', imagePath));
var response = await request.send();
```

**Response 201 — Berhasil**
```json
{
  "success": true,
  "message": "Bukti pembayaran berhasil diupload. Menunggu konfirmasi admin."
}
```

**Response 422 — Error**
```json
{
  "success": false,
  "message": "Sudah ada bukti pembayaran yang sedang menunggu konfirmasi admin."
}
```

**Kemungkinan pesan error 422:**
- `Tagihan SPP tidak ditemukan untuk semester ini.`
- `Pembayaran semester ini sudah dikonfirmasi (Lunas).`
- `Sudah ada bukti pembayaran yang sedang menunggu konfirmasi admin.`

---

### GET `/spp/{semId}/status`

Cek status terkini pembayaran untuk semester tertentu.

> **🔒 Protected**

**Path Parameter**

| Parameter | Tipe | Keterangan |
|---|---|---|
| `semId` | integer | ID semester, contoh: `20252` |

**Contoh Request**
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
    "upload_terakhir": {
      "id": 1,
      "status": "dikonfirmasi",
      "jumlah": 3500000,
      "catatan": null,
      "file_url": "http://127.0.0.1:8000/storage/bukti-pembayaran/compressed/uuid.jpg",
      "uploaded_at": "03 Februari 2026 14:00",
      "confirmed_at": "05 Februari 2026 09:00"
    }
  }
}
```

| Field `upload_terakhir` | Tipe | Keterangan |
|---|---|---|
| `status` | string | `pending` / `dikonfirmasi` / `ditolak` |
| `catatan` | string\|null | Alasan penolakan jika status `ditolak` |
| `file_url` | string\|null | URL preview bukti (versi compressed) |

---

## Pengumuman

### GET `/pengumuman`

Daftar pengumuman aktif dari kampus, terbaru di atas.

> **🔒 Protected**

**Query Parameters**

| Parameter | Tipe | Keterangan |
|---|---|---|
| `page` | integer | Nomor halaman (default: 1) |

**Response 200**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "judul": "Pengisian KRS Semester Genap 2025/2026",
      "isi": "<p>Mahasiswa diwajibkan mengisi KRS mulai tanggal...</p>",
      "tgl_publish": "2026-01-15",
      "aktif": true,
      "created_at": "2026-01-14T10:00:00.000000Z",
      "updated_at": "2026-01-14T10:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 10,
    "total": 1,
    "last_page": 1
  }
}
```

> ⚠️ Field `isi` berisi HTML. Gunakan `WebView` atau HTML parser di Flutter untuk menampilkannya.

---

### GET `/pengumuman/{id}`

Detail satu pengumuman.

> **🔒 Protected**

**Path Parameter**

| Parameter | Tipe | Keterangan |
|---|---|---|
| `id` | integer | ID pengumuman |

**Contoh Request**
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

**Response 404**
```json
{
  "message": "No query results for model [App\\Models\\Pengumuman] 99"
}
```

---

## Kode Error

### HTTP Status Codes

| Kode | Arti | Kapan Terjadi |
|---|---|---|
| `200` | OK | Request berhasil |
| `201` | Created | Data berhasil dibuat (POST KRS, upload bukti) |
| `401` | Unauthorized | Token tidak ada / expired / invalid |
| `404` | Not Found | Data tidak ditemukan |
| `422` | Unprocessable | Validasi gagal atau business rule dilanggar |
| `500` | Server Error | Error internal server |

### Response 401 — Token Expired / Invalid
```json
{
  "message": "Unauthenticated."
}
```
> Redirect ke halaman login.

### Response 422 — Validasi
```json
{
  "message": "The nobp field is required.",
  "errors": {
    "nobp": ["The nobp field is required."],
    "password": ["The password field is required."]
  }
}
```

---

## Data Testing

Gunakan data berikut untuk testing di Postman / aplikasi Flutter:

```
Base URL  : http://127.0.0.1:8000/api/v1
NoBP      : 2210050
Password  : 01112002
Semester  : 20252 (Genap 2025/2026)
```

### Alur Testing Lengkap
```
1. POST /login              → dapat token
2. GET  /me                 → cek info user
3. GET  /semester/aktif     → cek semester & periode KRS
4. GET  /profil             → lihat profil mahasiswa
5. GET  /kelas              → lihat daftar kelas tersedia
6. GET  /krs                → lihat KRS aktif
7. GET  /nilai              → lihat histori nilai
8. GET  /nilai/summary      → lihat IP & IPK
9. GET  /spp                → lihat tagihan SPP
10. GET /spp/aktif          → tagihan semester ini
11. GET /spp/20252/status   → status pembayaran
12. GET /pengumuman         → daftar pengumuman
13. POST /logout            → logout
```

---

## Catatan Integrasi Flutter

### Simpan Token
```dart
// Simpan setelah login
await SharedPreferences.getInstance()
  .then((prefs) => prefs.setString('token', token));
```

### Tambah Header Authorization
```dart
Map<String, String> headers = {
  'Authorization': 'Bearer $token',
  'Accept': 'application/json',
  'Content-Type': 'application/json',
};
```

### Handle 401
```dart
if (response.statusCode == 401) {
  // Hapus token, redirect ke login
  await prefs.remove('token');
  Navigator.pushReplacementNamed(context, '/login');
}
```

### Format semId
```dart
// Semester Ganjil 2025 → 20251
// Semester Genap 2025  → 20252
int semId = 20252;
```

---

*Dokumen ini dibuat otomatis berdasarkan implementasi controller. Update jika ada perubahan endpoint.*
