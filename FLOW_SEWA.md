# FLOW SEWA — Sistem Manajemen Kost Serene

> Dokumen ini menjelaskan alur lengkap proses sewa kamar kost, mulai dari check-in,
> penagihan, pembayaran, upgrade kamar, perpanjangan, hingga check-out/pembatalan.
> Dilengkapi logic berdasarkan tipe harga dan role pengguna.

---

## DAFTAR ISI

1. [Entitas Utama & Relasi](#1-entitas-utama--relasi)
2. [Status & Lifecycle](#2-status--lifecycle)
3. [Flow Utama: Check-In (Mulai Sewa)](#3-flow-utama-check-in-mulai-sewa)
4. [Flow Penagihan (Billing)](#4-flow-penagihan-billing)
5. [Flow Pembayaran (Payment)](#5-flow-pembayaran-payment)
6. [Flow Perpanjangan Sewa](#6-flow-perpanjangan-sewa)
7. [Flow Upgrade / Pindah Kamar](#7-flow-upgrade--pindah-kamar)
8. [Flow Check-Out / Selesai Sewa](#8-flow-check-out--selesai-sewa)
9. [Flow Pembatalan (Cancel)](#9-flow-pembatalan-cancel)
10. [Matrix Izin Berdasarkan Role](#10-matrix-izin-berdasarkan-role)
11. [Skenario Edge Case](#11-skenario-edge-case)

---

## 1. Entitas Utama & Relasi

```
Consumer (Penyewa)
    └── RoomOccupancy (Kontrak Sewa)
            ├── Room (Kamar)
            └── Billing (Tagihan)
                    ├── BillingDetail (Rincian tagihan)
                    └── Payment (Bukti bayar)
```

| Model | Tabel | Kolom Kunci |
|---|---|---|
| `RoomOccupancy` | `room_occupancies` | `room_id`, `consumer_id`, `tanggal_masuk`, `tanggal_keluar`, `tipe_harga`, `status` |
| `Billing` | `billings` | `invoice_number`, `consumer_id`, `room_id`, `periode_awal`, `periode_akhir`, `total_tagihan`, `status` |
| `BillingDetail` | `billing_details` | `billing_id`, `keterangan`, `qty`, `harga`, `subtotal` |
| `Payment` | `payments` | `billing_id`, `tanggal_bayar`, `jumlah`, `metode`, `bukti_bayar` |
| `Room` | `rooms` | `nomor_kamar`, `jenis_kamar`, `harga` (bulanan), `harga_harian`, `status` |

---

## 2. Status & Lifecycle

### Room (`rooms.status`)
```
tersedia  ──[check-in]──►  terisi  ──[check-out/selesai]──►  tersedia
```

### RoomOccupancy (`room_occupancies.status`)
```
aktif  ──[selesai/perpanjangan/tanggal_keluar terlewat]──►  tidak aktif
```
> Ketika `tanggal_keluar` sudah terlewat (< hari ini), sistem otomatis set status menjadi `tidak aktif` saat halaman occupancy dimuat (lazy-update di `index()`).

### Billing (`billings.status`)
```
pending  ──[ada bayar sebagian]──►  sebagian  ──[lunas penuh]──►  lunas
   └──[bayar penuh langsung]──────────────────────────────────────►  lunas
```
> `updateStatus()` di model `Billing` dipanggil setiap kali payment ditambah/diubah.

---

## 3. Flow Utama: Check-In (Mulai Sewa)

### Langkah-langkah
```
1. Pilih kamar (status = tersedia)
2. Pilih atau buat penyewa (consumer)
3. Isi tanggal_masuk, tanggal_keluar, tipe_sewa (bulanan/harian)
4. Submit form → RoomOccupancyController@store()
```

### Logic di Controller (`store()`)

```
IF tipe_sewa == 'bulanan' AND tanggal_keluar kosong:
    tanggal_keluar = tanggal_masuk + 30 hari

tipe_harga = tipe_sewa  ← disimpan ke kolom tipe_harga

RoomOccupancy::create() dengan status = 'aktif'
Room::update(['status' => 'terisi'])
BillingService::generateBillingForOccupancy($occupancy)
```

### Kalkulasi Billing Otomatis (`BillingService`)

#### Tipe Harian
```
harga_per_hari = room->harga_harian
qty            = diffInDays(tanggal_masuk, tanggal_keluar)
subtotal       = harga_per_hari × qty

Keterangan: "Sewa Kamar {nomor} - Harian ({qty} hari)"
```

#### Tipe Bulanan (dengan prorate)
```
harga_bulanan  = room->harga
days_in_month  = jumlah hari dalam bulan tanggal_masuk
harga_per_hari = harga_bulanan / days_in_month
subtotal_raw   = harga_per_hari × qty_hari
subtotal       = round(subtotal_raw / 100) × 100  ← dibulatkan ke ratusan

Keterangan: "Sewa Kamar {nomor} - Bulanan (Prorate: {qty} hari dari {days_in_month} hari)"
```

#### Addon (tambahan fasilitas)
```
Setiap addon yang terpasang di kamar → ditambah sebagai BillingDetail terpisah
qty = 1, harga = addon->harga
```

---

## 4. Flow Penagihan (Billing)

### Pembuatan Tagihan
Tagihan dibuat **otomatis** saat check-in melalui `BillingService`. Tidak perlu dibuat manual.

### Melihat Tagihan
- Semua role dapat melihat daftar billing (`billings.index`, `billings.show`)
- Halaman show menampilkan rincian detail dan daftar payment

### Edit Tagihan
- **Hanya Owner (role_id = 1)** dapat edit billing
- Bisa mengubah: `periode_awal`, `periode_akhir`, `total_tagihan`
- Bisa tambah/ubah/hapus `BillingDetail`
- Bisa tambah/ubah payment langsung dari form billing edit
- Setelah disimpan → `updateStatus()` dipanggil ulang

### Download Invoice PDF
- Semua role dengan akses billing dapat download PDF
- PDF berisi letterhead logo Serene, detail tagihan, dan rincian pembayaran

---

## 5. Flow Pembayaran (Payment)

### Cara Tambah Payment
```
1. Dari halaman Billing → klik "Bayar" / "Tambah Pembayaran"
2. Isi: tanggal_bayar, jumlah, metode (tunai/transfer/qris), upload bukti_bayar (WAJIB)
3. Submit → PaymentController@store()
4. Billing::updateStatus() dipanggil → status billing diperbarui
```

### Validasi Wajib
- `bukti_bayar_file` → **required**, format: jpg/jpeg/png/pdf, max 2MB
- File disimpan di `storage/app/public/payments/{uuid}.{ext}`

### Status Billing Setelah Payment
```
IF total_dibayar == 0         → pending
IF total_dibayar < total      → sebagian
IF total_dibayar >= total     → lunas
```

### Dua Jalur Tambah Payment
| Jalur | Route | Catatan |
|---|---|---|
| Langsung dari form payments/create | `payments.store` | Validasi via `StorePaymentRequest` |
| Dari edit billing (owner) | `billings.update` | Validasi inline di `BillingController@update()` |

---

## 6. Flow Perpanjangan Sewa

### Siapa yang Bisa?
Semua role (termasuk Admin dan pengguna lain) dapat melakukan perpanjangan via tombol "Perpanjang" di halaman occupancy.

### Langkah-langkah
```
1. Klik tombol "Perpanjang" pada occupancy aktif
2. Sistem menghitung:
   - tanggal_masuk_baru = tanggal_keluar_lama (same day overlap)
   - tanggal_keluar_baru = tanggal_masuk_baru + 1 bulan, diset ke tanggal 5
3. Owner dapat mengubah tanggal jika perlu
4. Submit → RoomOccupancyController@update() dengan is_extending = '1'
```

### Logic di Controller (update — extend mode)
```
occupancy_lama.status = 'tidak aktif'   ← tutup occupancy lama

RoomOccupancy::create({
    room_id       : sama,
    consumer_id   : sama,
    tanggal_masuk : tanggal_keluar_lama,
    tanggal_keluar: tanggal_masuk + 1 bulan (ke tgl 5),
    tipe_harga    : 'bulanan',
    status        : 'aktif',
})

Room status TIDAK diubah (tetap 'terisi')

BillingService::generateBillingForOccupancy(occupancy_baru)
← billing baru dibuat dengan harga penuh (tidak prorate karena start awal bulan)
```

### Catatan Penting
- Billing lama tetap ada dan statusnya tidak berubah
- Billing baru dibuat untuk periode perpanjangan
- Jika billing lama masih `pending` atau `sebagian`, penyewa harus tetap melunasi billing lama

---

## 7. Flow Upgrade / Pindah Kamar

### Siapa yang Bisa?
**Owner (role_id=1) dan Admin (role_id=2)** — role lain mendapat error 403.

### Langkah-langkah
```
1. Klik tombol "Upgrade" pada occupancy aktif
2. Pilih kamar baru (hanya kamar dengan status 'tersedia')
3. Isi upgrade_from, upgrade_to (rentang tanggal upgrade)
4. Pilih rent_type: Bulanan atau Harian
5. Submit → RoomOccupancyController@applyUpgrade()
```

### Logic Kalkulasi Selisih Harga

#### Jika rent_type = Bulanan
```
days = diffInDays(upgrade_from, upgrade_to)

IF days <= 30:
    old_total = kamar_lama.harga
    new_total = kamar_baru.harga
ELSE:
    remaining_days = days - 30
    old_total = kamar_lama.harga + (kamar_lama.harga_harian × remaining_days)
    new_total = kamar_baru.harga + (kamar_baru.harga_harian × remaining_days)
```

#### Jika rent_type = Harian
```
old_total = kamar_lama.harga_harian × days
new_total = kamar_baru.harga_harian × days
```

#### Penerapan Selisih ke Billing
```
delta = new_total - old_total

IF delta != 0:
    BillingDetail::create({
        billing_id  : billing_aktif_atau_terakhir,
        keterangan  : "Upgrade kamar dari {lama} ke {baru} ({tgl_from} s/d {tgl_to})",
        qty         : 1,
        harga       : delta,   ← bisa negatif jika downgrade ke harga lebih rendah
        subtotal    : delta,
    })
    billing.total_tagihan += delta

    IF delta > 0 AND billing.status == 'lunas':
        billing.status = 'sebagian'   ← re-open billing jika ada tambahan tagihan
```

> **Penting:** Jika `delta` negatif (downgrade ke kamar lebih murah), `subtotal` akan bernilai negatif → ini mengurangi total tagihan di billing yang ada.

### Perubahan Status Setelah Upgrade
```
occupancy.room_id = kamar_baru.id
kamar_baru.status = 'terisi'
kamar_lama.status = 'tersedia'
billing.room_id   = kamar_baru.id
```

---

## 7.1 Skenario Upgrade Spesifik

### A. Upgrade ke Kamar Lebih Mahal (bulanan → bulanan)
```
Kamar lama: Rp 800.000/bulan
Kamar baru: Rp 1.200.000/bulan
Periode  : 30 hari

delta = 1.200.000 - 800.000 = +400.000

→ BillingDetail baru dengan subtotal +400.000
→ total_tagihan naik 400.000
→ Jika billing sudah lunas → status kembali ke 'sebagian'
→ Penyewa perlu bayar selisih 400.000
```

### B. Downgrade ke Kamar Lebih Murah (bulanan → bulanan)
```
Kamar lama: Rp 1.200.000/bulan
Kamar baru: Rp 800.000/bulan
Periode  : 30 hari

delta = 800.000 - 1.200.000 = -400.000

→ BillingDetail baru dengan subtotal -400.000 (kredit)
→ total_tagihan turun 400.000
→ Status billing dihitung ulang (mungkin langsung lunas)
```

### C. Ganti Tipe: Harian → Bulanan
```
Ini dilakukan melalui form upgrade dengan memilih rent_type berbeda.
Namun perlu diperhatikan: occupancy.tipe_harga TIDAK diubah secara otomatis
oleh applyUpgrade(). Jika perlu mengubah tipe_harga di occupancy,
harus dilakukan via edit occupancy (Owner only).

Kalkulasi selisih menggunakan rent_type yang dipilih di form upgrade,
bukan tipe_harga yang tersimpan di occupancy.
```

### D. Ganti Tipe: Bulanan → Harian
```
Sama dengan C — rent_type dipilih 'Harian' di form upgrade.
old_total = kamar_lama.harga_harian × days
new_total = kamar_baru.harga_harian × days
delta dimasukkan ke billing aktif.
```

### E. Kamar Sama, Hanya Ganti Tipe Harga
```
applyUpgrade() mensyaratkan room_id != occupancy.room_id.
→ Tidak bisa upgrade ke kamar yang sama.
→ Untuk mengubah tipe_harga saja, gunakan edit occupancy (Owner only).
```

---

## 8. Flow Check-Out / Selesai Sewa

### Cara 1: Manual (Tombol "Selesai")
```
RoomOccupancyController@complete()
    occupancy.status = 'tidak aktif'
    room.status      = 'tersedia'
```
> Tidak ada pengecekan apakah billing sudah lunas — selesaikan billing secara terpisah.

### Cara 2: Otomatis (Tanggal Keluar Terlewat)
```
Di RoomOccupancyController@index():
    IF tanggal_keluar < hari_ini:
        occupancy.status = 'tidak aktif'
        room.status      = 'tersedia'
```
> Ini terjadi saat halaman occupancy dimuat. Bukan background job/scheduler.

### Catatan
- Billing yang belum lunas tidak otomatis dibatalkan saat check-out
- Data occupancy, billing, dan payment tetap tersimpan sebagai histori
- Kamar langsung bisa disewa oleh penyewa baru

---

## 9. Flow Pembatalan (Cancel)

### Delete Occupancy
```
RoomOccupancyController@destroy()
    occupancy.delete()
← Tidak ada penghapusan billing, detail, atau payment secara otomatis
← Tidak ada pengembalian status room secara otomatis
```

> **Perhatian:** `destroy()` saat ini tidak memiliki pengecekan role. Perlu ditambahkan
> pemeriksaan role jika diperlukan. Juga tidak me-reset `room.status` ke `tersedia`.

### Rekomendasi Prosedur Cancel Manual
```
1. Hapus atau void billing terkait (via billing edit — Owner only)
2. Klik "Selesai" pada occupancy → kamar kembali tersedia
3. Atau: langsung hapus occupancy, lalu update status room manual
```

---

## 10. Matrix Izin Berdasarkan Role

| Aksi | Owner (1) | Admin (2) | Lainnya |
|---|:---:|:---:|:---:|
| Lihat daftar occupancy | ✅ | ✅ | ✅ |
| Check-in penyewa baru | ✅ | ✅ | ❌ |
| Edit data occupancy | ✅ | ❌ | ❌ |
| Perpanjang sewa | ✅ | ✅ | ✅* |
| Upgrade / pindah kamar | ✅ | ✅ | ❌ |
| Selesaikan sewa (complete) | ✅ | ✅ | ✅* |
| Hapus occupancy (destroy) | ✅ | ✅ | ✅* |
| Lihat billing | ✅ | ✅ | ✅ |
| Edit billing | ✅ | ❌ | ❌ |
| Download invoice PDF | ✅ | ✅ | ✅ |
| Tambah payment | ✅ | ✅ | ✅* |
| Edit / hapus payment | ✅ | ❌ | ❌ |

> \* Belum ada pembatasan role di controller untuk aksi ini — accessible ke semua authenticated user.

### Detail Role Check di Controller

```php
// Edit occupancy (non-extend)
if (!$isExtending && auth()->user()->role_id !== 1) abort(403);

// Update occupancy (non-extend)
if (!$isExtending && auth()->user()->role_id !== 1) abort(403);

// Upgrade form & apply upgrade
if (!in_array(auth()->user()->role_id, [1, 2])) abort(403);

// Edit billing
if (auth()->user()->role_id !== 1) abort(403);

// Update billing
if (auth()->user()->role_id !== 1) abort(403);
```

---

## 11. Skenario Edge Case

### A. Penyewa Check-In di Akhir Bulan (Prorate Bulanan)
```
Masuk     : 28 Januari
Keluar    : 27 Februari (30 hari)
days_in_month = 31 (Januari)
harga_per_hari = 1.200.000 / 31 = 38.709,67
subtotal_raw   = 38.709,67 × 30 = 1.161.290,32
subtotal       = round(1.161.290 / 100) × 100 = 1.161.300
```

### B. Upgrade Ditambah ke Billing yang Sudah Lunas
```
→ BillingDetail delta ditambahkan
→ total_tagihan bertambah
→ billing.status diset ke 'sebagian' (re-opened)
→ Penyewa wajib bayar selisih lagi
```

### C. Delta Upgrade = 0 (Harga Sama)
```
→ TIDAK ada BillingDetail yang dibuat
→ billing tidak berubah
→ occupancy dan room tetap diperbarui
```

### D. Tidak Ada Billing Aktif Saat Upgrade
```
→ Sistem mencari billing terbaru (apapun statusnya)
→ Jika tetap tidak ada → return error:
   "Tidak ada invoice aktif untuk ditambahkan selisih"
→ Upgrade dibatalkan
```

### E. Tanggal Keluar Kosong (Sewa Bulanan Tanpa Batas)
```
→ RoomOccupancy.tanggal_keluar = null
→ days_remaining ditampilkan sebagai '-' di UI
→ Tidak pernah otomatis inactive (karena tanggal_keluar kosong)
→ Harus di-complete secara manual
```

### F. Billing Tapi Kamar Sudah Ganti (Setelah Upgrade)
```
→ billing.room_id diperbarui ke kamar baru setelah upgrade
→ Histori detail lama tetap tersimpan di billing_details
→ Invoice akan menunjukkan kamar baru sebagai kamar aktif
```

---

## RINGKASAN FLOW DIAGRAM

```
[Consumer dipilih]
        │
        ▼
[Pilih Kamar Tersedia]
        │
        ▼
[Isi Form Check-In: tanggal, tipe_sewa]
        │
        ▼
[RoomOccupancy.create() → status: aktif]
[Room.update()          → status: terisi]
[BillingService.generate() → Billing + BillingDetail]
        │
        ▼
[Tagihan Muncul di Dashboard Penyewa]
        │
        ├──[Perpanjang]──► [Close occupancy lama] → [Buat occupancy baru] → [Billing baru]
        │
        ├──[Upgrade]──► [Hitung delta] → [Tambah BillingDetail] → [Pindah kamar]
        │
        ├──[Bayar]────► [Payment.create() dengan bukti wajib] → [Billing.updateStatus()]
        │                      │
        │               [status: pending → sebagian → lunas]
        │
        └──[Selesai]──► [occupancy.status = tidak aktif] → [room.status = tersedia]
```

---

*Dokumen ini mencerminkan implementasi aktual di `RoomOccupancyController`, `BillingController`, `PaymentController`, dan `BillingService` per tanggal pembuatan.*
