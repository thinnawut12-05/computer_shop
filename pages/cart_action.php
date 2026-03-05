<?php
require_once '../includes/config.php';

$action = $_POST['action'] ?? '';
$redirect = $_POST['redirect'] ?? '../index.php';

if ($action === 'add') {
    $product_id = intval($_POST['product_id'] ?? 0);
    $qty = max(1, intval($_POST['qty'] ?? 1));
    
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM products WHERE id=? AND status='active' AND stock>0");
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    
    if ($product) {
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['qty'] = min(
                $_SESSION['cart'][$product_id]['qty'] + $qty,
                $product['stock']
            );
        } else {
            $_SESSION['cart'][$product_id] = [
                'id' => $product_id,
                'name' => $product['name'],
                'brand' => $product['brand'],
                'price' => $product['price'],
                'qty' => $qty,
                'stock' => $product['stock']
            ];
        }
        $_SESSION['msg'] = ['type'=>'success', 'text'=>'✓ เพิ่ม "' . $product['name'] . '" ในตะกร้าแล้ว'];
    }
}

elseif ($action === 'update') {
    foreach ($_POST['qty'] as $pid => $qty) {
        $pid = intval($pid);
        $qty = intval($qty);
        if ($qty <= 0) {
            unset($_SESSION['cart'][$pid]);
        } elseif (isset($_SESSION['cart'][$pid])) {
            $_SESSION['cart'][$pid]['qty'] = min($qty, $_SESSION['cart'][$pid]['stock']);
        }
    }
    $_SESSION['msg'] = ['type'=>'success', 'text'=>'✓ อัพเดทตะกร้าแล้ว'];
    $redirect = '../pages/cart.php';
}

elseif ($action === 'remove') {
    $pid = intval($_POST['product_id'] ?? 0);
    unset($_SESSION['cart'][$pid]);
    $_SESSION['msg'] = ['type'=>'warning', 'text'=>'ลบสินค้าออกจากตะกร้าแล้ว'];
    $redirect = '../pages/cart.php';
}

elseif ($action === 'clear') {
    $_SESSION['cart'] = [];
    $redirect = '../pages/cart.php';
}

header('Location: ' . $redirect);
exit;
