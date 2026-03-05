<?php
require_once '../includes/config.php';
if (!isset($_SESSION['admin_id'])) { header('Location: dashboard.php'); exit; }
$db = getDB();
$id = intval($_GET['id'] ?? 0);
$order = $db->query("SELECT * FROM orders WHERE id=$id")->fetch_assoc();
if (!$order) { header('Location: admin_orders.php'); exit; }
$items = $db->query("SELECT * FROM order_items WHERE order_id=$id");
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ออเดอร์ #<?= str_pad($id,5,'0',STR_PAD_LEFT) ?> - Admin</title>
<link rel="stylesheet" href="<?= SITE_URL ?>/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="admin-layout">
  <div class="admin-sidebar">
    <div class="admin-logo"><i class="fas fa-bolt"></i> ADMIN</div>
    <nav class="admin-nav">
      <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
      <a href="admin_products.php"><i class="fas fa-box"></i> สินค้า</a>
      <a href="admin_orders.php" class="active"><i class="fas fa-shopping-bag"></i> คำสั่งซื้อ</a>
      <a href="admin_categories.php"><i class="fas fa-tags"></i> หมวดหมู่</a>
      <a href="<?= SITE_URL ?>/index.php" target="_blank"><i class="fas fa-external-link-alt"></i> ดูหน้าร้าน</a>
      <a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
    </nav>
  </div>
  <div class="admin-content">
    <div style="display:flex; align-items:center; gap:1rem; margin-bottom:1.5rem;">
      <a href="admin_orders.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> กลับ</a>
      <h1 style="font-size:1.5rem; font-weight:700;">
        คำสั่งซื้อ <span style="color:var(--primary);">#<?= str_pad($id,5,'0',STR_PAD_LEFT) ?></span>
      </h1>
      <span class="status-badge status-<?= $order['status'] ?>" style="font-size:0.9rem;">
        <?= match($order['status']) { 'pending'=>'รออนุมัติ','confirmed'=>'ยืนยันแล้ว','shipping'=>'กำลังจัดส่ง','delivered'=>'ส่งแล้ว','cancelled'=>'ยกเลิก', default=>$order['status'] } ?>
      </span>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:1.5rem;">
      <div class="card">
        <div class="card-header"><i class="fas fa-user" style="color:var(--primary)"></i> ข้อมูลลูกค้า</div>
        <div style="font-size:0.9rem; line-height:2;">
          <div><strong>ชื่อ:</strong> <?= htmlspecialchars($order['customer_name']) ?></div>
          <div><strong>อีเมล:</strong> <?= htmlspecialchars($order['customer_email']) ?></div>
          <div><strong>โทร:</strong> <?= htmlspecialchars($order['customer_phone']) ?></div>
          <div><strong>ที่อยู่:</strong> <?= nl2br(htmlspecialchars($order['shipping_address'])) ?></div>
          <?php if ($order['notes']): ?>
          <div><strong>หมายเหตุ:</strong> <?= htmlspecialchars($order['notes']) ?></div>
          <?php endif; ?>
        </div>
      </div>
      <div class="card">
        <div class="card-header"><i class="fas fa-info-circle" style="color:var(--primary)"></i> ข้อมูลออเดอร์</div>
        <div style="font-size:0.9rem; line-height:2;">
          <div><strong>วันที่สั่ง:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></div>
          <div><strong>ชำระผ่าน:</strong> <?= match($order['payment_method']) { 'transfer'=>'โอนเงินธนาคาร','cod'=>'เก็บเงินปลายทาง','cash'=>'เงินสดที่ร้าน', default=>$order['payment_method'] } ?></div>
          <div><strong>ยอดรวม:</strong> <span style="color:var(--primary); font-weight:700; font-size:1.1rem;"><?= formatPrice($order['total_amount']) ?></span></div>
        </div>
      </div>
    </div>

    <div class="card" style="padding:0; overflow:hidden;">
      <div class="card-header" style="padding:1.25rem 1.5rem;">
        <i class="fas fa-box" style="color:var(--primary)"></i> รายการสินค้า
      </div>
      <table class="data-table">
        <thead><tr><th>สินค้า</th><th>ราคา/ชิ้น</th><th>จำนวน</th><th>รวม</th></tr></thead>
        <tbody>
          <?php while ($item = $items->fetch_assoc()): ?>
          <tr>
            <td style="font-weight:600;"><?= htmlspecialchars($item['product_name']) ?></td>
            <td><?= formatPrice($item['price']) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td style="color:var(--primary); font-weight:700;"><?= formatPrice($item['subtotal']) ?></td>
          </tr>
          <?php endwhile; ?>
          <tr style="background:var(--dark-3);">
            <td colspan="3" style="text-align:right; font-weight:700; padding:1rem;">ยอดรวมทั้งหมด</td>
            <td style="color:var(--primary); font-weight:700; font-size:1.2rem;"><?= formatPrice($order['total_amount']) ?></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
