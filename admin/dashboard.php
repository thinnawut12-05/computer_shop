<?php
require_once '../includes/config.php';

// ถ้ายังไม่ login ให้ไปหน้า login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$db = getDB();

$total_orders   = $db->query("SELECT COUNT(*) as n FROM orders")->fetch_assoc()['n'];
$total_revenue  = $db->query("SELECT COALESCE(SUM(total_amount),0) as n FROM orders WHERE status != 'cancelled'")->fetch_assoc()['n'];
$total_products = $db->query("SELECT COUNT(*) as n FROM products WHERE status='active'")->fetch_assoc()['n'];
$pending_orders = $db->query("SELECT COUNT(*) as n FROM orders WHERE status='pending'")->fetch_assoc()['n'];
$recent_orders  = $db->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - TechShop Pro</title>
<link rel="stylesheet" href="<?= SITE_URL ?>/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="admin-layout">
  <div class="admin-sidebar">
    <div class="admin-logo"><i class="fas fa-bolt"></i> ADMIN</div>
    <nav class="admin-nav">
      <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
      <a href="admin_products.php"><i class="fas fa-box"></i> สินค้า</a>
      <a href="admin_orders.php"><i class="fas fa-shopping-bag"></i> คำสั่งซื้อ</a>
      <a href="admin_categories.php"><i class="fas fa-tags"></i> หมวดหมู่</a>
      <a href="<?= SITE_URL ?>/index.php" target="_blank"><i class="fas fa-external-link-alt"></i> ดูหน้าร้าน</a>
      <a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
    </nav>
  </div>
  <div class="admin-content">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
      <h1 style="font-size:1.5rem; font-weight:700;">Dashboard <span style="color:var(--text-muted); font-size:1rem; font-weight:400;">ภาพรวมระบบ</span></h1>
      <span style="color:var(--text-muted); font-size:0.85rem;">สวัสดี, <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></span>
    </div>
    <div class="stat-grid">
      <div class="stat-card">
        <div class="stat-icon" style="background:rgba(0,212,255,0.15);"><i class="fas fa-shopping-bag" style="color:var(--primary)"></i></div>
        <div><div class="stat-value"><?= number_format($total_orders) ?></div><div class="stat-label">คำสั่งซื้อทั้งหมด</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:rgba(16,185,129,0.15);"><i class="fas fa-coins" style="color:var(--success)"></i></div>
        <div><div class="stat-value" style="color:var(--success);"><?= formatPrice($total_revenue) ?></div><div class="stat-label">รายได้รวม</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:rgba(124,58,237,0.15);"><i class="fas fa-box" style="color:#a78bfa"></i></div>
        <div><div class="stat-value" style="color:#a78bfa"><?= number_format($total_products) ?></div><div class="stat-label">สินค้าทั้งหมด</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background:rgba(245,158,11,0.15);"><i class="fas fa-clock" style="color:var(--warning)"></i></div>
        <div><div class="stat-value" style="color:var(--warning)"><?= number_format($pending_orders) ?></div><div class="stat-label">รออนุมัติ</div></div>
      </div>
    </div>
    <div class="card" style="padding:0; overflow:hidden;">
      <div class="card-header" style="padding:1.25rem 1.5rem; border-bottom:1px solid var(--border); display:flex; align-items:center;">
        <i class="fas fa-clock" style="color:var(--primary)"></i>&nbsp; คำสั่งซื้อล่าสุด
        <a href="admin_orders.php" style="margin-left:auto; font-size:0.8rem; color:var(--primary); text-decoration:none;">ดูทั้งหมด →</a>
      </div>
      <table class="data-table">
        <thead><tr><th>เลขที่</th><th>ชื่อลูกค้า</th><th>ยอดรวม</th><th>วิธีชำระ</th><th>สถานะ</th><th>วันที่</th><th></th></tr></thead>
        <tbody>
          <?php while ($o = $recent_orders->fetch_assoc()): ?>
          <tr>
            <td><strong style="color:var(--primary);">#<?= str_pad($o['id'],5,'0',STR_PAD_LEFT) ?></strong></td>
            <td><?= htmlspecialchars($o['customer_name']) ?></td>
            <td style="color:var(--success); font-weight:600;"><?= formatPrice($o['total_amount']) ?></td>
            <td style="font-size:0.8rem;"><?= match($o['payment_method']) { 'transfer'=>'โอนเงิน','cod'=>'COD','cash'=>'เงินสด',default=>$o['payment_method'] } ?></td>
            <td><span class="status-badge status-<?= $o['status'] ?>"><?= match($o['status']) { 'pending'=>'รออนุมัติ','confirmed'=>'ยืนยันแล้ว','shipping'=>'กำลังจัดส่ง','delivered'=>'ส่งแล้ว','cancelled'=>'ยกเลิก',default=>$o['status'] } ?></span></td>
            <td style="font-size:0.8rem; color:var(--text-muted);"><?= date('d/m/y H:i', strtotime($o['created_at'])) ?></td>
            <td><a href="admin_order_detail.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-secondary"><i class="fas fa-eye"></i></a></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
