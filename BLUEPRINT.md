# Blueprint Sistem Manajemen Kost

## 📋 Daftar Isi
1. [Gambaran Umum](#gambaran-umum)
2. [Fitur Utama](#fitur-utama)
3. [Arsitektur Sistem](#arsitektur-sistem)
4. [Database Schema](#database-schema)
5. [Module & Controller](#module--controller)
6. [API Endpoints](#api-endpoints)
7. [Workflow Proses](#workflow-proses)
8. [Instalasi & Setup](#instalasi--setup)

---

## 📌 Gambaran Umum

**Sistem Manajemen Kost** adalah aplikasi web berbasis Laravel 10 untuk mengelola:
- Data penyewa (consumer)
- Data kamar (room)
- Occupancy & checkout management
- Billing & invoice
- Payment tracking
- Addon charges (charges tambahan)
- WhatsApp notifications

**Tech Stack:**
- Backend: Laravel 10.50.0, PHP 8.1.10
- Frontend: Blade Templates, Bootstrap/AdminLTE, JavaScript
- Database: MySQL
- UI: AdminLTE Dashboard

---

## ✨ Fitur Utama

### 1. **Manajemen Kamar & Penyewa**
- ✅ Daftar kamar dengan status (tersedia, terisi, maintenance)
- ✅ Jenis kamar (bulanan, harian, VIP, dll)
- ✅ Harga kamar (bulanan & harian)
- ✅ Data penyewa dengan kontak, NIK, profil

### 2. **Occupancy Management**
- ✅ Check-in/check-out tracking
- ✅ Visual seat map dengan color-coded status:
  - **Hijau (Available)**: Kamar kosong tersedia
  - **Biru (Occupied)**: Kamar terisi, semua lunas
  - **Kuning (Due Soon)**: Sisa ≤5 hari checkout
  - **Merah (Warning)**: Ada tagihan belum lunas & ≤5 hari checkout
  - **Abu-abu (Expired)**: Checkout date sudah lewat
- ✅ Quick detail modal dengan info penyewa, tagihan, & aksi

### 3. **Billing & Invoice**
- ✅ Auto-generate billing saat occupancy dibuat
- ✅ Invoice number (INV-xxx format)
- ✅ Multiple billing details (kamar, addon, dll)
- ✅ Status tracking (pending, sebagian, lunas)
- ✅ PDF invoice download
- ✅ Pagination, search, date range filter

### 4. **Payment Recording**
- ✅ Catat pembayaran untuk setiap invoice
- ✅ Hitung sisa tagihan otomatis
- ✅ Support metode: tunai, transfer, cek
- ✅ Upload bukti pembayaran (file/nota)
- ✅ Auto-update billing status (lunas jika penuh)
- ✅ History pembayaran dengan detail rincian

### 5. **Addon Charges**
- ✅ Master addon data (WiFi, TV, Water, dll)
- ✅ Tambah addon charges ke billing yang sudah ada
- ✅ Auto-merge ke billing (tidak bikin invoice baru)
- ✅ Kalkulasi subtotal otomatis
- ✅ Update billing total & status

### 6. **WhatsApp Integration**
- ✅ Kirim notifikasi tagihan ke penyewa
- ✅ Kirim reminder perpanjang/booking saat lunas
- ✅ Include info: invoice, total, sisa, days to checkout
- ✅ Automatic phone number formatting

### 7. **Reports & Analytics**
- ✅ Occupancy summary
- ✅ Finance reports (pending, sebagian, lunas)
- ✅ Payment history dengan filters
- ✅ Billing reminders (overdue tracking)

---

## 🏗️ Arsitektur Sistem

```
kost-app/
├── app/
│   ├── Console/
│   │   └── Commands/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── RoomOccupancyController.php
│   │   │   ├── BillingController.php
│   │   │   ├── PaymentController.php
│   │   │   ├── AddonTransactionController.php
│   │   │   └── [Other Controllers]
│   │   ├── Middleware/
│   │   ├── Requests/
│   │   └── Kernel.php
│   ├── Models/
│   │   ├── Room.php
│   │   ├── Consumer.php
│   │   ├── RoomOccupancy.php
│   │   ├── Billing.php
│   │   ├── BillingDetail.php
│   │   ├── Payment.php
│   │   ├── RoomAddon.php
│   │   ├── AddonTransaction.php
│   │   └── [Other Models]
│   ├── Services/
│   │   ├── BillingService.php
│   │   ├── InvoiceService.php
│   │   └── ReminderService.php
│   └── Providers/
│       ├── AppServiceProvider.php
│       └── [Other Providers]
├── routes/
│   ├── web.php
│   ├── api.php
│   └── auth.php
├── resources/
│   ├── views/
│   │   ├── layouts/app.blade.php
│   │   ├── rooms/
│   │   ├── consumers/
│   │   ├── occupancies/
│   │   ├── billings/
│   │   ├── payments/
│   │   ├── addon_transactions/
│   │   └── reports/
│   ├── css/
│   └── js/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── config/
│   ├── adminlte.php
│   ├── app.php
│   └── [Other Configs]
└── tests/
    ├── Feature/
    └── Unit/
```

---

## 💾 Database Schema

### **rooms**
```sql
id | nomor_kamar | jenis_kamar | status | harga_bulanan | harga_harian | created_at
```
- Status: `tersedia`, `terisi`, `maintenance`

### **consumers**
```sql
id | nama | nik | no_hp | alamat | status | created_at
```

### **room_occupancies**
```sql
id | room_id | consumer_id | tanggal_masuk | tanggal_keluar | status | created_at
```
- Status: `aktif`, `tidak aktif`

### **billings**
```sql
id | consumer_id | room_id | invoice_number | periode_awal | periode_akhir 
| total_tagihan | status | created_at
```
- Status: `pending`, `sebagian`, `lunas`
- invoice_number format: INV-YYYYMMDDxxxxx

### **billing_details**
```sql
id | billing_id | keterangan | qty | harga | subtotal | created_at
```

### **payments**
```sql
id | billing_id | tanggal_bayar | jumlah | metode | bukti_bayar | created_at
```
- Metode: `tunai`, `transfer`, `cek`

### **room_addons** (Master Data)
```sql
id | nama_addon | harga | created_at
```

### **addon_transactions**
```sql
id | consumer_id | room_id | invoice_number | tanggal | status | total | catatan | created_at
```
- Status: `pending`, `posted`, `canceled`
- (Historical tracking, addon details langsung merge ke billing)

### **addon_transaction_details**
```sql
id | addon_transaction_id | addon_id | nama_addon | qty | harga | subtotal
```

### **billing_reminders**
```sql
id | billing_id | days_overdue | is_sent | created_at
```

---

## 🎮 Module & Controller

### **RoomOccupancyController**
**Routes:**
- `GET /occupancies` - List dengan visual seat map, filters
- `POST /occupancies` - Create occupancy, auto-generate billing
- `GET /occupancies/{id}/edit` - Edit form
- `POST /occupancies/{id}` - Update
- `GET /occupancies/{id}/complete` - Mark as selesai (inactive room)

**Logic:**
- Color-coded cards berdasar: expired, due_soon_unpaid, due_soon, occupied, available
- Compute paid/remaining untuk setiap occupancy
- Check 5 hari before checkout → flag due_soon
- Modal dengan tombol: Billing, Tambah Addon, Selesai Sewa, Edit, Kirim WA

### **BillingController**
**Routes:**
- `GET /billings` - List dengan search, status, date range, pagination
- `GET /billings/{id}` - Detail with rincian items

**Logic:**
- Filter: search (invoice/penyewa), status, start_date, end_date
- Pagination: 15 per page
- Show reminder badge jika overdue

### **PaymentController**
**Routes:**
- `GET /payments/create?billing=ID` - Form with billing detail preview
- `POST /payments` - Record payment
- `GET /payments` - List history dengan filters, pagination

**Logic:**
- Show billing info: invoice, consumer, kamar, total, paid, remaining
- Detail table: keterangan, qty, harga, subtotal
- Footer summary: Total Tagihan, Sudah Bayar, Sisa Tagihan
- Validation: payment ≤ remaining
- Auto-update billing status to `lunas` if fully paid

### **AddonTransactionController**
**Routes:**
- `GET /addon-transactions` - List billing (not separate AT), with paid/remaining
- `GET /addon-transactions/create` - Form dengan consumer picker
- `POST /addon-transactions` - Add addon items directly to billing
- `GET /addon-transactions/consumer/{id}/active-room` - JSON endpoint

**Logic:**
- Index: show billing list (not AT), filters: search, status, date range
- Create: pick consumer → auto-load active billing → add addon items → merge to billing
- No separate AT invoices; addon details merged to existing billing
- Update billing total & status

---

## 🔗 API Endpoints

### **JSON Endpoints**
```
GET /addon-transactions/consumer/{consumer}/active-room
Response:
{
  "billing": {
    "id": 14,
    "invoice_number": "INV-20251223-00014",
    "total_tagihan": 500000,
    "status": "sebagian"
  }
}
```

---

## 🔄 Workflow Proses

### **1. Occupancy Lifecycle**
```
Buat Occupancy
  ↓
Auto-generate Billing (INV-xxx)
  ↓
Set Room status = terisi
  ↓
[Penyewa aktif]
  ↓
Kirim notifikasi WA (tagihan)
  ↓
[≤5 hari before checkout] → Card kuning
  ↓
Jika ada tagihan belum lunas → Card merah, tombol Billing & Kirim WA
  ↓
Bayar → Payment recorded → Auto-update status
  ↓
Jika lunas → Card biru, tombol Kirim WA (booking reminder)
  ↓
Selesai Sewa → Mark inactive, room = tersedia
```

### **2. Addon Charges Flow**
```
Pilih Consumer di Form Addon
  ↓
Auto-load active Billing (INV-xxx)
  ↓
Input addon items (qty, harga)
  ↓
Submit → Merge details ke existing Billing
  ↓
Update Billing total + status
  ↓
Redirect ke payments.create dengan billing ID
  ↓
Catat pembayaran
```

### **3. Payment Recording**
```
Klik "Bayar" di billing
  ↓
Show form dengan billing detail + paid/remaining
  ↓
Input payment amount (≤remaining)
  ↓
Submit → Record payment
  ↓
Auto-check: if total_paid >= total_tagihan → status = lunas
  ↓
Redirect ke billing detail dengan success message
```

### **4. WhatsApp Notifications**
```
Status = Pending/Sebagian:
  → Kirim: Invoice, Total, Sisa, Days to checkout
  → Call-to-action: Segera bayar

Status = Lunas:
  → Kirim: Congratulations, Days to checkout
  → Call-to-action: Booking/perpanjang sesi berikutnya
```

---

## 🚀 Instalasi & Setup

### **Requirements**
- PHP 8.1+
- Composer
- MySQL 5.7+
- Node.js (optional, untuk assets)

### **Setup Steps**

1. **Clone & Install**
   ```bash
   cd c:\laragon\www\kost-app
   composer install
   npm install
   ```

2. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

4. **Serve**
   ```bash
   php artisan serve
   ```

5. **Access**
   - Local: `http://localhost:8000`
   - Production: `https://kost.vespahobby.xyz`

### **Key Configuration**
- Database: `.env` (DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD)
- Mail: `.env` (untuk email notifikasi, optional)
- Pagination: `AppServiceProvider.php` (Paginator::useBootstrap())

---

## 📊 Fitur Lanjutan

### **Billing Reminders**
- Service: `BillingService::generateReminders()`
- Deteksi overdue (>0 days)
- Track is_sent untuk mencegah spam

### **Invoice PDF**
- Service: `InvoiceService::generateInvoiceHtml()`
- Download sebagai PDF
- Used in: BillingController::downloadInvoice()

### **Role & Permission**
- Uses: `RolePermissionSeeder`
- Menu filtering berdasar user role
- AdminLTE integration

---

## 🔐 Security Notes

- ✅ Input validation (FormRequest)
- ✅ Authorization checks (Controller methods)
- ✅ CSRF protection
- ✅ SQL injection prevention (Eloquent ORM)
- ⚠️ WhatsApp API: Direct HTTP redirect (no API key stored)

---

## 📝 Notes untuk Development

1. **Pagination**: Semua list view pakai `paginate(15).withQueryString()` untuk preserve filters
2. **Date Format**: Use `Y-m-d` untuk input, format display dengan Carbon
3. **Phone Format**: Strip ke 62xxx... untuk WhatsApp API
4. **Color Codes**:
   - Green (#28a745): Available
   - Blue (#007bff): Occupied, lunas
   - Yellow (#f8c146): Due soon (≤5 days)
   - Red (#dc3545): Warning (tagihan + due soon)
   - Gray (#6c757d): Expired
5. **Invoice Numbering**: Format INV-YYYYMMDDxxxxx, auto-increment per hari

---

**Last Updated:** 23 December 2025  
**Version:** 1.0  
**Status:** Production Ready
