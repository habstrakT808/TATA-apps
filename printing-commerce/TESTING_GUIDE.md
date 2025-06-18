# 🧪 TESTING GUIDE - Tata Printing API

## 📋 Overview
Guide ini akan membantu testing API dengan urutan workflow yang benar menggunakan Postman setelah seeder diperbaiki.

## 🗂️ Data Structure Setelah Seeder
Setelah menjalankan `php artisan db:seed`, kamu akan mendapatkan data dengan struktur yang berurutan:

### 📊 Pesanan Distribution
- **15 pesanan** - Status: `pending` (baru, belum bayar)
- **20 pesanan** - Status: `menunggu_konfirmasi` (sudah upload bukti bayar)
- **25 pesanan** - Status: `dikerjakan` (sudah lunas, sedang dikerjakan)
- **20 pesanan** - Status: `revisi` (sedang dalam proses revisi)
- **15 pesanan** - Status: `selesai` (completed dengan revisi history)
- **5 pesanan** - Status: `dibatalkan` (cancelled)

### 💳 Transaksi
- Otomatis dibuat untuk pesanan yang membutuhkan (status: menunggu_konfirmasi, dikerjakan, revisi, selesai)
- Order ID format: `ORD-YYYYMMDD-XXXXXX`
- Bukti pembayaran: `payment_proof_1.jpg` sampai `payment_proof_5.jpg`

### 🔄 Revisi
- Pesanan dengan status `revisi`: ada 1-2 revisi ongoing
- Pesanan dengan status `selesai`: ada 1-3 revisi complete history
- Pesanan dengan status `dikerjakan`: ada initial revisi files

---

## 🚀 TESTING WORKFLOW

### 1️⃣ **AUTHENTICATION**

#### Login User
```http
POST {{base_url}}/api/mobile/login
Content-Type: application/json

{
    "email": "user1@gmail.com",
    "password": "12345678"
}
```

#### Login Admin
```http
POST {{base_url}}/api/admin/login
Content-Type: application/json

{
    "email": "admin@gmail.com",
    "password": "12345678"
}
```

---

### 2️⃣ **ORDER MANAGEMENT WORKFLOW**

#### A. Create New Order (User)
```http
POST {{base_url}}/api/mobile/pesanan
Authorization: Bearer {{user_token}}
Content-Type: application/json

{
    "id_jasa": 1,
    "id_paket_jasa": 1,
    "deskripsi": "Logo design untuk startup teknologi",
    "catatan_tambahan": "Minta yang minimalis dan modern",
    "files": [] // Upload files if any
}
```

#### B. Get User Orders (User)
```http
GET {{base_url}}/api/mobile/pesanan
Authorization: Bearer {{user_token}}

# Filter by status
GET {{base_url}}/api/mobile/pesanan?status=pending
GET {{base_url}}/api/mobile/pesanan?status=dikerjakan
```

#### C. Get Order Detail (User)
```http
GET {{base_url}}/api/mobile/pesanan/{{pesanan_uuid}}
Authorization: Bearer {{user_token}}
```

---

### 3️⃣ **PAYMENT WORKFLOW**

#### A. Create Payment Transaction (User)
```http
POST {{base_url}}/api/mobile/transaksi
Authorization: Bearer {{user_token}}
Content-Type: application/json

{
    "pesanan_uuid": "{{pesanan_uuid}}",
    "id_metode_pembayaran": 1
}
```

#### B. Upload Payment Proof (User)
```http
POST {{base_url}}/api/mobile/transaksi/upload-bukti/{{order_id}}
Authorization: Bearer {{user_token}}
Content-Type: multipart/form-data

bukti_pembayaran: [file]
```

#### C. Get Pending Payments (Admin)
```http
GET {{base_url}}/api/admin/payments/pending
Authorization: Bearer {{admin_token}}
```

#### D. Confirm Payment (Admin)
```http
POST {{base_url}}/api/admin/payments/confirm
Authorization: Bearer {{admin_token}}
Content-Type: application/json

{
    "order_id": "{{order_id}}",
    "admin_notes": "Pembayaran sudah dikonfirmasi"
}
```

#### E. Reject Payment (Admin)
```http
POST {{base_url}}/api/admin/payments/reject
Authorization: Bearer {{admin_token}}
Content-Type: application/json

{
    "order_id": "{{order_id}}",
    "alasan_penolakan": "Bukti pembayaran tidak jelas"
}
```

---

### 4️⃣ **ORDER PROCESSING WORKFLOW**

#### A. Get All Orders (Admin)
```http
GET {{base_url}}/api/admin/pesanan
Authorization: Bearer {{admin_token}}

# With filters
GET {{base_url}}/api/admin/pesanan?status=menunggu_konfirmasi
GET {{base_url}}/api/admin/pesanan?search=user_name
```

#### B. Assign Editor to Order (Admin)
```http
POST {{base_url}}/api/admin/pesanan/{{pesanan_uuid}}/assign-editor
Authorization: Bearer {{admin_token}}
Content-Type: application/json

{
    "id_editor": 1
}
```

#### C. Update Order Status (Admin)
```http
PUT {{base_url}}/api/admin/pesanan/{{pesanan_uuid}}/status
Authorization: Bearer {{admin_token}}
Content-Type: application/json

{
    "status": "dikerjakan"
}
```

---

### 5️⃣ **REVISION WORKFLOW**

#### A. Request Revision (User)
```http
POST {{base_url}}/api/mobile/pesanan/{{pesanan_uuid}}/revisi
Authorization: Bearer {{user_token}}
Content-Type: multipart/form-data

catatan_revisi: "Tolong ubah warna background menjadi lebih terang"
files[]: [file1, file2] // Optional revision files
```

#### B. Get Revision History (User)
```http
GET {{base_url}}/api/mobile/pesanan/{{pesanan_uuid}}/revision-history
Authorization: Bearer {{user_token}}
```

#### C. Approve Revision (User)
```http
POST {{base_url}}/api/mobile/pesanan/{{pesanan_uuid}}/approve-revision/{{revision_uuid}}
Authorization: Bearer {{user_token}}
```

#### D. Accept Final Work (User)
```http
POST {{base_url}}/api/mobile/pesanan/{{pesanan_uuid}}/accept
Authorization: Bearer {{user_token}}
```

---

### 6️⃣ **EDITOR WORKFLOW** (If available)

#### A. Get Assigned Orders (Editor)
```http
GET {{base_url}}/api/editor/pesanan
Authorization: Bearer {{editor_token}}
```

#### B. Upload Work Result (Editor)
```http
POST {{base_url}}/api/editor/pesanan/{{pesanan_uuid}}/upload
Authorization: Bearer {{editor_token}}
Content-Type: multipart/form-data

type: "preview" // or "final"
notes: "Preview awal untuk review"
files[]: [file1, file2]
```

---

## 🎯 **TESTING SCENARIOS**

### Scenario 1: Complete Happy Path
1. User creates new order ✅
2. User creates payment transaction ✅
3. User uploads payment proof ✅
4. Admin confirms payment ✅
5. Admin assigns editor ✅
6. Editor uploads preview ✅
7. User requests revision ✅
8. Editor uploads final work ✅
9. User accepts final work ✅

### Scenario 2: Payment Rejection
1. User creates order ✅
2. User uploads invalid payment proof ✅
3. Admin rejects payment ❌
4. User uploads correct payment proof ✅
5. Admin confirms payment ✅

### Scenario 3: Multiple Revisions
1. Order in progress ✅
2. User requests revision #1 ✅
3. Editor responds with preview ✅
4. User requests revision #2 ✅
5. Editor responds with final ✅
6. User accepts work ✅

### Scenario 4: Order Cancellation
1. User creates order ✅
2. User cancels before payment ✅
3. Check order status = "dibatalkan" ✅

---

## 📝 **POSTMAN COLLECTION VARIABLES**

Buat environment di Postman dengan variables:

```json
{
    "base_url": "http://localhost:8000",
    "user_token": "",
    "admin_token": "",
    "editor_token": "",
    "pesanan_uuid": "",
    "order_id": "",
    "revision_uuid": ""
}
```

---

## 🐛 **COMMON TESTING ISSUES**

### Issue: 404 Not Found
- ✅ Check if route exists in `routes/api.php`
- ✅ Verify HTTP method (GET/POST/PUT/DELETE)
- ✅ Check middleware authentication

### Issue: 422 Validation Error
- ✅ Check required fields in request body
- ✅ Verify data types (string/integer/file)
- ✅ Check file upload limits

### Issue: 403 Unauthorized
- ✅ Include `Authorization: Bearer {{token}}` header
- ✅ Check token expiration
- ✅ Verify user permissions

### Issue: 500 Internal Server Error
- ✅ Check Laravel logs: `storage/logs/laravel.log`
- ✅ Verify database relationships
- ✅ Check if seeder ran successfully

---

## 🔍 **USEFUL TESTING QUERIES**

### Check Pesanan Status Distribution
```sql
SELECT status, COUNT(*) as count 
FROM pesanan 
GROUP BY status;
```

### Check Transaksi Data
```sql
SELECT t.order_id, t.status, p.status as pesanan_status
FROM transaksi t
JOIN pesanan p ON t.id_pesanan = p.id_pesanan;
```

### Check Revision Data
```sql
SELECT p.uuid, pr.urutan_revisi, COUNT(ru.id_revisi_user) as user_files, COUNT(re.id_revisi_editor) as editor_files
FROM pesanan p
LEFT JOIN pesanan_revisi pr ON p.id_pesanan = pr.id_pesanan
LEFT JOIN revisi_user ru ON pr.id_revisi = ru.id_revisi
LEFT JOIN revisi_editor re ON pr.id_revisi = re.id_revisi
WHERE p.status IN ('revisi', 'selesai')
GROUP BY p.uuid, pr.urutan_revisi;
```

---

## ✅ **TESTING CHECKLIST**

- [ ] Authentication works for all user types
- [ ] Order creation and listing work correctly
- [ ] Payment workflow is complete
- [ ] Admin can manage orders and payments
- [ ] Revision system works end-to-end
- [ ] File uploads work properly
- [ ] Status transitions are logical
- [ ] Error handling returns proper responses
- [ ] All CRUD operations work
- [ ] Database relationships are intact

---

**Happy Testing! 🚀** 