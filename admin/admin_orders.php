<?php
require_once '../includes/config.php';
if (!isset($_SESSION['admin_id'])) { header('Location: dashboard.php'); exit; }
$db = getDB();

// Update status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $oid = intval($_POST['order_id']);
    $status = $_POST['status'] ?? 'pending';
    $db->query("UPDATE orders SET status='$status' WHERE id=$oid");
    $msg = ['type'=>'success', 'text'=>'✓ อัพเดทสถานะออเดอร์แล้ว'];
}

$orders = $db->query("SELECT * FROM orders ORDER BY created_at DESC");
$status_labels = ['pending'=>'รออนุมัติ','confirmed'=>'ยืนยันแล้ว','shipping'=>'กำลังจัดส่ง','delivered'=>'ส่งแล้ว','cancelled'=>'ยกเลิก'];
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>จัดการออเดอร์ - Admin</title>
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
    <h1 style="font-size:1.5rem; font-weight:700; margin-bottom:1.5rem;">
      <i class="fas fa-shopping-bag" style="color:var(--primary)"></i> จัดการคำสั่งซื้อ
    </h1>
    <?php if (isset($msg)): ?>
      <div class="alert alert-<?= $msg['type'] ?>"><?= $msg['text'] ?></div>
    <?php endif; ?>
    <div class="card" style="padding:0; overflow:hidden;">
      <table class="data-table">
        <thead>
          <tr><th>เลขที่</th><th>ลูกค้า</th><th>โทร</th><th>ยอดรวม</th><th>ชำระ</th><th>สถานะ</th><th>วันที่</th><th>จัดการ</th></tr>
        </thead>
        <tbody>
          <?php while ($o = $orders->fetch_assoc()): ?>
          <tr>
            <td><strong style="color:var(--primary);">#<?= str_pad($o['id'],5,'0',STR_PAD_LEFT) ?></strong></td>
            <td>
              <div style="font-weight:600;"><?= htmlspecialchars($o['customer_name']) ?></div>
              <div style="font-size:0.75rem; color:var(--text-muted);"><?= htmlspecialchars($o['customer_email']) ?></div>
            </td>
            <td style="font-size:0.85rem;"><?= htmlspecialchars($o['customer_phone']) ?></td>
            <td style="color:var(--success); font-weight:700;"><?= formatPrice($o['total_amount']) ?></td>
            <td style="font-size:0.8rem;">
              <?= match($o['payment_method']) { 'transfer'=>'โอนเงิน','cod'=>'COD','cash'=>'เงินสด', default=>$o['payment_method'] } ?>
            </td>
            <td>
              <form method="POST" style="display:inline;">
                <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                <select name="status" class="form-control" style="padding:0.3rem 0.5rem; font-size:0.8rem; width:auto;"
                        onchange="this.form.submit()">
                  <?php foreach ($status_labels as $k=>$v): ?>
                  <option value="<?= $k ?>" <?= $o['status']===$k?'selected':'' ?>><?= $v ?></option>
                  <?php endforeach; ?>
                </select>
              </form>
            </td>
            <td style="font-size:0.8rem; color:var(--text-muted);"><?= date('d/m/y H:i', strtotime($o['created_at'])) ?></td>
            <td>
              <a href="admin_order_detail.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-secondary">
                <i class="fas fa-eye"></i> ดูรายละเอียด
              </a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
