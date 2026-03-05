<?php
require_once '../includes/config.php';
$db = getDB();
$q = trim($_GET['q'] ?? '');
$page_title = 'ค้นหา: ' . $q;
$icon_map = ['cpu'=>'fa-microchip','gpu'=>'fa-tv','ram'=>'fa-memory','ssd'=>'fa-hdd','case'=>'fa-desktop','monitor'=>'fa-tv'];

if ($q) {
    $like = '%' . $q . '%';
    $stmt = $db->prepare("SELECT p.*, c.name as cat_name, c.slug as cat_slug, c.icon as cat_icon 
                           FROM products p JOIN categories c ON p.category_id=c.id 
                           WHERE p.status='active' AND (p.name LIKE ? OR p.brand LIKE ? OR p.description LIKE ?)
                           ORDER BY p.featured DESC, p.id DESC");
    $stmt->bind_param('sss', $like, $like, $like);
    $stmt->execute();
    $products = $stmt->get_result();
}

include '../includes/header.php';
?>
<div class="container">
  <h1 class="section-title">
    <i class="fas fa-search" style="color:var(--primary)"></i>
    ผลการค้นหา: "<?= htmlspecialchars($q) ?>"
    <?php if (isset($products)): ?>
      <span style="font-size:0.9rem; color:var(--text-muted); font-weight:400;">(<?= $products->num_rows ?> รายการ)</span>
    <?php endif; ?>
  </h1>

  <?php if (!$q): ?>
    <div class="alert alert-info"><i class="fas fa-info-circle"></i> กรุณากรอกคำค้นหา</div>
  <?php elseif ($products->num_rows === 0): ?>
    <div class="card" style="text-align:center; padding:3rem;">
      <i class="fas fa-search" style="font-size:3rem; color:var(--text-muted); margin-bottom:1rem;"></i>
      <p style="color:var(--text-muted);">ไม่พบสินค้าที่ตรงกับ "<?= htmlspecialchars($q) ?>"</p>
      <a href="../index.php" class="btn btn-primary" style="margin-top:1rem;">ดูสินค้าทั้งหมด</a>
    </div>
  <?php else: ?>
  <div class="product-grid">
    <?php while ($p = $products->fetch_assoc()): ?>
    <div class="product-card">
      <div class="product-img">
        <i class="fas <?= $icon_map[$p['cat_slug']] ?? 'fa-microchip' ?>"></i>
        <?php if ($p['featured']): ?><span class="badge-featured"><i class="fas fa-star"></i> แนะนำ</span><?php endif; ?>
      </div>
      <div class="product-body">
        <div class="product-brand"><?= htmlspecialchars($p['brand']) ?></div>
        <div class="product-name"><a href="product.php?id=<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></a></div>
        <div class="product-price"><?= formatPrice($p['price']) ?></div>
        <div class="product-stock <?= $p['stock']<=0?'out':($p['stock']<=5?'low':'') ?>">
          <?= $p['stock']<=0 ? 'สินค้าหมด' : "มีสินค้า {$p['stock']} ชิ้น" ?>
        </div>
        <form method="POST" action="cart_action.php">
          <input type="hidden" name="action" value="add">
          <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
          <input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI'] ?>">
          <button class="btn-add-cart" <?= $p['stock']<=0?'disabled':'' ?>>
            <i class="fas fa-cart-plus"></i> เพิ่มในตะกร้า
          </button>
        </form>
      </div>
    </div>
    <?php endwhile; ?>
  </div>
  <?php endif; ?>
</div>
<?php include '../includes/footer.php'; ?>
