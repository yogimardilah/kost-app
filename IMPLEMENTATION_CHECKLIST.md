# ✅ DETAILED CHECKLIST - KOST APP IMPLEMENTATION

## 📌 How to Use This Checklist
- [ ] = Not Started
- [x] = Completed
- [~] = In Progress
- Copy this file and track progress locally

---

## 🔴 PHASE 1: AUTH & ROLE SYSTEM (Days 1-2)

### Foundation: Middleware & Authorization
- [ ] **Create CheckRole Middleware**
  - [ ] File: `app/Http/Middleware/CheckRole.php`
  - [ ] Handle multiple roles check
  - [ ] Redirect unauthorized requests
  - [ ] Test: admin can access admin routes, owner cannot

- [ ] **Update User Model**
  - [ ] Add `role_id` to fillable
  - [ ] Create `belongsTo(Role)` relation
  - [ ] Add methods: `hasRole($role)`, `isAdmin()`, `isOwner()`
  - [ ] Test: Auth::user()->hasRole('admin')

- [ ] **Update Role Model**
  - [ ] Create `hasMany(User)` relation
  - [ ] Add seeder data (Admin, Owner)
  - [ ] Test: $role->users->count() returns users with that role

- [ ] **Protect Routes**
  - [ ] Edit `routes/web.php`
  - [ ] Wrap routes with role middleware
  - [ ] Admin routes: `/admin/*`, `/roles/*`
  - [ ] Owner routes: `/kost/*`, `/billing/*`
  - [ ] Test: Access denied for wrong role

- [ ] **Frontend Authorization**
  - [ ] Edit `resources/views/layouts/navigation.blade.php`
  - [ ] Hide menu items based on role
  - [ ] Use `@if(Auth::user()->isAdmin())` blade directive
  - [ ] Test: Menu shows/hides correctly per role

---

## 👤 PHASE 1: CONSUMER MODULE (Days 2-3)

### Database
- [ ] **Create consumer_vehicles Migration**
  - [ ] File: `database/migrations/2025_12_17_*_create_consumer_vehicles_table.php`
  - [ ] Columns: id, consumer_id, jenis_kendaraan, nomor_polisi, created_at, updated_at
  - [ ] Foreign key: consumer_id → consumers(id) on delete cascade
  - [ ] Run: `php artisan migrate`
  - [ ] Verify in PostgreSQL

- [ ] **Update consumers Migration**
  - [ ] Check NIK column type (should be string)
  - [ ] Check no_hp column type
  - [ ] Add unique constraint on NIK
  - [ ] Drop old kendaraan column if exists

### Models
- [ ] **Create ConsumerVehicle Model**
  - [ ] File: `app/Models/ConsumerVehicle.php`
  - [ ] Fillable: consumer_id, jenis_kendaraan, nomor_polisi
  - [ ] Relation: `belongsTo(Consumer)`
  - [ ] Test: Model::create() works

- [ ] **Update Consumer Model**
  - [ ] Remove `kendaraan` from fillable
  - [ ] Add relation: `hasMany(ConsumerVehicle)`
  - [ ] Update fillable: nik, nama, no_hp, alamat
  - [ ] Add: `$table->string('nik')->unique();`

### Controllers
- [ ] **Create StoreConsumerRequest**
  - [ ] File: `app/Http/Requests/StoreConsumerRequest.php`
  - [ ] Rules:
    ```php
    'nik' => 'required|unique:consumers|numeric|digits:16',
    'nama' => 'required|string|max:255',
    'no_hp' => 'required|regex:/^62/',
    'alamat' => 'required|string|max:500'
    ```
  - [ ] Test: Invalid NIK rejected

- [ ] **Create UpdateConsumerRequest**
  - [ ] File: `app/Http/Requests/UpdateConsumerRequest.php`
  - [ ] Same rules but ignore current consumer

- [ ] **Complete ConsumerController**
  - [ ] Use Form Requests
  - [ ] Implement all CRUD methods
  - [ ] Add error handling
  - [ ] Methods:
    - [ ] index() - list consumers
    - [ ] create() - show create form
    - [ ] store() - save with validation
    - [ ] show() - detail + vehicles
    - [ ] edit() - show edit form
    - [ ] update() - update with validation
    - [ ] destroy() - delete

- [ ] **Create ConsumerVehicleController**
  - [ ] File: `app/Http/Controllers/ConsumerVehicleController.php`
  - [ ] Nested under consumer: `/consumers/{consumer}/vehicles`
  - [ ] Methods: index, create, store, edit, update, destroy

### Views
- [ ] **Create consumer views directory**
  - [ ] `resources/views/consumers/`

- [ ] **Create index.blade.php**
  - [ ] Table: NIK, Nama, No HP, Alamat, Action
  - [ ] Add: Create button
  - [ ] Search functionality (optional)
  - [ ] Display vehicle count

- [ ] **Create create.blade.php**
  - [ ] Form: NIK, Nama, No HP, Alamat
  - [ ] Submit button
  - [ ] Cancel link

- [ ] **Create edit.blade.php**
  - [ ] Same form as create
  - [ ] Pre-fill existing data

- [ ] **Create show.blade.php**
  - [ ] Consumer details
  - [ ] Vehicles list
  - [ ] Add vehicle button
  - [ ] Edit/Delete consumer buttons

- [ ] **Create vehicles folder**
  - [ ] `resources/views/consumers/vehicles/`
  - [ ] index.blade.php - table vehicles
  - [ ] create.blade.php - add vehicle form
  - [ ] edit.blade.php - edit vehicle form

### Routes
- [ ] **Add routes to web.php**
  ```php
  Route::middleware('auth')->group(function () {
      Route::resource('consumers', ConsumerController::class);
      Route::resource('consumers.vehicles', ConsumerVehicleController::class);
  });
  ```

### Testing
- [ ] Test: Create consumer with valid data
- [ ] Test: Reject invalid NIK (not 16 digits)
- [ ] Test: Reject duplicate NIK
- [ ] Test: Add vehicle to consumer
- [ ] Test: List consumers shows all
- [ ] Test: Edit consumer updates data
- [ ] Test: Delete consumer removes records

---

## 🏠 PHASE 1: ROOM MODULE (Days 3-4)

### Models
- [ ] **Update Room Model**
  - [ ] Verify relations:
    ```php
    belongsTo(Kost)
    hasMany(RoomOccupancy)
    belongsToMany(RoomAddon)
    hasMany(Billing)
    ```
  - [ ] Add accessor: `isAvailable()`
  - [ ] Add mutator for status

### Controllers
- [ ] **Create StoreRoomRequest**
  - [ ] Rules:
    ```php
    'nomor_kamar' => 'required|unique:rooms',
    'jenis_kamar' => 'required|in:single,double,suite',
    'harga' => 'required|numeric|min:50000',
    'kost_id' => 'required|exists:kosts,id',
    'status' => 'required|in:tersedia,terisi'
    ```

- [ ] **Update RoomController**
  - [ ] Use Form Requests
  - [ ] Fix index() - remove kostId parameter requirement
  - [ ] Complete all CRUD methods
  - [ ] Add proper error handling
  - [ ] Methods:
    - [ ] index() - list all rooms or by kost
    - [ ] create() - show form
    - [ ] store() - save
    - [ ] show() - detail
    - [ ] edit() - show form
    - [ ] update() - update
    - [ ] destroy() - delete (only if no occupancy)

### Views
- [ ] **Create room views directory**
  - [ ] `resources/views/rooms/`

- [ ] **Create index.blade.php**
  - [ ] Table: Nomor, Jenis, Harga, Status, Action
  - [ ] Filter by status
  - [ ] Create button
  - [ ] Status badge (tersedia=green, terisi=red)

- [ ] **Create create.blade.php**
  - [ ] Form: nomor_kamar, jenis_kamar, harga, kost_id
  - [ ] Dropdown for kost selection
  - [ ] Initial status: tersedia

- [ ] **Create edit.blade.php**
  - [ ] Same as create with pre-filled data

- [ ] **Create show.blade.php**
  - [ ] Room details
  - [ ] Current occupancy info
  - [ ] Billing history
  - [ ] Add-ons assigned
  - [ ] Edit/Delete buttons

### Routes
- [ ] Add routes to web.php (already done, verify)

### Testing
- [ ] Test: Create room with valid data
- [ ] Test: Reject duplicate nomor_kamar
- [ ] Test: Invalid harga rejected
- [ ] Test: List shows all rooms
- [ ] Test: Status badge displays correctly
- [ ] Test: Cannot delete room with active occupancy

---

## 🟡 PHASE 2: BILLING & PAYMENT (Days 5-7)

### Models
- [ ] **Create Billing Model**
  - [ ] File: `app/Models/Billing.php`
  - [ ] Relations:
    ```php
    belongsTo(Consumer)
    belongsTo(Room)
    hasMany(BillingDetail)
    hasMany(Payment)
    ```
  - [ ] Scopes: `unpaid()`, `paid()`, `overdue()`
  - [ ] Methods: `calculateTotal()`, `markAsPaid()`

- [ ] **Create BillingDetail Model**
  - [ ] File: `app/Models/BillingDetail.php`
  - [ ] Relations:
    ```php
    belongsTo(Billing)
    belongsTo(RoomAddon, 'addon_id')
    ```
  - [ ] Fillable: billing_id, addon_id, harga, qty

- [ ] **Create Payment Model**
  - [ ] File: `app/Models/Payment.php`
  - [ ] Relations: `belongsTo(Billing)`
  - [ ] Statuses: pending, verified, rejected
  - [ ] Fillable: billing_id, metode, bukti_foto, tanggal_bayar, status

- [ ] **Create/Update RoomAddon Model**
  - [ ] File: `app/Models/RoomAddon.php`
  - [ ] Relations:
    ```php
    hasMany(BillingDetail)
    belongsToMany(Room)
    ```
  - [ ] Fillable: nama_jasa, harga, satuan, keterangan

### Controllers
- [ ] **Create Form Requests**
  - [ ] StoreBillingRequest
  - [ ] StorePaymentRequest
  - [ ] VerifyPaymentRequest

- [ ] **Create BillingController**
  - [ ] File: `app/Http/Controllers/BillingController.php`
  - [ ] Methods:
    - [ ] index() - list billing
    - [ ] create() - form
    - [ ] store() - save + calculate total
    - [ ] show() - detail
    - [ ] edit() - edit if unpaid
    - [ ] update() - update if unpaid
    - [ ] destroy() - delete if unpaid

- [ ] **Create PaymentController**
  - [ ] File: `app/Http/Controllers/PaymentController.php`
  - [ ] Methods:
    - [ ] store() - submit payment
    - [ ] verify() - admin verify
    - [ ] reject() - admin reject

- [ ] **Create RoomAddonController**
  - [ ] File: `app/Http/Controllers/RoomAddonController.php`
  - [ ] Complete CRUD implementation

### Views
- [ ] **Create billing views**
  - [ ] `resources/views/billings/`
  - [ ] index.blade.php
  - [ ] create.blade.php
  - [ ] edit.blade.php
  - [ ] show.blade.php (with items & payments)
  - [ ] payment-form.blade.php

- [ ] **Create payment views**
  - [ ] `resources/views/payments/`
  - [ ] verify-pending.blade.php (admin)
  - [ ] payment-receipt.blade.php (user)

- [ ] **Create addon views**
  - [ ] `resources/views/addons/`
  - [ ] index, create, edit, show

### Logic
- [ ] **Auto-update Room Status**
  - [ ] After payment verified → room.status = 'terisi'
  - [ ] On consumer checkout → room.status = 'tersedia'

- [ ] **Calculate Total Billing**
  - [ ] Room harga + sum(addon harga)

- [ ] **Payment Verification**
  - [ ] Admin review payment upload
  - [ ] Approve → status = verified, room = terisi
  - [ ] Reject → status = rejected, allow resubmit

### Testing
- [ ] Test: Create billing calculates total
- [ ] Test: Add items to billing
- [ ] Test: Submit payment with file upload
- [ ] Test: Admin verify payment
- [ ] Test: Room status updates automatically
- [ ] Test: Cannot edit paid billing

---

## 📊 PHASE 3: DASHBOARD (Days 8-9)

### Backend
- [ ] **Create DashboardController**
  - [ ] File: `app/Http/Controllers/DashboardController.php`
  - [ ] Methods:
    - [ ] index() - main dashboard
    - [ ] getRevenueStats() - API endpoint
    - [ ] getRoomStats() - API endpoint
    - [ ] getOccupancyRate() - API endpoint

- [ ] **Dashboard Queries**
  - [ ] Total revenue (all time, this month, today)
  - [ ] Total rooms (total, occupied, available)
  - [ ] Occupancy percentage
  - [ ] Overdue payments count
  - [ ] Recent transactions

### Database
- [ ] **Add Indexes**
  - [ ] `billings`: (consumer_id, status, created_at)
  - [ ] `billings`: (room_id)
  - [ ] `payments`: (billing_id, status)
  - [ ] `room_occupancies`: (room_id, consumer_id)

### Frontend
- [ ] **Create dashboard.blade.php**
  - [ ] Stats cards layout
  - [ ] Charts area
  - [ ] Recent transactions table

- [ ] **Add Chart.js**
  - [ ] Install: `npm install chart.js`
  - [ ] Revenue trend chart (line)
  - [ ] Room status pie chart
  - [ ] Payment status bar chart

- [ ] **Components**
  - [ ] Stats card component
  - [ ] Chart component
  - [ ] Recent transactions widget

### Testing
- [ ] Test: Stats show correct numbers
- [ ] Test: Charts render data
- [ ] Test: API endpoints respond

---

## 📑 PHASE 3: REPORTS (Days 9-10)

### Backend
- [ ] **Create ReportController**
  - [ ] Methods:
    - [ ] transactionReport() - all transactions
    - [ ] revenueReport() - by date range
    - [ ] occupancyReport() - room usage

- [ ] **Excel Export**
  - [ ] Install: `composer require phpoffice/phpspreadsheet`
  - [ ] Create traits for Excel generation
  - [ ] Export transactions
  - [ ] Export revenue
  - [ ] Export occupancy

### Frontend
- [ ] **Create report views**
  - [ ] `resources/views/reports/`
  - [ ] transactions.blade.php
  - [ ] revenue.blade.php
  - [ ] occupancy.blade.php

- [ ] **Filter Forms**
  - [ ] Date range picker
  - [ ] Filter by room/consumer
  - [ ] Export button

### Testing
- [ ] Test: Generate report (view)
- [ ] Test: Export to Excel
- [ ] Test: Filter by date range

---

## 🟢 PHASE 4: WHATSAPP & ADVANCED (Days 11-15)

### WhatsApp Setup
- [ ] **Environment Setup**
  - [ ] Register API key
  - [ ] Configure `config/whatsapp.php`
  - [ ] Add to `.env`

- [ ] **Create Service**
  - [ ] File: `app/Services/WhatsAppService.php`
  - [ ] Methods: sendMessage(), sendInvoice()
  - [ ] Queue job: SendWhatsAppMessage

- [ ] **Notifications**
  - [ ] Create notification class
  - [ ] Use with Event/Listener pattern
  - [ ] Test: Send test message

### Invoice Delivery
- [ ] **Create Command**
  - [ ] `php artisan command:send-invoice`
  - [ ] Get unpaid billings
  - [ ] Queue messages
  - [ ] Log delivery

### Reminder System
- [ ] **Create Command**
  - [ ] `php artisan command:send-reminders`
  - [ ] Check overdue billings
  - [ ] Send 1 day before due
  - [ ] Log reminders sent

### Advanced Features
- [ ] **Occupancy Tracking**
  - [ ] Check-in/check-out dates
  - [ ] Duration calculation
  - [ ] Status history

- [ ] **Traffic Report**
  - [ ] Occupancy timeline
  - [ ] Churn analysis

### Testing
- [ ] Test: Send test WhatsApp message
- [ ] Test: Invoice notification
- [ ] Test: Reminder command
- [ ] Test: All features work together

---

## 🔒 SECURITY & QUALITY

- [ ] All inputs validated
- [ ] SQL injection prevented (using Eloquent)
- [ ] CSRF tokens on all forms
- [ ] Authorization checks on all endpoints
- [ ] Error messages don't expose sensitive info
- [ ] File uploads sanitized
- [ ] Sensitive data encrypted
- [ ] Logging implemented
- [ ] Rate limiting (optional)
- [ ] API authentication (Sanctum) if needed

---

## 📈 PERFORMANCE

- [ ] Database indexes added
- [ ] Eager loading used (with/load)
- [ ] N+1 queries eliminated
- [ ] Pagination implemented
- [ ] Caching considered
- [ ] Queue jobs for heavy operations
- [ ] Load testing done

---

## 🧪 TESTING

- [ ] Unit tests for models
- [ ] Feature tests for controllers
- [ ] Authorization tests
- [ ] Validation tests
- [ ] API response tests
- [ ] All tests passing

---

## 📚 DOCUMENTATION

- [ ] Code comments added
- [ ] README updated
- [ ] API endpoints documented
- [ ] Database schema documented
- [ ] Setup instructions clear

---

## 🚀 DEPLOYMENT

- [ ] Migrations run on server
- [ ] Environment variables set
- [ ] Assets compiled (npm run build)
- [ ] Database seeded
- [ ] Logs configured
- [ ] Backups configured
- [ ] Monitoring setup
- [ ] Go live!

---

**Progress**: Update this checklist as you complete items!
