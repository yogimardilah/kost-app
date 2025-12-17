# 🎯 ROADMAP IMPLEMENTASI - KOST MANAGEMENT SYSTEM

**Status Proyek**: 60-65% Complete  
**Last Updated**: 17 Desember 2025  
**Target**: MVP Production Ready

---

## 📊 PROGRESS OVERVIEW

| Module | Status | Progress | Priority |
|--------|--------|----------|----------|
| 🔐 Auth & Role | PARTIAL | 70% | 🔴 CRITICAL |
| 👤 Master Consumer | PARTIAL | 60% | 🔴 CRITICAL |
| 🏠 Master Kamar | PARTIAL | 65% | 🔴 CRITICAL |
| ➕ Add Ons | MINIMAL | 30% | 🟡 HIGH |
| 📊 Dashboard | NONE | 0% | 🟡 HIGH |
| 💰 Billing | MINIMAL | 20% | 🔴 CRITICAL |
| 📑 Report | NONE | 0% | 🟢 MEDIUM |
| 🔒 Security | PARTIAL | 40% | 🟡 HIGH |
| 📱 WhatsApp API | NONE | 0% | 🟢 MEDIUM |

---

## 🔴 PHASE 1: CRITICAL FOUNDATION (WEEK 1)
**Durasi**: 3-4 hari | **Impact**: Enable core operations

### 1.1 Auth & Role System ⚡
**Status**: 70% → 100%

#### Tasks:
- [ ] **Middleware CheckRole** - Role-based access control
  ```
  Location: app/Http/Middleware/CheckRole.php
  - Middleware untuk validasi role user
  - Redirect jika tidak authorized
  ```

- [ ] **Relasi User-Role** - Fix model relationships
  ```
  User Model:
  - belongsTo(Role)
  - hasMany(ConsumerProfile) [optional]
  
  Role Model:
  - hasMany(User)
  - Enum roles: ['Admin', 'Owner']
  ```

- [ ] **Role Middleware di Routes** - Proteksi menu
  ```
  Route::middleware(['auth', 'role:owner'])->group(...)
  Route::middleware(['auth', 'role:admin'])->group(...)
  ```

- [ ] **Frontend Authorization** - Menu based on role
  ```
  app/View/Components/AuthorizedMenu.php
  - Conditional menu items per role
  - Hide unauthorized links
  ```

**Files to Create/Edit**:
- [ ] `app/Http/Middleware/CheckRole.php` (NEW)
- [ ] `app/Models/User.php` (EDIT - add role relation)
- [ ] `app/Models/Role.php` (EDIT - add user relation)
- [ ] `routes/web.php` (EDIT - add middleware)
- [ ] `resources/views/layouts/navigation.blade.php` (EDIT)

---

### 1.2 Consumer Module Complete ⚡
**Status**: 60% → 100%

#### Tasks:
- [ ] **Migrasi Consumer Vehicles** - Create new table
  ```
  Table: consumer_vehicles
  - id, consumer_id, jenis_kendaraan, nomor_polisi, created_at
  - Foreign key: consumer_id → consumers
  ```

- [ ] **Model ConsumerVehicle** - New model
  ```
  ConsumerVehicle:
  - belongsTo(Consumer)
  
  Consumer:
  - hasMany(ConsumerVehicle)
  ```

- [ ] **ConsumerController CRUD** - Complete implementation
  ```
  - index() ✅
  - create() ✅
  - store() - Add validation
  - edit() - Implementation
  - update() - Implementation
  - destroy() - Implementation
  - show() - Detail dengan vehicles
  ```

- [ ] **ConsumerVehicleController** - Nested resource
  ```
  CRUD untuk kendaraan penghuni
  Routes:
  /consumers/{consumer}/vehicles
  /consumers/{consumer}/vehicles/{vehicle}
  ```

- [ ] **Form Request Validation** - Input validation
  ```
  StoreConsumerRequest:
  - NIK: required|unique:consumers|numeric|size:16
  - Nama: required|string|max:255
  - No HP: required|regex:/^62/|digits_between:10,12
  - Alamat: required|string|max:500
  
  StoreVehicleRequest:
  - Jenis: required|in:motor,mobil,sepeda
  - Nomor Polisi: required|unique:consumer_vehicles
  ```

- [ ] **Views/Templates** - Complete Blade templates
  ```
  resources/views/consumers/
  - index.blade.php (list dengan vehicle count)
  - create.blade.php (form input)
  - edit.blade.php (form update)
  - show.blade.php (detail + vehicles)
  - partials/form.blade.php
  - vehicles/index.blade.php
  - vehicles/create.blade.php
  ```

**Files to Create/Edit**:
- [ ] `database/migrations/2025_12_17_*_create_consumer_vehicles_table.php` (NEW)
- [ ] `app/Models/ConsumerVehicle.php` (NEW)
- [ ] `app/Models/Consumer.php` (EDIT - add vehicle relation)
- [ ] `app/Http/Controllers/ConsumerController.php` (EDIT - complete CRUD)
- [ ] `app/Http/Controllers/ConsumerVehicleController.php` (NEW)
- [ ] `app/Http/Requests/StoreConsumerRequest.php` (NEW)
- [ ] `app/Http/Requests/UpdateConsumerRequest.php` (NEW)
- [ ] `app/Http/Requests/StoreVehicleRequest.php` (NEW)
- [ ] `resources/views/consumers/*` (NEW - all views)

---

### 1.3 Room Module Complete ⚡
**Status**: 65% → 100%

#### Tasks:
- [ ] **RoomController Refactor** - Proper CRUD
  ```
  - index() - List by kost or all
  - create() - Form create
  - store() - Save with validation
  - edit() - Edit form
  - update() - Update room
  - destroy() - Delete room
  - show() - Detail room + occupancy
  ```

- [ ] **Room Model Relationships** - Complete relations
  ```
  Room:
  - belongsTo(Kost)
  - hasMany(RoomOccupancy)
  - belongsToMany(RoomAddon) [via pivot table]
  - hasMany(Billing)
  - Accessor: is_available (status check)
  ```

- [ ] **Form Request Validation**
  ```
  StoreRoomRequest:
  - nomor_kamar: required|unique:rooms|string
  - jenis_kamar: required|in:single,double,suite
  - harga: required|numeric|min:0
  - status: required|in:tersedia,terisi
  - kost_id: required|exists:kosts
  ```

- [ ] **Views Lengkap**
  ```
  resources/views/rooms/
  - index.blade.php
  - create.blade.php
  - edit.blade.php
  - show.blade.php (dengan occupancy timeline)
  - partials/form.blade.php
  - partials/status-badge.blade.php
  ```

**Files to Create/Edit**:
- [ ] `app/Http/Controllers/RoomController.php` (EDIT - complete implementation)
- [ ] `app/Http/Requests/StoreRoomRequest.php` (NEW)
- [ ] `app/Http/Requests/UpdateRoomRequest.php` (NEW)
- [ ] `app/Models/Room.php` (EDIT - add all relations)
- [ ] `resources/views/rooms/*` (NEW - all views)

---

### 1.4 Database & Model Relationships ⚡
**Status**: 50% → 100%

#### Tasks:
- [ ] **Migrasi Fixes** - Ensure foreign keys
  ```
  Check/Update:
  - users.role_id FK → roles.id
  - rooms.kost_id FK → kosts.id
  - room_occupancies.consumer_id FK
  - room_occupancies.room_id FK
  - billings.consumer_id, billings.room_id FK
  - billing_details.addon_id FK
  - payments.billing_id FK
  ```

- [ ] **Model Relationships Audit** - Complete all models
  ```
  Verify all models have:
  - belongsTo() relations
  - hasMany() relations
  - belongsToMany() if applicable
  - Proper fillable/guarded
  - Proper casts
  ```

**Files to Verify**:
- [ ] `database/migrations/*` - All migrations
- [ ] `app/Models/*` - All models

---

## 🟡 PHASE 2: BILLING & TRANSACTIONS (WEEK 2)
**Durasi**: 3-4 hari | **Impact**: Revenue tracking

### 2.1 Models & Relationships
- [ ] **Model Billing** - Main billing entity
  ```
  Billing:
  - belongsTo(Consumer)
  - belongsTo(Room)
  - hasMany(BillingDetail)
  - hasMany(Payment)
  - Scopes: unpaid(), paid(), overdue()
  - Methods: calculateTotal(), markAsPaid()
  ```

- [ ] **Model BillingDetail** - Line items
  ```
  BillingDetail:
  - belongsTo(Billing)
  - belongsTo(RoomAddon)
  ```

- [ ] **Model Payment** - Payment records
  ```
  Payment:
  - belongsTo(Billing)
  - Statuses: pending, verified, rejected
  ```

- [ ] **Model RoomAddon** - Add-on services
  ```
  RoomAddon:
  - hasMany(BillingDetail)
  - belongsToMany(Room) [via pivot]
  ```

### 2.2 BillingController & Logic
- [ ] **BillingController CRUD**
  ```
  - index() - List billing per consumer/room
  - create() - Form create
  - store() - Create billing + auto-calculate
  - show() - Detail with items & payments
  - edit() - Edit (if unpaid)
  - update() - Update (if unpaid)
  - destroy() - Delete (if unpaid)
  ```

- [ ] **Payment Processing**
  ```
  - submitPayment() - Upload bukti
  - verifyPayment() - Admin verify
  - rejectPayment() - Admin reject
  - markAsPaid() - After verification
  ```

- [ ] **Room Status Auto-Update**
  ```
  After billing created:
  - If status='paid' → room.status = 'terisi'
  - If consumer checkout → room.status = 'tersedia'
  ```

### 2.3 Views
- [ ] `resources/views/billings/*` - All billing views
  - index, create, edit, show
  - payment form
  - invoice template

### 2.4 Validation & Form Requests
- [ ] `StoreBillingRequest` - Validation rules
- [ ] `StorePaymentRequest` - Payment validation
- [ ] `VerifyPaymentRequest` - Verification rules

---

## 🟡 PHASE 3: DASHBOARD & REPORTS (WEEK 2-3)
**Durasi**: 2-3 hari | **Impact**: Business insights

### 3.1 Dashboard Backend
- [ ] **DashboardController**
  ```
  API endpoints:
  - getRevenueStat() - Daily/Monthly revenue
  - getRoomStatus() - Occupied/Available
  - getOccupancyRate() - % rooms occupied
  - getRecentPayments() - Last 10 payments
  - getOverduePayments() - Past due
  ```

- [ ] **Query Optimization**
  ```
  - Index pada:
    billings: consumer_id, room_id, status, created_at
    payments: billing_id, status
    room_occupancies: room_id, consumer_id
  ```

### 3.2 Dashboard Frontend
- [ ] **Chart.js Integration**
  ```
  - Revenue trend chart (line)
  - Room status pie chart
  - Payment status breakdown
  - Occupancy timeline
  ```

- [ ] **Dashboard Layout**
  ```
  views/dashboard.blade.php:
  - Stats cards (revenue, rooms, occupancy)
  - Charts area
  - Recent transactions table
  - Quick actions
  ```

### 3.3 Report Module
- [ ] **ReportController**
  ```
  - transactionReport() - Filter by date
  - revenueReport() - Income per period
  - occupancyReport() - Room usage
  ```

- [ ] **Excel Export**
  ```
  - Install PhpSpreadsheet
  - Export transactions
  - Export revenue
  - Export room status
  ```

---

## 🟢 PHASE 4: ADVANCED FEATURES (WEEK 3-4)
**Durasi**: 3-4 hari | **Impact**: Automation & integration

### 4.1 WhatsApp Integration
- [ ] **WhatsApp API Setup**
  ```
  Service:
  - Twilio or official WhatsApp API
  - Config: API key, sender number
  - Queue jobs untuk bulk sending
  ```

- [ ] **Invoice Delivery**
  ```
  Command: send:invoice-notification
  - Send invoice ke penghuni
  - Include payment link
  - Log pengiriman
  ```

- [ ] **Reminder System**
  ```
  Command: send:due-reminder
  - Cron: Daily check overdue
  - Send reminder 1 hari before due
  - Log resend count
  ```

- [ ] **Payment Notification**
  ```
  Event: PaymentReceived
  - Notify consumer when payment verified
  - Notify admin for new payment
  ```

### 4.2 Traffic/Occupancy Tracking
- [ ] **RoomOccupancy Enhancement**
  ```
  Log:
  - Check-in date
  - Check-out date
  - Duration
  - Status history
  ```

- [ ] **Traffic Report**
  ```
  - Monthly occupancy timeline
  - Average stay duration
  - Churn/vacancy analysis
  ```

### 4.3 Testing & Documentation
- [ ] **Unit Tests**
  ```
  - Model tests
  - Controller tests
  - Validation tests
  ```

- [ ] **API Documentation**
  ```
  - Routes documentation
  - Authorization rules
  - Response formats
  ```

---

## 📋 DETAILED CHECKLIST BY PRIORITY

### 🔴 CRITICAL (MUST DO - Week 1)

- [ ] Create middleware `CheckRole.php`
- [ ] Update User model with role relation
- [ ] Create consumer_vehicles migration
- [ ] Create ConsumerVehicle model
- [ ] Create Form Requests (Consumer, Room, Vehicle)
- [ ] Complete ConsumerController CRUD
- [ ] Complete RoomController CRUD
- [ ] Create all consumer views
- [ ] Create all room views
- [ ] Test all CRUD operations
- [ ] Add auth routes protection
- [ ] Fix all model relationships

**Estimated Time**: 30-35 hours  
**Team Size**: 1-2 developers

---

### 🟡 HIGH PRIORITY (Week 2)

- [ ] Create Billing model & controller
- [ ] Create Payment model & controller
- [ ] Create RoomAddon complete implementation
- [ ] Billing validation rules
- [ ] Auto-update room status logic
- [ ] Billing views (create, show, payment form)
- [ ] Payment verification system
- [ ] DashboardController with stats
- [ ] Dashboard views with cards
- [ ] Database indexing

**Estimated Time**: 25-30 hours  
**Team Size**: 1-2 developers

---

### 🟢 MEDIUM PRIORITY (Week 2-3)

- [ ] Chart.js integration
- [ ] Report module backend
- [ ] Report views
- [ ] Excel export setup
- [ ] WhatsApp API integration
- [ ] Invoice notification queue
- [ ] Reminder system setup

**Estimated Time**: 20-25 hours  
**Team Size**: 1-2 developers

---

## 🎓 IMPLEMENTATION TIPS

### Code Quality
```
✅ Use Form Request Validation
✅ Use Middleware for authorization
✅ Create API Resources for responses
✅ Use Eloquent scopes
✅ Add proper error handling
✅ Use transactions for financial operations
```

### Database
```
✅ Add indexes on frequently queried columns
✅ Use soft deletes for critical data
✅ Add timestamps (created_at, updated_at)
✅ Foreign key constraints
✅ Use migrations for schema changes
```

### Testing
```
✅ Test authorization (middleware)
✅ Test validation rules
✅ Test CRUD operations
✅ Test business logic
✅ Test API responses
```

---

## 📅 TIMELINE ESTIMATE

| Phase | Duration | Target Date |
|-------|----------|-------------|
| Phase 1 (Critical) | 3-4 days | Dec 20 |
| Phase 2 (Billing) | 3-4 days | Dec 23 |
| Phase 3 (Dashboard) | 2-3 days | Dec 26 |
| Phase 4 (Advanced) | 3-4 days | Dec 30 |
| **Total MVP** | **12-15 days** | **Jan 2** |

---

## 🚀 DEPLOYMENT CHECKLIST

Before going to production:
- [ ] All tests passing
- [ ] Security validation complete
- [ ] Database migrated
- [ ] Env variables configured
- [ ] Error logging setup
- [ ] WhatsApp API configured
- [ ] Backup system ready
- [ ] Performance optimized

---

**Last Note**: Focus on Phase 1 first. Don't move to Phase 2 until Phase 1 is 100% complete. Quality > Speed!
