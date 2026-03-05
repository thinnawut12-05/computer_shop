<?php
require_once '../includes/config.php';
if (!isset($_SESSION['admin_id'])) { header('Location: dashboard.php'); exit; }
$db = getDB();

// Handle actions
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add' || $action === 'edit') {
        $name = trim($_POST['name'] ?? '');
        $category_id = intval($_POST['category_id'] ?? 0);
        $brand = trim($_POST['brand'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $stock = intval($_POST['stock'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $featured = intval($_POST['featured'] ?? 0);
        $status = $_POST['status'] ?? 'active';
        
        if ($action === 'add') {
            $stmt = $db->prepare("INSERT INTO products (category_id,name,brand,description,price,stock,featured,status) VALUES (?,?,?,?,?,?,?,?)");
            $stmt->bind_param('isssdiss', $category_id, $name, $brand, $description, $price, $stock, $featured, $status);
            $stmt->execute();
            $msg = ['type'=>'success', 'text'=>'✓ เพิ่มสินค้าสำเร็จ'];
        } else {
            $id = intval($_POST['product_id']);
            $stmt = $db->prepare("UPDATE products SET category_id=?,name=?,brand=?,description=?,price=?,stock=?,featured=?,status=? WHERE id=?");
            $stmt->bind_param('isssdiisi', $category_id, $name, $brand, $description, $price, $stock, $featured, $status, $id);
            $stmt->execute();
            $msg = ['type'=>'success', 'text'=>'✓ แก้ไขสินค้าสำเร็จ'];
        }
    } elseif ($action === 'delete') {
        $id = intval($_POST['product_id']);
        $db->query("UPDATE products SET status='inactive' WHERE id=$id");
        $msg = ['type'=>'warning', 'text'=>'ลบสินค้าแล้ว'];
    }
}

$categories = $db->query("SELECT * FROM categories ORDER BY id");
$products = $db->query("SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id=c.id WHERE p.status='active' ORDER BY p.id DESC");
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>จัดการสินค้า - Admin</title>
<link rel="stylesheet" href="<?= SITE_URL ?>/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:9999; align-items:center; justify-content:center; }
.modal-overlay.show { display:flex; }
.modal { background:var(--dark-2); border:1px solid var(--border); border-radius:16px; padding:2rem; width:100%; max-width:600px; max-height:90vh; overflow-y:auto; }
</style>
</head>
<body>
<div class="admin-layout">
  <div class="admin-sidebar">
    <div class="admin-logo"><i class="fas fa-bolt"></i> ADMIN</div>
    <nav class="admin-nav">
      <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
      <a href="admin_products.php" class="active"><i class="fas fa-box"></i> สินค้า</a>
      <a href="admin_orders.php"><i class="fas fa-shopping-bag"></i> คำสั่งซื้อ</a>
      <a href="admin_categories.php"><i class="fas fa-tags"></i> หมวดหมู่</a>
      <a href="<?= SITE_URL ?>/index.php" target="_blank"><i class="fas fa-external-link-alt"></i> ดูหน้าร้าน</a>
      <a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
    </nav>
  </div>
  <div class="admin-content">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
      <h1 style="font-size:1.5rem; font-weight:700;"><i class="fas fa-box" style="color:var(--primary)"></i> จัดการสินค้า</h1>
      <button class="btn btn-primary" onclick="openModal()"><i class="fas fa-plus"></i> เพิ่มสินค้าใหม่</button>
    </div>

    <?php if ($msg): ?>
      <div class="alert alert-<?= $msg['type'] ?>"><?= $msg['text'] ?></div>
    <?php endif; ?>

    <div class="card" style="padding:0; overflow:hidden;">
      <table class="data-table">
        <thead>
          <tr><th>ID</th><th>ชื่อสินค้า</th><th>หมวดหมู่</th><th>ราคา</th><th>สต็อก</th><th>แนะนำ</th><th>จัดการ</th></tr>
        </thead>
        <tbody>
          <?php while ($p = $products->fetch_assoc()): ?>
          <tr>
            <td style="color:var(--text-muted);">#<?= $p['id'] ?></td>
            <td>
              <strong><?= htmlspecialchars($p['name']) ?></strong>
              <div style="font-size:0.75rem; color:var(--primary);"><?= htmlspecialchars($p['brand']) ?></div>
            </td>
            <td style="font-size:0.85rem;"><?= htmlspecialchars($p['cat_name']) ?></td>
            <td style="color:var(--success); font-weight:600;"><?= formatPrice($p['price']) ?></td>
            <td>
              <span style="<?= $p['stock']<=0 ? 'color:var(--danger)' : ($p['stock']<=5 ? 'color:var(--warning)' : 'color:var(--success)') ?>; font-weight:600;">
                <?= $p['stock'] ?>
              </span>
            </td>
            <td><?= $p['featured'] ? '<i class="fas fa-star" style="color:var(--warning)"></i>' : '-' ?></td>
            <td style="display:flex; gap:0.5rem;">
              <button class="btn btn-sm btn-secondary" 
                onclick="editProduct(<?= htmlspecialchars(json_encode($p)) ?>)">
                <i class="fas fa-edit"></i>
              </button>
              <form method="POST" onsubmit="return confirm('ลบสินค้านี้?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
              </form>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal-overlay" id="modal">
  <div class="modal">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
      <h2 style="font-size:1.2rem; font-weight:700;" id="modal-title">เพิ่มสินค้าใหม่</h2>
      <button onclick="closeModal()" style="background:none; border:none; color:var(--text-muted); font-size:1.5rem; cursor:pointer;">&times;</button>
    </div>
    <form method="POST" id="product-form">
      <input type="hidden" name="action" id="form-action" value="add">
      <input type="hidden" name="product_id" id="form-product-id">
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">ชื่อสินค้า *</label>
          <input type="text" name="name" id="f-name" class="form-control" required>
        </div>
        <div class="form-group">
          <label class="form-label">แบรนด์</label>
          <input type="text" name="brand" id="f-brand" class="form-control">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">หมวดหมู่ *</label>
          <select name="category_id" id="f-category" class="form-control" required>
            <?php $categories->data_seek(0); while ($c = $categories->fetch_assoc()): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">ราคา (บาท) *</label>
          <input type="number" name="price" id="f-price" class="form-control" step="0.01" min="0" required>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">จำนวนสต็อก</label>
          <input type="number" name="stock" id="f-stock" class="form-control" min="0" value="0">
        </div>
        <div class="form-group">
          <label class="form-label">สถานะ</label>
          <select name="status" id="f-status" class="form-control">
            <option value="active">แสดง</option>
            <option value="inactive">ซ่อน</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">รายละเอียด</label>
        <textarea name="description" id="f-description" class="form-control" rows="3"></textarea>
      </div>
      <div class="form-group">
        <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer;">
          <input type="checkbox" name="featured" id="f-featured" value="1" style="accent-color:var(--primary);">
          <span>สินค้าแนะนำ (Featured)</span>
        </label>
      </div>
      <div style="display:flex; gap:0.75rem; justify-content:flex-end;">
        <button type="button" class="btn btn-secondary" onclick="closeModal()">ยกเลิก</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> บันทึก</button>
      </div>
    </form>
  </div>
</div>

<script>
function openModal() {
  document.getElementById('modal-title').textContent = 'เพิ่มสินค้าใหม่';
  document.getElementById('form-action').value = 'add';
  document.getElementById('product-form').reset();
  document.getElementById('modal').classList.add('show');
}
function closeModal() { document.getElementById('modal').classList.remove('show'); }
function editProduct(p) {
  document.getElementById('modal-title').textContent = 'แก้ไขสินค้า';
  document.getElementById('form-action').value = 'edit';
  document.getElementById('form-product-id').value = p.id;
  document.getElementById('f-name').value = p.name;
  document.getElementById('f-brand').value = p.brand || '';
  document.getElementById('f-category').value = p.category_id;
  document.getElementById('f-price').value = p.price;
  document.getElementById('f-stock').value = p.stock;
  document.getElementById('f-status').value = p.status;
  document.getElementById('f-description').value = p.description || '';
  document.getElementById('f-featured').checked = p.featured == 1;
  document.getElementById('modal').classList.add('show');
}
</script>
</body>
</html>
