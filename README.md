# TechShop Pro - ระบบขายอุปกรณ์คอมพิวเตอร์

## 📦 ไฟล์ทั้งหมด
```
computer_shop/
├── index.php              ← หน้าหลักร้านค้า
├── css/
│   └── style.css          ← สไตล์ชีตหลัก
├── includes/
│   ├── config.php         ← การตั้งค่าฐานข้อมูล
│   ├── header.php         ← Header ทุกหน้า
│   └── footer.php         ← Footer ทุกหน้า
├── pages/
│   ├── product.php        ← รายละเอียดสินค้า
│   ├── cart.php           ← ตะกร้าสินค้า
│   ├── cart_action.php    ← จัดการตะกร้า
│   ├── checkout.php       ← ชำระเงิน
│   ├── order_success.php  ← สั่งซื้อสำเร็จ
│   └── search.php         ← ค้นหาสินค้า
├── admin/
│   ├── dashboard.php      ← Admin Dashboard
│   ├── admin_products.php ← จัดการสินค้า
│   ├── admin_orders.php   ← จัดการออเดอร์
│   ├── admin_order_detail.php
│   ├── admin_categories.php
│   └── admin_logout.php
└── database.sql           ← ไฟล์ฐานข้อมูล
```

## 🚀 วิธีติดตั้ง

### 1. ติดตั้ง XAMPP
- ดาวน์โหลด XAMPP จาก https://www.apachefriends.org
- เปิด Apache และ MySQL

### 2. วางไฟล์
```
วางโฟลเดอร์ computer_shop ไปที่:
C:\xampp\htdocs\computer_shop\
```

### 3. สร้างฐานข้อมูล
1. เปิดเบราว์เซอร์ → http://localhost/phpmyadmin
2. คลิก **Import**
3. เลือกไฟล์ `database.sql`
4. กด **Go**

### 4. ตรวจสอบ config.php
```php
// includes/config.php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // ถ้ามีรหัสผ่านให้เพิ่มที่นี่
define('DB_NAME', 'computer_shop');
define('SITE_URL', 'http://localhost/computer_shop');
```

### 5. เปิดร้าน
- **หน้าร้าน:** http://localhost/computer_shop/
- **หลังบ้าน:** http://localhost/computer_shop/admin/dashboard.php
- **Admin Login:** `admin` / `admin1234`

---

## 🛍️ ฟีเจอร์ระบบ

### หน้าร้าน (Frontend)
- ✅ แสดงสินค้าแบ่งตามหมวดหมู่ (CPU, GPU, RAM, SSD/M.2, เคส, จอมอนิเตอร์)
- ✅ ค้นหาสินค้า
- ✅ กรองและเรียงลำดับสินค้า
- ✅ หน้ารายละเอียดสินค้าพร้อมสเปค
- ✅ ระบบตะกร้าสินค้า (Session)
- ✅ หน้าชำระเงิน (โอนเงิน / COD / เงินสด)
- ✅ ใบยืนยันออเดอร์

### หลังบ้าน (Admin)
- ✅ Dashboard แสดงสถิติ (ยอดขาย, ออเดอร์, สินค้า)
- ✅ จัดการสินค้า (เพิ่ม/แก้ไข/ลบ)
- ✅ จัดการออเดอร์ (เปลี่ยนสถานะ)
- ✅ ดูรายละเอียดออเดอร์
- ✅ จัดการหมวดหมู่

---

## 🔑 Default Admin
- Username: `admin`
- Password: `admin1234`

> **หมายเหตุ:** ควรเปลี่ยนรหัสผ่านหลังติดตั้ง
> สร้างรหัสผ่านใหม่ด้วย: `password_hash('รหัสผ่านใหม่', PASSWORD_DEFAULT)`
