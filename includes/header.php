<?php
// includes/header.php
if (!isset($page_title)) $page_title = 'TechShop Pro';
$db = getDB();
$categories = $db->query("SELECT * FROM categories ORDER BY id");
$cart_count = getCartCount();
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($page_title) ?> | TechShop Pro</title>
<link rel="stylesheet" href="<?= SITE_URL ?>/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<nav class="navbar">
  <div class="navbar-inner">
    <a href="<?= SITE_URL ?>/index.php" class="logo">TECH<span>SHOP</span> PRO</a>
    
    <div class="nav-search">
      <form method="GET" action="<?= SITE_URL ?>/pages/search.php">
        <i class="fas fa-search"></i>
        <input type="text" name="q" placeholder="ค้นหาสินค้า CPU, GPU, RAM..." 
               value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
      </form>
    </div>

    <div class="nav-links">
      <a href="<?= SITE_URL ?>/index.php"><i class="fas fa-home"></i> หน้าแรก</a>
      <?php if (isset($_SESSION['customer_id'])): ?>
        <a href="<?= SITE_URL ?>/pages/orders.php"><i class="fas fa-box"></i> ออเดอร์</a>
        <a href="<?= SITE_URL ?>/pages/logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
      <?php else: ?>
        <a href="<?= SITE_URL ?>/pages/login.php"><i class="fas fa-user"></i> เข้าสู่ระบบ</a>
      <?php endif; ?>
      <a href="<?= SITE_URL ?>/pages/cart.php" class="cart-btn">
        <i class="fas fa-shopping-cart"></i> ตะกร้า
        <?php if ($cart_count > 0): ?>
          <span class="cart-badge"><?= $cart_count ?></span>
        <?php endif; ?>
      </a>
    </div>
  </div>
</nav>

<div class="cat-nav">
  <div class="cat-nav-inner">
    <a href="<?= SITE_URL ?>/index.php" class="cat-link <?= (!isset($_GET['cat'])) ? 'active' : '' ?>">
      <i class="fas fa-th"></i> ทั้งหมด
    </a>
    <?php while ($cat = $categories->fetch_assoc()): ?>
    <a href="<?= SITE_URL ?>/index.php?cat=<?= $cat['slug'] ?>" 
       class="cat-link <?= (($_GET['cat'] ?? '') === $cat['slug']) ? 'active' : '' ?>">
      <i class="fas <?= $cat['icon'] ?>"></i> <?= htmlspecialchars($cat['name']) ?>
    </a>
    <?php endwhile; ?>
  </div>
</div>
