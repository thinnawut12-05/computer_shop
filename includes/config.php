<?php
// ==========================================
// config.php - ตั้งค่าการเชื่อมต่อฐานข้อมูล
// ==========================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'computer_shop');
define('SITE_NAME', 'TechShop Pro');
define('SITE_URL', 'http://localhost/computer_shop');

// เชื่อมต่อฐานข้อมูล
function getDB() {
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $conn->set_charset('utf8mb4');
        if ($conn->connect_error) {
            die("<div style='color:red;padding:20px;font-family:sans-serif;'>
                ❌ ไม่สามารถเชื่อมต่อฐานข้อมูลได้: " . $conn->connect_error . "
                <br>กรุณาตรวจสอบการตั้งค่าใน config.php
            </div>");
        }
    }
    return $conn;
}

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cart Helper
function getCart() {
    return $_SESSION['cart'] ?? [];
}

function getCartCount() {
    $cart = getCart();
    return array_sum(array_column($cart, 'qty'));
}

function getCartTotal() {
    $cart = getCart();
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * $item['qty'];
    }
    return $total;
}

function formatPrice($price) {
    return '฿' . number_format($price, 0, '.', ',');
}
?>
