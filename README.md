# # TechShop Pro| # ระบบขายอุปกรณ์คอมพิวเตอร์🖥️
ระบบนี้เป็นการศึกษาเท่านั้นและอยู่ช่วงการทดลองยังไม่สมบูรณ์ 100 %

## 🛠️ เทคโนโลยีที่ใช้

-   🐘   ** Backend: **   PHP 
-   📄   ** Frontend: **   Bootstrap 5, HTML, JavaScript (JS) 
-   🖥️   **สภาพแวดล้อมเซิร์ฟเวอร์: **   XAMPP ( MySQL)



## ⚠️ คำเตือน 

**ข้อควรทราบ:**  ระบบนี้ถูกพัฒนาขึ้นสำหรับการศึกษาเท่านั้นไม่มีที่ตั้งของร้านคอมนี้จริง⚠️


##  🚀ขั้นตอนการติดตั้ง 

1. **โคลนโปรเจค**  📂
```git clone https://github.com/thinnawut12-05/computer_shop```
```cd computer_shop```
```code .```
2.  **Import ฐานข้อมูล**  🗃️   
 -   เปิด  `phpmyadmin`   
 -   สร้างฐานข้อมูลใหม่ ที่มีชื่อcomputer_shop  
 -   Import ไฟล์  `database/hotel_db.sql`  เข้าไปในฐานข้อมูลนั้น 

## 🧑‍💻วิธีการใช้งาน 

1. **เปิด XAMPP Control Panel**  (หากไม่มี  [ติดตั้ง Xampp ที่นี่](https://www.apachefriends.org/download.html))
2.   กด  **Start**  ที่  `Apache`  และ  `MySQL`
3.   เปิดเบราว์เซอร์ของคุณและไปที่:  [http://localhost/computer_shop/](http://localhost/computer_shop/)
4.   เปิดเบราว์เซอร์ของคุณและไปที่(หน้าแอดมิน): [[http://localhost/computer_shop/admin/login.php](http://localhost/computer_shop/admin/login.php)]
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
- Password: `123456`


