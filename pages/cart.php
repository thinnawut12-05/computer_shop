<?php
require_once '../includes/config.php';
$page_title = 'ตะกร้าสินค้า';
$cart = getCart();
$total = getCartTotal();
include '../includes/header.php';
?>

<div class="container">
  <h1 class="section-title"><i class="fas fa-shopping-cart" style="color:var(--primary)"></i> ตะกร้าสินค้า</h1>

  <?php if (isset($_SESSION['msg'])): ?>
    <div class="alert alert-<?= $_SESSION['msg']['type'] ?>"><?= $_SESSION['msg']['text'] ?></div>
    <?php unset($_SESSION['msg']); ?>
  <?php endif; ?>

  <?php if (empty($cart)): ?>
    <div class="card" style="text-align:center; padding:4rem;">
      <i class="fas fa-cart-arrow-down" style="font-size:4rem; color:var(--text-muted); margin-bottom:1rem;"></i>
      <p style="font-size:1.1rem; color:var(--text-muted); margin-bottom:1.5rem;">ตะกร้าของคุณว่างเปล่า</p>
      <a href="../index.php" class="btn btn-primary btn-lg"><i class="fas fa-arrow-left"></i> กลับไปซื้อสินค้า</a>
    </div>
  <?php else: ?>
  <div style="display:grid; grid-template-columns:1fr 320px; gap:1.5rem; align-items:start;">
    
    <!-- Cart Items -->
    <div class="card" style="padding:0; overflow:hidden;">
      <form method="POST" action="cart_action.php">
        <input type="hidden" name="action" value="update">
        <table class="cart-table">
          <thead>
            <tr>
              <th>สินค้า</th>
              <th>ราคา</th>
              <th>จำนวน</th>
              <th>รวม</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cart as $pid => $item): ?>
            <tr>
              <td>
                <div style="font-weight:600;"><?= htmlspecialchars($item['name']) ?></div>
                <div style="font-size:0.8rem; color:var(--primary);"><?= htmlspecialchars($item['brand'] ?? '') ?></div>
              </td>
              <td><?= formatPrice($item['price']) ?></td>
              <td>
                <input type="number" name="qty[<?= $pid ?>]" value="<?= $item['qty'] ?>" 
                       min="0" max="<?= $item['stock'] ?>" class="qty-input">
              </td>
              <td style="color:var(--primary); font-weight:700;"><?= formatPrice($item['price'] * $item['qty']) ?></td>
              <td>
                <button type="submit" name="qty[<?= $pid ?>]" value="0" 
                        class="btn btn-sm btn-danger"
                        onclick="this.name='qty[<?= $pid ?>]'; this.value='0';">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <div style="padding:1rem; display:flex; gap:0.75rem; border-top:1px solid var(--border);">
          <button type="submit" class="btn btn-secondary"><i class="fas fa-sync"></i> อัพเดทตะกร้า</button>
          <a href="../index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> ซื้อสินค้าต่อ</a>
        </div>
      </form>
    </div>

    <!-- Summary -->
    <div class="card">
      <div class="card-header"><i class="fas fa-receipt" style="color:var(--primary)"></i> สรุปคำสั่งซื้อ</div>
      <div style="display:flex; justify-content:space-between; margin-bottom:0.75rem;">
        <span style="color:var(--text-muted);">จำนวนสินค้า</span>
        <span><?= getCartCount() ?> ชิ้น</span>
      </div>
      <div style="display:flex; justify-content:space-between; margin-bottom:1.5rem; padding-top:0.75rem; border-top:1px solid var(--border);">
        <span style="font-size:1.1rem; font-weight:700;">ยอดรวมทั้งหมด</span>
        <span style="font-size:1.5rem; font-weight:700; color:var(--primary); font-family:'Rajdhani',sans-serif;">
          <?= formatPrice($total) ?>
        </span>
      </div>
      <a href="checkout.php" class="btn btn-primary btn-lg" style="width:100%; justify-content:center;">
        <i class="fas fa-credit-card"></i> สั่งซื้อสินค้า
      </a>
      <div style="margin-top:1rem; font-size:0.78rem; color:var(--text-muted); text-align:center;">
        <i class="fas fa-shield-alt"></i> ระบบปลอดภัย 100%
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
