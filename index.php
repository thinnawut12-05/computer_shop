<?php
require_once 'includes/config.php';
$page_title = 'หน้าแรก';
$db = getDB();

// Filter
$cat_slug = $_GET['cat'] ?? '';
$sort = $_GET['sort'] ?? 'featured';

$where = "WHERE p.status='active'";
$params = [];
$types = '';

if ($cat_slug) {
    $where .= " AND c.slug = ?";
    $params[] = $cat_slug;
    $types .= 's';
}

$order = match($sort) {
    'price_asc' => 'p.price ASC',
    'price_desc' => 'p.price DESC',
    'name' => 'p.name ASC',
    default => 'p.featured DESC, p.id DESC'
};

$sql = "SELECT p.*, c.name as cat_name, c.slug as cat_slug, c.icon as cat_icon 
        FROM products p JOIN categories c ON p.category_id = c.id 
        $where ORDER BY $order LIMIT 60";

if ($types) {
    $stmt = $db->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $products = $stmt->get_result();
} else {
    $products = $db->query($sql);
}

// Featured products
$featured = $db->query("SELECT p.*, c.name as cat_name, c.icon as cat_icon 
                         FROM products p JOIN categories c ON p.category_id=c.id 
                         WHERE p.featured=1 AND p.status='active' LIMIT 4");

include 'includes/header.php';
?>

<div class="container">

  <?php if (!$cat_slug): ?>
  <div class="hero">
    <h1><i class="fas fa-bolt"></i> TechShop Pro</h1>
    <p>ร้านขายอุปกรณ์คอมพิวเตอร์ครบวงจร CPU · GPU · RAM · SSD · เคส · จอมอนิเตอร์</p>
  </div>

  <!-- Category Cards -->
  <h2 class="section-title"><i class="fas fa-th" style="color:var(--primary)"></i> หมวดหมู่สินค้า</h2>
  <div class="cat-grid">
    <?php
    $db->query("SELECT * FROM categories ORDER BY id")->data_seek(0);
    $cats = $db->query("SELECT * FROM categories ORDER BY id");
    $cat_icons = ['fa-microchip','fa-tv','fa-memory','fa-hdd','fa-desktop','fa-tv'];
    while ($c = $cats->fetch_assoc()):
    ?>
    <a href="?cat=<?= $c['slug'] ?>" class="cat-card">
      <i class="fas <?= $c['icon'] ?>"></i>
      <span><?= htmlspecialchars($c['name']) ?></span>
    </a>
    <?php endwhile; ?>
  </div>

  <!-- Featured -->
  <h2 class="section-title"><i class="fas fa-star" style="color:var(--secondary)"></i> สินค้าแนะนำ</h2>
  <div class="product-grid">
    <?php while ($p = $featured->fetch_assoc()): 
      $icon_map2 = ['cpu'=>'fa-microchip','gpu'=>'fa-tv','ram'=>'fa-memory','ssd'=>'fa-hdd','case'=>'fa-desktop','monitor'=>'fa-tv'];
    ?>
    <div class="product-card">
      <div class="product-img">
        <i class="fas <?= $icon_map2[$p['cat_slug'] ?? 'cpu'] ?? 'fa-microchip' ?>"></i>
        <span class="badge-featured"><i class="fas fa-star"></i> แนะนำ</span>
      </div>
      <div class="product-body">
        <div class="product-brand"><?= htmlspecialchars($p['brand']) ?></div>
        <div class="product-name">
          <a href="pages/product.php?id=<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></a>
        </div>
        <div class="product-price"><?= formatPrice($p['price']) ?></div>
        <div class="product-stock <?= $p['stock'] <= 0 ? 'out' : ($p['stock'] <= 5 ? 'low' : '') ?>">
          <?php if ($p['stock'] <= 0): ?>
            <i class="fas fa-times-circle"></i> สินค้าหมด
          <?php elseif ($p['stock'] <= 5): ?>
            <i class="fas fa-exclamation-triangle"></i> เหลือ <?= $p['stock'] ?> ชิ้น
          <?php else: ?>
            <i class="fas fa-check-circle" style="color:var(--success)"></i> มีสินค้า (<?= $p['stock'] ?> ชิ้น)
          <?php endif; ?>
        </div>
        <form method="POST" action="pages/cart_action.php">
          <input type="hidden" name="action" value="add">
          <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
          <input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI'] ?>">
          <button class="btn-add-cart" <?= $p['stock'] <= 0 ? 'disabled' : '' ?>>
            <i class="fas fa-cart-plus"></i>
            <?= $p['stock'] <= 0 ? 'สินค้าหมด' : 'เพิ่มในตะกร้า' ?>
          </button>
        </form>
      </div>
    </div>
    <?php endwhile; ?>
  </div>

  <h2 class="section-title"><i class="fas fa-fire" style="color:var(--primary)"></i> สินค้าทั้งหมด</h2>
  <?php endif; ?>

  <!-- Sort Bar -->
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; flex-wrap:wrap; gap:0.5rem;">
    <?php if ($cat_slug): ?>
      <h2 class="section-title" style="margin-bottom:0;">
        <i class="fas fa-filter" style="color:var(--primary)"></i> 
        <?= htmlspecialchars($cat_slug) ?>
      </h2>
    <?php else: ?>
      <span></span>
    <?php endif; ?>
    <div style="display:flex; gap:0.5rem; align-items:center;">
      <span style="color:var(--text-muted); font-size:0.85rem;">เรียงตาม:</span>
      <?php foreach (['featured'=>'แนะนำ','price_asc'=>'ราคา ต่ำ-สูง','price_desc'=>'ราคา สูง-ต่ำ','name'=>'ชื่อ A-Z'] as $k=>$v): ?>
      <a href="?<?= http_build_query(['cat'=>$cat_slug,'sort'=>$k]) ?>" 
         class="btn btn-sm <?= $sort===$k ? 'btn-primary' : 'btn-secondary' ?>"><?= $v ?></a>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Product Grid -->
  <div class="product-grid">
    <?php
    $icon_map = ['cpu'=>'fa-microchip','gpu'=>'fa-tv','ram'=>'fa-memory','ssd'=>'fa-hdd','case'=>'fa-desktop','monitor'=>'fa-tv'];
    while ($p = $products->fetch_assoc()):
    ?>
    <div class="product-card">
      <div class="product-img">
        <?php if ($p['image'] && $p['image'] !== 'default.jpg' && file_exists("uploads/".$p['image'])): ?>
          <img src="uploads/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
        <?php else: ?>
          <i class="fas <?= $icon_map[$p['cat_slug'] ?? 'cpu'] ?? 'fa-microchip' ?>"></i>
        <?php endif; ?>
        <?php if ($p['featured']): ?>
          <span class="badge-featured"><i class="fas fa-star"></i> แนะนำ</span>
        <?php endif; ?>
      </div>
      <div class="product-body">
        <div class="product-brand"><?= htmlspecialchars($p['brand']) ?></div>
        <div class="product-name">
          <a href="pages/product.php?id=<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></a>
        </div>
        <div class="product-price"><?= formatPrice($p['price']) ?></div>
        <div class="product-stock <?= $p['stock'] <= 0 ? 'out' : ($p['stock'] <= 5 ? 'low' : '') ?>">
          <?php if ($p['stock'] <= 0): ?>
            <i class="fas fa-times-circle"></i> สินค้าหมด
          <?php elseif ($p['stock'] <= 5): ?>
            <i class="fas fa-exclamation-triangle"></i> เหลือ <?= $p['stock'] ?> ชิ้น
          <?php else: ?>
            <i class="fas fa-check-circle" style="color:var(--success)"></i> มีสินค้า (<?= $p['stock'] ?> ชิ้น)
          <?php endif; ?>
        </div>
        <form method="POST" action="pages/cart_action.php">
          <input type="hidden" name="action" value="add">
          <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
          <input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI'] ?>">
          <button class="btn-add-cart" <?= $p['stock'] <= 0 ? 'disabled' : '' ?>>
            <i class="fas fa-cart-plus"></i>
            <?= $p['stock'] <= 0 ? 'สินค้าหมด' : 'เพิ่มในตะกร้า' ?>
          </button>
        </form>
      </div>
    </div>
    <?php endwhile; ?>
  </div>

</div>

<?php include 'includes/footer.php'; ?>
