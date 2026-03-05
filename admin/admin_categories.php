<?php
require_once '../includes/config.php';
if (!isset($_SESSION['admin_id'])) { header('Location: dashboard.php'); exit; }
$db = getDB();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $name = trim($_POST['name']); $slug = trim($_POST['slug']); $icon = trim($_POST['icon']); $desc = trim($_POST['description']);
        $stmt = $db->prepare("INSERT INTO categories (name,slug,icon,description) VALUES (?,?,?,?)");
        $stmt->bind_param('ssss', $name, $slug, $icon, $desc);
        $stmt->execute();
        $msg = ['type'=>'success','text'=>'✓ เพิ่มหมวดหมู่สำเร็จ'];
    }
}

$categories = $db->query("SELECT c.*, COUNT(p.id) as product_count FROM categories c LEFT JOIN products p ON c.id=p.category_id AND p.status='active' GROUP BY c.id ORDER BY c.id");
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8"><title>จัดการหมวดหมู่</title>
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
      <a href="admin_orders.php"><i class="fas fa-shopping-bag"></i> คำสั่งซื้อ</a>
      <a href="admin_categories.php" class="active"><i class="fas fa-tags"></i> หมวดหมู่</a>
      <a href="<?= SITE_URL ?>/index.php" target="_blank"><i class="fas fa-external-link-alt"></i> ดูหน้าร้าน</a>
      <a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
    </nav>
  </div>
  <div class="admin-content">
    <h1 style="font-size:1.5rem; font-weight:700; margin-bottom:1.5rem;">
      <i class="fas fa-tags" style="color:var(--primary)"></i> จัดการหมวดหมู่
    </h1>
    <?php if ($msg): ?><div class="alert alert-<?= $msg['type'] ?>"><?= $msg['text'] ?></div><?php endif; ?>
    <div style="display:grid; grid-template-columns:1fr 380px; gap:1.5rem;">
      <div class="card" style="padding:0; overflow:hidden;">
        <table class="data-table">
          <thead><tr><th>Icon</th><th>ชื่อ</th><th>Slug</th><th>สินค้า</th></tr></thead>
          <tbody>
            <?php while ($c = $categories->fetch_assoc()): ?>
            <tr>
              <td><i class="fas <?= htmlspecialchars($c['icon']) ?>" style="color:var(--primary); font-size:1.2rem;"></i></td>
              <td style="font-weight:600;"><?= htmlspecialchars($c['name']) ?></td>
              <td style="color:var(--text-muted); font-size:0.85rem;"><?= htmlspecialchars($c['slug']) ?></td>
              <td><span style="color:var(--primary); font-weight:700;"><?= $c['product_count'] ?></span> รายการ</td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
      <div class="card">
        <div class="card-header"><i class="fas fa-plus" style="color:var(--primary)"></i> เพิ่มหมวดหมู่</div>
        <form method="POST">
          <input type="hidden" name="action" value="add">
          <div class="form-group"><label class="form-label">ชื่อหมวดหมู่ *</label><input type="text" name="name" class="form-control" required></div>
          <div class="form-group"><label class="form-label">Slug * (ภาษาอังกฤษ)</label><input type="text" name="slug" class="form-control" required placeholder="เช่น cpu, gpu"></div>
          <div class="form-group"><label class="form-label">Font Awesome Icon</label><input type="text" name="icon" class="form-control" value="fa-microchip" placeholder="fa-microchip"></div>
          <div class="form-group"><label class="form-label">คำอธิบาย</label><textarea name="description" class="form-control" rows="2"></textarea></div>
          <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;"><i class="fas fa-save"></i> บันทึก</button>
        </form>
      </div>
    </div>
  </div>
</div>
</body>
</html>
