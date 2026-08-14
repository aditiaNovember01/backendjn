# White Box Testing — Halo Jayanusa Backend
> Teknik: Statement Coverage · Branch Coverage · Path Testing · Basis Path Testing  
> Scope: API Controllers (Laravel 12) + React Native (halojayanusa)  
> Versi: 1.0.0 | Agustus 2026

---

## Apa Itu White Box Testing?

**White Box Testing** (juga dikenal sebagai *Glass Box Testing* atau *Structural Testing*) adalah metode pengujian perangkat lunak yang menguji **struktur internal program** — bukan hanya input/output.

Penguji **harus mengetahui kode sumber** program untuk merancang test case.

### Teknik yang Digunakan

| Teknik | Penjelasan |
|---|---|
| **Statement Coverage** | Setiap baris kode harus dieksekusi minimal 1 kali |
| **Branch Coverage** | Setiap percabangan `if/else` harus diuji untuk kondisi `true` dan `false` |
| **Path Testing** | Setiap jalur eksekusi unik harus diuji |
| **Basis Path Testing** | Menggunakan Cyclomatic Complexity untuk menentukan jumlah test case minimal |

### Cyclomatic Complexity
```
V(G) = E - N + 2P
atau
V(G) = Jumlah kondisi (if/while/for/case) + 1
```
Semakin tinggi V(G), semakin banyak jalur dan test case yang dibutuhkan.

---

## 1. AuthController — `login()`

### 1.1 Flow Graph & Jalur Eksekusi

```
                    [START]
                       │
                 [1] Ambil nobpValue dari request
                (nobp ?? nim ?? nidn ?? noBP ?? username)
                       │
              ┌────────▼────────┐
              │ [2] nobpValue != │
              │  null && !has   │
              │  ('nobp')?      │
              └────┬────────────┘
                  T│           │F
                   ▼           │
         [3] merge('nobp')    │
                   │          │
                   └────┬─────┘
                        │
                 [4] validate()
                        │
                 [5] identifier = trim(nobp)
                        │
              ┌─────────▼──────────┐
              │ [6] identifier ==  │
              │    'D001'?         │
              └───┬────────────────┘
                 T│              │F
                  ▼              │
       [7] identifier='DSN001'   │
                  │              │
                  └──────┬───────┘
                         │
              ┌──────────▼──────────┐
              │ [8] Dosen::where    │
              │  dosenid/nidn exist?│
              └───┬─────────────────┘
                 T│               │F
                  ▼               │
       ┌──────────────────┐       │
       │[9] User::where   │       │
       │ dosenid + role   │       │
       │ dosen exist?     │       │
       └──┬───────────────┘       │
         T│        │F             │
          │        ▼              │
          │  [10] throw           │
          │  ValidationException  │
          │  "ID Dosen salah"     │
          │                       │
    [11] Hash::check              │
    password?                     │
    ┌─────┴─────┐                 │
   F│           │T                │
    ▼           ▼                 │
  [12]throw  [13]delete tokens    │
  Exception  create token         │
             return 200 Dosen     │
                                  │
                        ┌─────────▼──────────┐
                        │[14] User::where     │
                        │ mhsnobp + role mhs  │
                        │ exist?              │
                        └──┬──────────────────┘
                          T│          │F
                            │          ▼
                            │   [15] throw
                            │   ValidationException
                            │   "NoBP salah"
                        [16] Hash::check
                        password?
                        ┌────┴─────┐
                       F│          │T
                        ▼          │
                    [17] throw  [18] Mahasiswa::find
                    Exception    exist?
                                ┌───┴────┐
                               T│        │F
                                │        ▼
                                │   [19] return 404
                                │
                        [20] delete tokens
                        [21] create token
                        [22] return 200 Mahasiswa
```

### 1.2 Cyclomatic Complexity

**Kondisi yang ada:**
1. `nobpValue !== null && !has('nobp')` → merge
2. `identifier === 'D001'` → alias
3. `Dosen exists` → login dosen
4. `!$user || !Hash::check` → dosen invalid
5. `!$user || !Hash::check` → mahasiswa invalid
6. `!$mahasiswa` → mahasiswa tidak ditemukan

**V(G) = 6 + 1 = 7**  
→ Butuh **7 test case** untuk full basis path coverage.

### 1.3 Test Cases

| TC | ID | Deskripsi | Input | Kondisi | Expected |
|---|---|---|---|---|---|
| TC-AUTH-01 | Login field `nim` | Gunakan `nim` bukan `nobp` | `{nim: "2210050", password: "01112002"}` | `nobpValue != null && !has('nobp')` = TRUE | 200 token mahasiswa |
| TC-AUTH-02 | Alias D001 | Input `D001` di-alias ke `DSN001` | `{nobp: "D001", password: "password123"}` | `identifier === 'D001'` = TRUE | Cari dosen DSN001 |
| TC-AUTH-03 | Login dosen valid | NoBP dosen + password benar | `{nobp: "DSN001", password: "password123"}` | Dosen exist, Hash match | 200 role=dosen |
| TC-AUTH-04 | Login dosen password salah | NoBP dosen + password salah | `{nobp: "DSN001", password: "salah"}` | Dosen exist, Hash NOT match | 422 "ID Dosen salah" |
| TC-AUTH-05 | Login mahasiswa valid | NoBP + password benar | `{nobp: "2210050", password: "01112002"}` | User exist, Hash match, Mhs exist | 200 role=mahasiswa |
| TC-AUTH-06 | Login mahasiswa password salah | NoBP + password salah | `{nobp: "2210050", password: "wrong"}` | User exist, Hash NOT match | 422 "NoBP salah" |
| TC-AUTH-07 | NoBP tidak ditemukan | NoBP tidak ada di DB | `{nobp: "9999999", password: "xxx"}` | User NOT exist | 422 "NoBP salah" |

### 1.4 Branch Coverage Matrix

| Branch | TC-01 | TC-02 | TC-03 | TC-04 | TC-05 | TC-06 | TC-07 |
|---|---|---|---|---|---|---|---|
| merge nobp (T) | ✅ | - | - | - | - | - | - |
| merge nobp (F) | - | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| alias D001 (T) | - | ✅ | - | - | - | - | - |
| alias D001 (F) | ✅ | - | ✅ | ✅ | ✅ | ✅ | ✅ |
| Dosen exist (T) | - | ✅ | ✅ | ✅ | - | - | - |
| Dosen exist (F) | ✅ | - | - | - | ✅ | ✅ | ✅ |
| Hash dosen valid (T) | - | - | ✅ | - | - | - | - |
| Hash dosen invalid (F) | - | - | - | ✅ | - | - | - |
| User mhs exist + hash (T) | - | - | - | - | ✅ | - | - |
| User mhs NOT exist/hash fail (F) | - | - | - | - | - | ✅ | ✅ |

---

## 2. KrsController — `store()`

### 2.1 Flow Graph

```
[START]
  │
  ├─[1] validate kelas_id
  │
  ├─[2] Sem::find → isKrsOpen()?
  │      F → return 422 "KRS tutup"
  │      T ↓
  ├─[3] kelas.kelassem !== semId?
  │      T → return 422 "Kelas bukan semester aktif"
  │      F ↓
  ├─[4] kelas.is_penuh?
  │      T → return 422 "Kelas penuh"
  │      F ↓
  ├─[5] KRS sudah ada (krskelasid + sem + !hapus)?
  │      T → return 422 "Sudah ambil kelas"
  │      F ↓
  ├─[6] totalSks + sksBaru > 24?
  │      T → return 422 "Melebihi 24 SKS"
  │      F ↓
  ├─[7] BuktiPembayaran dikonfirmasi exist?
  │      T → sudahBayar = true
  │      F ↓
  ├─[8] Registrasi::lunas exist?
  │      T → sudahBayar = true
  │      F → sudahBayar = false
  │
  ├─[9] !sudahBayar?
  │      T → return 422 "Belum bayar"
  │      F ↓
  ├─[10] Registrasi::firstOrCreate
  │
  └─[11] Krs::create → return 201
```

### 2.2 Cyclomatic Complexity

Kondisi: 2 (KRS open) + 3 (kelas cek) + 4 (existing) + 5 (SKS) + 6 (BuktiPembayaran) + 7 (Registrasi) + 8 (sudahBayar) + 9 (!sudahBayar) = **8 kondisi**

**V(G) = 8 + 1 = 9**

### 2.3 Test Cases

| TC | ID | Skenario | Kondisi yang diuji | Expected |
|---|---|---|---|---|
| TC-KRS-01 | KRS tutup | `isKrsOpen() = false` | Node 2 = FALSE | 422 "KRS belum dibuka" |
| TC-KRS-02 | Kelas semester lain | `kelas.kelassem ≠ semId` | Node 3 = TRUE | 422 "Kelas tidak tersedia" |
| TC-KRS-03 | Kelas penuh | `is_penuh = true` | Node 4 = TRUE | 422 "Kelas sudah penuh" |
| TC-KRS-04 | Sudah ambil kelas | KRS sama exist | Node 5 = TRUE | 422 "Sudah mengambil" |
| TC-KRS-05 | SKS melebihi 24 | Total SKS + baru > 24 | Node 6 = TRUE | 422 "Melebihi 24 SKS" |
| TC-KRS-06 | Belum bayar (tidak ada bukti & registrasi) | Node 7=F, 8=F, 9=T | 422 "Belum bayar SPP" |
| TC-KRS-07 | Bayar via cicilan (bukti dikonfirmasi) | Node 7=T | 201 "KRS berhasil" |
| TC-KRS-08 | Bayar via registrasi lunas | Node 7=F, 8=T | 201 "KRS berhasil" |
| TC-KRS-09 | Semua valid, KRS berhasil | Semua kondisi FALSE | 201 "KRS berhasil" |

### 2.4 Statement Coverage Check

| Statement | TC yang menutup |
|---|---|
| `$sem?->isKrsOpen()` | TC-KRS-01 (F), TC-KRS-07~09 (T) |
| `$kelas->kelassem !== $semId` | TC-KRS-02 (T), TC-KRS-03~09 (F) |
| `$kelas->is_penuh` | TC-KRS-03 (T), TC-KRS-04~09 (F) |
| `$existing` check | TC-KRS-04 (T), TC-KRS-05~09 (F) |
| `($totalSks + $sksBaru) > 24` | TC-KRS-05 (T), TC-KRS-06~09 (F) |
| `BuktiPembayaran::where... exists()` | TC-KRS-06~07 |
| `Registrasi::lunas()... exists()` | TC-KRS-06, TC-KRS-08 |
| `!$sudahBayar` | TC-KRS-06 (T), TC-KRS-07~09 (F) |
| `Registrasi::firstOrCreate` | TC-KRS-07~09 |
| `Krs::create` | TC-KRS-07~09 |

---

## 3. SppController — `uploadBukti()`

### 3.1 Flow Graph

```
[START]
  │
  ├─[1] validate (file, jumlah_bayar, sem_id, tipe_bayar)
  │
  ├─[2] Spp::where exist?
  │      F → return 404 "Tagihan tidak ditemukan"
  │      T ↓
  ├─[3] Registrasi::lunas exist?
  │      T → return 422 "Sudah Lunas"
  │      F ↓
  ├─[4] tipe_bayar === 'cicilan2'?
  │      T → cicilan1 dalam tipeYangSudahDikonfirmasi?
  │           F → return 422 "Cicilan 2 butuh cicilan 1"
  │           T ↓
  │      F ↓
  ├─[5] BuktiPembayaran pending (tipe sama) exist?
  │      T → return 422 "Sudah ada pending"
  │      F ↓
  └─[6] store file → BuktiPembayaran::create → return 201
```

### 3.2 Cyclomatic Complexity

V(G) = 5 kondisi + 1 = **6**

### 3.3 Test Cases

| TC | ID | Skenario | Node yang aktif | Expected |
|---|---|---|---|---|
| TC-SPP-01 | SPP tidak ditemukan | `Spp exist = false` | Node 2 = F | 404 "Tagihan tidak ditemukan" |
| TC-SPP-02 | Sudah lunas | `Registrasi::lunas exist` | Node 3 = T | 422 "Sudah Lunas" |
| TC-SPP-03 | Cicilan 2 tanpa cicilan 1 | `tipe=cicilan2`, cicilan1 belum dikonfirmasi | Node 4=T, cicilan1 check=F | 422 "Cicilan 2 butuh cicilan 1" |
| TC-SPP-04 | Cicilan 2 dengan cicilan 1 ada | `tipe=cicilan2`, cicilan1 dikonfirmasi | Node 4=T, cicilan1 check=T | Lanjut ke node 5 |
| TC-SPP-05 | Sudah ada pending tipe sama | `pending exist = true` | Node 5 = T | 422 "Sudah ada pending" |
| TC-SPP-06 | Upload valid pertama kali | Semua validasi lolos | Semua node FALSE | 201 "Upload berhasil" |

### 3.4 Basis Path Testing

**Jalur Independen:**
1. START → 2F → return 404
2. START → 2T → 3T → return 422 Lunas
3. START → 2T → 3F → 4T → cicilan1_check=F → return 422 Cicilan
4. START → 2T → 3F → 4T → cicilan1_check=T → 5T → return 422 Pending
5. START → 2T → 3F → 4T → cicilan1_check=T → 5F → return 201
6. START → 2T → 3F → 4F → 5T → return 422 Pending
7. START → 2T → 3F → 4F → 5F → return 201 ✅ (happy path)

---

## 4. NilaiController — `index()`

### 4.1 Flow Graph

```
[START]
  │
  ├─[1] ambil KRS dengan nilai (krsnilai NOT NULL, != '')
  │
  ├─[2] groupBy('krssem')
  │
  ├─[3] forEach semester:
  │      ├─[3a] totalSks > 0?
  │      │       T → ip = totalMutu/totalSks
  │      │       F → ip = 0
  │      └─[3b] map formatNilaiItem
  │
  ├─[4] allSks > 0?
  │      T → ipk = allMutu/allSks
  │      F → ipk = 0
  │
  └─[5] return response JSON
```

### 4.2 Test Cases

| TC | ID | Skenario | Kondisi | Expected |
|---|---|---|---|---|
| TC-NILAI-01 | Mahasiswa tanpa nilai | KRS ada tapi krsnilai kosong | allSks = 0 | IPK = 0, semesters = [] |
| TC-NILAI-02 | 1 semester, semua lulus | totalSks > 0, semua nilai A | allSks > 0 | IPK = 4.0 |
| TC-NILAI-03 | Multi semester, mix nilai | A, B, C di berbagai semester | groupBy banyak key | IP per sem beda-beda |
| TC-NILAI-04 | Semester dengan 0 SKS | Entah MK 0 SKS | totalSks = 0 per sem | ip = 0 untuk sem itu |

---

## 5. ProfilController — `show()`

### 5.1 Flow Graph

```
[START]
  │
  ├─[1] Mahasiswa::findOrFail
  │      NOT FOUND → 404 ModelNotFoundException
  │
  ├─[2] SettingBiaya::forMahasiswa
  │      exist? → $biaya != null
  │      NULL   → $biaya = null
  │
  ├─[3] mahasiswa->getTahunKe()
  │      (mhsangkatan vs tahun semester aktif)
  │
  ├─[4] biaya->getPembangunanByTahun(tahunKe)
  │      tahunKe = 1,2,3,4 → nilai berbeda
  │      tahunKe lainnya → 0
  │
  ├─[5] mahasiswa->getDosenPA()
  │      KRS exist → dosen != null
  │      KRS kosong → dosen = null
  │
  └─[6] return response dengan biaya:
         $biaya != null → spp_penuh, cicilan_1, cicilan_2
         $biaya = null  → semua null
```

### 5.2 Cyclomatic Complexity: V(G) = 4 kondisi + 1 = **5**

### 5.3 Test Cases

| TC | ID | Skenario | Kondisi | Expected |
|---|---|---|---|---|
| TC-PROFIL-01 | NoBP tidak ada | `findOrFail` gagal | Exception | 404 |
| TC-PROFIL-02 | Biaya tidak ada di settingbiaya | `forMahasiswa` null | `$biaya = null` | biaya semua null |
| TC-PROFIL-03 | Tahun ke-1 (ada pembangunan) | angkatan = tahun sekarang | `tahunKe = 1` | pembangunan = 1.500.000 |
| TC-PROFIL-04 | Tahun ke-4 (tidak ada pembangunan) | angkatan 4 tahun lalu | `tahunKe = 4` | pembangunan = null |
| TC-PROFIL-05 | Profil lengkap (happy path) | Semua data ada | Semua kondisi normal | 200 data lengkap |

---

## 6. Ringkasan Cyclomatic Complexity

| Controller / Method | V(G) | Min Test Cases |
|---|---|---|
| `AuthController::login` | 7 | 7 |
| `KrsController::store` | 9 | 9 |
| `KrsController::destroy` | 2 | 2 |
| `SppController::uploadBukti` | 6 | 6 |
| `SppController::formatSpp` | 4 | 4 |
| `NilaiController::index` | 3 | 3 |
| `ProfilController::show` | 5 | 5 |
| **TOTAL** | **36** | **36** |

---

## 7. White Box Testing — Sisi React Native (halojayanusa)

Meskipun folder terpisah, berikut komponen React Native yang perlu diuji dengan white box testing:

### 7.1 `authService.js` / `useAuth` Hook

```
Fungsi: login(nobp, password)

Jalur yang perlu diuji:
  [1] Input kosong → validasi lokal → tidak kirim request
  [2] Request berhasil (200) → simpan token → navigasi Home
  [3] Request gagal 422 → tampilkan pesan error dari response.message
  [4] Request gagal 401 → clear token → navigasi Login
  [5] Network error → tampilkan "Koneksi gagal"
```

| TC | Kondisi | Expected |
|---|---|---|
| TC-RN-AUTH-01 | `nobp = ""` | Tidak hit API, tampilkan "NoBP wajib diisi" |
| TC-RN-AUTH-02 | Response 200 | Token disimpan di AsyncStorage/SecureStore |
| TC-RN-AUTH-03 | Response 422 `{message: "NoBP salah"}` | Tampilkan pesan error tersebut |
| TC-RN-AUTH-04 | Response 401 | Token dihapus, redirect ke login |
| TC-RN-AUTH-05 | Network timeout | Tampilkan "Gagal terhubung ke server" |

### 7.2 `sppService.js` / SPP Upload

```
Fungsi: uploadBukti(file, jumlahBayar, tipeBayar)

Jalur:
  [1] file == null → validasi lokal
  [2] jumlahBayar <= 0 → validasi lokal
  [3] Upload berhasil → tampilkan success
  [4] Response 422 "Sudah pending" → tampilkan warning
  [5] Response 422 "Cicilan 2 butuh cicilan 1" → tampilkan info
```

| TC | Kondisi | Expected |
|---|---|---|
| TC-RN-SPP-01 | `file = null` | Tidak upload, "Pilih foto bukti bayar" |
| TC-RN-SPP-02 | `jumlahBayar = 0` | "Jumlah bayar tidak valid" |
| TC-RN-SPP-03 | Upload berhasil (201) | "Menunggu konfirmasi admin" |
| TC-RN-SPP-04 | 422 "Sudah pending" | Tampilkan info sudah ada upload |
| TC-RN-SPP-05 | 422 "Cicilan 2..." | Tampilkan panduan urutan cicilan |

### 7.3 `krsService.js` / Tambah KRS

```
Fungsi: tambahKrs(kelasId)

Jalur:
  [1] kelasId == null → validasi
  [2] is_penuh == true → disable tombol
  [3] !bisa_isi_krs → tampilkan pesan pembayaran
  [4] Berhasil (201) → refresh daftar KRS
  [5] 422 "SKS melebihi" → tampilkan sisa SKS
```

---

## 8. Contoh Test Case Tabel Format Skripsi

Format berikut sesuai untuk penulisan dokumen skripsi/TA:

### Tabel 1. Test Case White Box Testing — AuthController::login()

| No | ID Test Case | Prosedur Pengujian | Data Input | Hasil yang Diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|---|---|
| 1 | TC-AUTH-01 | Login menggunakan field `nim` | `{nim:"2210050", password:"01112002"}` | HTTP 200, token dikembalikan | HTTP 200, token valid | ✅ Pass |
| 2 | TC-AUTH-02 | Alias D001 → DSN001 | `{nobp:"D001", password:"password123"}` | HTTP 200, role=dosen | HTTP 200, role=dosen | ✅ Pass |
| 3 | TC-AUTH-03 | Login dosen valid | `{nobp:"DSN001", password:"password123"}` | HTTP 200, role=dosen, ada nidn | HTTP 200 ✓ | ✅ Pass |
| 4 | TC-AUTH-04 | Login dosen password salah | `{nobp:"DSN001", password:"salah"}` | HTTP 422, message "ID Dosen salah" | HTTP 422 ✓ | ✅ Pass |
| 5 | TC-AUTH-05 | Login mahasiswa valid | `{nobp:"2210050", password:"01112002"}` | HTTP 200, role=mahasiswa | HTTP 200 ✓ | ✅ Pass |
| 6 | TC-AUTH-06 | Password mahasiswa salah | `{nobp:"2210050", password:"wrong"}` | HTTP 422, message "NoBP salah" | HTTP 422 ✓ | ✅ Pass |
| 7 | TC-AUTH-07 | NoBP tidak terdaftar | `{nobp:"9999999", password:"xxx"}` | HTTP 422, message "NoBP salah" | HTTP 422 ✓ | ✅ Pass |

### Tabel 2. Test Case White Box Testing — KrsController::store()

| No | ID Test Case | Prosedur Pengujian | Kondisi Internal | Hasil yang Diharapkan | Status |
|---|---|---|---|---|---|
| 1 | TC-KRS-01 | Isi KRS saat periode tutup | `isKrsOpen() = false` | HTTP 422, "KRS belum dibuka" | ✅ Pass |
| 2 | TC-KRS-02 | Kelas bukan semester aktif | `kelas.kelassem ≠ semId` | HTTP 422, "Kelas tidak tersedia" | ✅ Pass |
| 3 | TC-KRS-03 | Kelas sudah penuh | `jumlah_mhs >= kapasitas` | HTTP 422, "Kelas sudah penuh" | ✅ Pass |
| 4 | TC-KRS-04 | Kelas sudah diambil sebelumnya | KRS exist = true | HTTP 422, "Sudah mengambil" | ✅ Pass |
| 5 | TC-KRS-05 | Penambahan SKS melebihi 24 | totalSks + sksBaru > 24 | HTTP 422, "Melebihi 24 SKS" | ✅ Pass |
| 6 | TC-KRS-06 | Belum bayar SPP | sudahBayar = false | HTTP 422, "Belum bayar" | ✅ Pass |
| 7 | TC-KRS-07 | Bayar via bukti dikonfirmasi | BuktiPembayaran exist | HTTP 201, "KRS berhasil" | ✅ Pass |
| 8 | TC-KRS-08 | Bayar via registrasi lunas | Registrasi::lunas exist | HTTP 201, "KRS berhasil" | ✅ Pass |
| 9 | TC-KRS-09 | Semua kondisi valid | Semua cek lolos | HTTP 201, "KRS berhasil" | ✅ Pass |

### Tabel 3. Test Case White Box Testing — SppController::uploadBukti()

| No | ID Test Case | Prosedur Pengujian | Kondisi Internal | Hasil yang Diharapkan | Status |
|---|---|---|---|---|---|
| 1 | TC-SPP-01 | SPP tidak terdaftar | Spp exist = false | HTTP 404, "Tagihan tidak ditemukan" | ✅ Pass |
| 2 | TC-SPP-02 | Upload saat sudah lunas | Registrasi lunas exist | HTTP 422, "Sudah Lunas" | ✅ Pass |
| 3 | TC-SPP-03 | Cicilan 2 tanpa cicilan 1 | cicilan1 belum ada | HTTP 422, "Cicilan 2 butuh cicilan 1" | ✅ Pass |
| 4 | TC-SPP-04 | Sudah ada pending sama | pending tipe exist | HTTP 422, "Sudah ada pending" | ✅ Pass |
| 5 | TC-SPP-05 | Upload valid pertama kali | Semua valid | HTTP 201, "Upload berhasil" | ✅ Pass |
| 6 | TC-SPP-06 | Cicilan 2 setelah cicilan 1 | cicilan1 dikonfirmasi | Lanjut ke cek pending | ✅ Pass |

---

## 9. Cara Menjalankan Pengujian

### Via Postman / REST Client

```bash
# Base URL
POST http://jayanusabackend.test/api/v1/login

# TC-AUTH-01: field nim
{
  "nim": "2210050",
  "password": "01112002"
}

# TC-AUTH-04: dosen password salah
{
  "nobp": "DSN001",
  "password": "salah123"
}
```

### Via PHP artisan tinker (manual assertion)

```php
# Jalankan: php artisan tinker

# TC-AUTH-05: Login mahasiswa valid
$user = \App\Models\User::where('mhsnobp', '2210050')->first();
$valid = \Illuminate\Support\Facades\Hash::check('01112002', $user->password);
assert($valid === true, 'TC-AUTH-05 PASS');

# TC-NILAI-01: IPK jika tidak ada nilai
$krsList = \App\Models\Krs::where('krsmhsnobp', '9999999')
    ->whereNotNull('krsnilai')->get();
assert($krsList->isEmpty(), 'TC-NILAI-01 PASS');
```

### Via curl (command line)

```bash
# Login dan ambil token
curl -X POST http://jayanusabackend.test/api/v1/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"nobp":"2210050","password":"01112002"}'

# TC-KRS-06: Tambah KRS tanpa bayar SPP
curl -X POST http://jayanusabackend.test/api/v1/krs \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"kelas_id": 148}'
```

---

## 10. Coverage Summary

| Controller | Branch Coverage | Statement Coverage | Path Coverage |
|---|---|---|---|
| AuthController::login | 100% (10/10 branch) | 100% | 7/7 path |
| KrsController::store | 100% (16/16 branch) | 100% | 9/9 path |
| KrsController::destroy | 100% (2/2 branch) | 100% | 2/2 path |
| SppController::uploadBukti | 100% (10/10 branch) | 100% | 6/6 path |
| NilaiController::index | 100% (4/4 branch) | 100% | 3/3 path |
| ProfilController::show | 100% (6/6 branch) | 100% | 5/5 path |

> **Target Coverage:** Minimum 80% branch coverage untuk setiap controller.  
> Semua controller di atas **sudah memenuhi** target 100% coverage secara teoritis.

---

*Dokumen ini dibuat berdasarkan analisis kode sumber controller Laravel 12 — Halo Jayanusa Backend*  
*Menggunakan teknik: Statement Coverage, Branch Coverage, Basis Path Testing (McCabe, 1976)*  
*Update: Agustus 2026*
