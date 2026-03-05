<?php
require_once '../includes/config.php';
$db = getDB();
$id = intval($_GET['id'] ?? 0);
$stmt = $db->prepare("SELECT * FROM orders WHERE id=?");
$stmt->bind_param('i', $id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
if (!$order) { header('Location: ../index.php'); exit; }
$items = $db->query("SELECT * FROM order_items WHERE order_id = $id");
$page_title = 'สั่งซื้อสำเร็จ';
include '../includes/header.php';
?>
<div class="container" style="max-width:700px;">
  <div class="card" style="text-align:center; padding:3rem;">
    <div style="font-size:5rem; color:var(--success); margin-bottom:1rem;">
      <i class="fas fa-check-circle"></i>
    </div>
    <h1 style="font-size:2rem; color:var(--success); margin-bottom:0.5rem;">สั่งซื้อสำเร็จ!</h1>
    <p style="color:var(--text-muted); margin-bottom:2rem;">หมายเลขคำสั่งซื้อ: <strong style="color:var(--primary);">#<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></strong></p>
    
    <div class="card" style="text-align:left; margin-bottom:1.5rem;">
      <div class="card-header"><i class="fas fa-box" style="color:var(--primary)"></i> รายการสินค้า</div>
      <?php while ($item = $items->fetch_assoc()): ?>
      <div style="display:flex; justify-content:space-between; padding:0.5rem 0; border-bottom:1px solid var(--border);">
        <span><?= htmlspecialchars($item['product_name']) ?> x<?= $item['quantity'] ?></span>
        <span style="color:var(--primary);"><?= formatPrice($item['subtotal']) ?></span>
      </div>
      <?php endwhile; ?>
      <div style="display:flex; justify-content:space-between; padding-top:0.75rem; font-weight:700; font-size:1.1rem;">
        <span>ยอดรวม</span>
        <span style="color:var(--primary);"><?= formatPrice($order['total_amount']) ?></span>
      </div>
    </div>

    <div style="background:var(--dark-3); border-radius:8px; padding:1rem; text-align:left; margin-bottom:1.5rem; font-size:0.9rem;">
      <div><i class="fas fa-user" style="color:var(--primary); width:20px;"></i> <?= htmlspecialchars($order['customer_name']) ?></div>
      <div style="margin-top:0.4rem;"><i class="fas fa-phone" style="color:var(--primary); width:20px;"></i> <?= htmlspecialchars($order['customer_phone']) ?></div>
      <div style="margin-top:0.4rem;"><i class="fas fa-map-marker-alt" style="color:var(--primary); width:20px;"></i> <?= htmlspecialchars($order['shipping_address']) ?></div>
    </div>

    <a href="../index.php" class="btn btn-primary btn-lg">
      <i class="fas fa-home"></i> กลับหน้าหลัก
    </a>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
