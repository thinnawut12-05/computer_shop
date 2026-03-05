<?php
require_once '../includes/config.php';
$db = getDB();

$id = intval($_GET['id'] ?? 0);
$stmt = $db->prepare("SELECT p.*, c.name as cat_name, c.slug as cat_slug, c.icon as cat_icon 
                       FROM products p JOIN categories c ON p.category_id=c.id 
                       WHERE p.id=? AND p.status='active'");
$stmt->bind_param('i', $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) { header('Location: ../index.php'); exit; }

$specs = json_decode($product['specs'] ?? '{}', true);
$page_title = $product['name'];
$icon_map = ['cpu'=>'fa-microchip','gpu'=>'fa-tv','ram'=>'fa-memory','ssd'=>'fa-hdd','case'=>'fa-desktop','monitor'=>'fa-tv'];

include '../includes/header.php';
?>

<div class="container">
  <!-- Breadcrumb -->
  <div style="margin-bottom:1.5rem; font-size:0.85rem; color:var(--text-muted);">
    <a href="../index.php" style="color:var(--primary); text-decoration:none;">หน้าแรก</a>
    &rsaquo; <a href="../index.php?cat=<?= $product['cat_slug'] ?>" style="color:var(--primary); text-decoration:none;"><?= htmlspecialchars($product['cat_name']) ?></a>
    &rsaquo; <?= htmlspecialchars($product['name']) ?>
  </div>

  <div class="product-detail">
    <!-- Image -->
    <div class="product-detail-img">
      <i class="fas <?= $icon_map[$product['cat_slug']] ?? 'fa-microchip' ?>"></i>
    </div>

    <!-- Info -->
    <div>
      <div class="product-brand" style="font-size:0.85rem; margin-bottom:0.5rem;">
        <?= htmlspecialchars($product['brand']) ?> &bull; <?= htmlspecialchars($product['cat_name']) ?>
      </div>
      <h1 style="font-size:1.8rem; font-weight:700; margin-bottom:1rem; line-height:1.3;">
        <?= htmlspecialchars($product['name']) ?>
      </h1>
      
      <div style="font-size:2.5rem; font-weight:700; color:var(--primary); font-family:'Rajdhani',sans-serif; margin-bottom:0.5rem;">
        <?= formatPrice($product['price']) ?>
      </div>
      
      <div class="product-stock <?= $product['stock'] <= 0 ? 'out' : ($product['stock'] <= 5 ? 'low' : '') ?>" style="font-size:0.9rem; margin-bottom:1.5rem;">
        <?php if ($product['stock'] <= 0): ?>
          <i class="fas fa-times-circle"></i> สินค้าหมด
        <?php elseif ($product['stock'] <= 5): ?>
          <i class="fas fa-exclamation-triangle"></i> เหลือ <?= $product['stock'] ?> ชิ้น เท่านั้น!
        <?php else: ?>
          <i class="fas fa-check-circle" style="color:var(--success)"></i> มีสินค้า <?= $product['stock'] ?> ชิ้น
        <?php endif; ?>
      </div>

      <?php if ($product['description']): ?>
      <p style="color:var(--text-muted); margin-bottom:1.5rem; line-height:1.7;">
        <?= htmlspecialchars($product['description']) ?>
      </p>
      <?php endif; ?>

      <!-- Add to Cart -->
      <form method="POST" action="../pages/cart_action.php" style="display:flex; gap:0.75rem; align-items:center; margin-bottom:1.5rem;">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
        <input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI'] ?>">
        <div style="display:flex; align-items:center; gap:0.5rem;">
          <label style="font-size:0.85rem; color:var(--text-muted);">จำนวน:</label>
          <input type="number" name="qty" value="1" min="1" max="<?= $product['stock'] ?>" 
                 class="qty-input" style="width:80px;">
        </div>
        <button class="btn btn-primary btn-lg" <?= $product['stock'] <= 0 ? 'disabled' : '' ?>>
          <i class="fas fa-cart-plus"></i> 
          <?= $product['stock'] <= 0 ? 'สินค้าหมด' : 'เพิ่มในตะกร้า' ?>
        </button>
      </form>

      <!-- Specs -->
      <?php if (!empty($specs)): ?>
      <div class="card">
        <div class="card-header"><i class="fas fa-list" style="color:var(--primary)"></i> สเปค</div>
        <table class="specs-table">
          <?php 
          $spec_labels = [
            'cores'=>'จำนวนคอร์','threads'=>'จำนวนเธรด','base_clock'=>'ความเร็วพื้นฐาน',
            'boost_clock'=>'ความเร็วสูงสุด','socket'=>'ซ็อกเก็ต','tdp'=>'TDP',
            'vram'=>'VRAM','cuda_cores'=>'CUDA Cores','stream_processors'=>'Stream Processors',
            'power'=>'พลังงาน','interface'=>'Interface',
            'capacity'=>'ความจุ','type'=>'ประเภท','speed'=>'ความเร็ว','latency'=>'Latency','rgb'=>'RGB',
            'read'=>'อ่าน','write'=>'เขียน','form_factor'=>'รูปแบบ',
            'form_factor'=>'Form Factor','motherboard'=>'มาเธอร์บอร์ด','gpu_clearance'=>'GPU Clearance','drive_bay'=>'Drive Bay',
            'size'=>'ขนาด','resolution'=>'ความละเอียด','panel'=>'แผง','refresh_rate'=>'อัตราการรีเฟรช',
            'response_time'=>'Response Time','hdr'=>'HDR','g_sync'=>'G-Sync','freesync'=>'FreeSync'
          ];
          foreach ($specs as $k => $v): ?>
          <tr>
            <td><?= $spec_labels[$k] ?? ucfirst($k) ?></td>
            <td><strong><?= htmlspecialchars($v) ?></strong></td>
          </tr>
          <?php endforeach; ?>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
