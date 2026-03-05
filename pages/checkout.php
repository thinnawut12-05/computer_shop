<?php
require_once '../includes/config.php';
$db = getDB();
$cart = getCart();
$total = getCartTotal();
$page_title = 'ชำระเงิน';

if (empty($cart)) { header('Location: cart.php'); exit; }

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['customer_name'] ?? '');
    $email = trim($_POST['customer_email'] ?? '');
    $phone = trim($_POST['customer_phone'] ?? '');
    $address = trim($_POST['shipping_address'] ?? '');
    $payment = $_POST['payment_method'] ?? 'transfer';
    $notes = trim($_POST['notes'] ?? '');

    if (!$name || !$email || !$phone || !$address) {
        $error = 'กรุณากรอกข้อมูลให้ครบถ้วน';
    } else {
        // Create order
        $customer_id = $_SESSION['customer_id'] ?? null;
        $stmt = $db->prepare("INSERT INTO orders (customer_id,customer_name,customer_email,customer_phone,shipping_address,total_amount,payment_method,notes) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->bind_param('issssdss', $customer_id, $name, $email, $phone, $address, $total, $payment, $notes);
        $stmt->execute();
        $order_id = $db->insert_id;

        // Order items
        foreach ($cart as $pid => $item) {
            $subtotal = $item['price'] * $item['qty'];
            $stmt2 = $db->prepare("INSERT INTO order_items (order_id,product_id,product_name,price,quantity,subtotal) VALUES (?,?,?,?,?,?)");
            $stmt2->bind_param('iisdid', $order_id, $pid, $item['name'], $item['price'], $item['qty'], $subtotal);
            $stmt2->execute();
            // Update stock
            $db->query("UPDATE products SET stock = stock - {$item['qty']} WHERE id = $pid AND stock >= {$item['qty']}");
        }

        // Clear cart
        $_SESSION['cart'] = [];
        header('Location: order_success.php?id=' . $order_id);
        exit;
    }
}

include '../includes/header.php';
?>

<div class="container">
  <h1 class="section-title"><i class="fas fa-credit-card" style="color:var(--primary)"></i> ชำระเงิน</h1>

  <?php if ($error): ?>
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
  <?php endif; ?>

  <div style="display:grid; grid-template-columns:1fr 360px; gap:1.5rem; align-items:start;">
    
    <!-- Form -->
    <form method="POST">
      <div class="card">
        <div class="card-header"><i class="fas fa-user" style="color:var(--primary)"></i> ข้อมูลผู้สั่งซื้อ</div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">ชื่อ-นามสกุล *</label>
            <input type="text" name="customer_name" class="form-control" required
                   value="<?= htmlspecialchars($_POST['customer_name'] ?? '') ?>" placeholder="กรอกชื่อ-นามสกุล">
          </div>
          <div class="form-group">
            <label class="form-label">อีเมล *</label>
            <input type="email" name="customer_email" class="form-control" required
                   value="<?= htmlspecialchars($_POST['customer_email'] ?? '') ?>" placeholder="email@example.com">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">เบอร์โทรศัพท์ *</label>
          <input type="tel" name="customer_phone" class="form-control" required
                 value="<?= htmlspecialchars($_POST['customer_phone'] ?? '') ?>" placeholder="08X-XXX-XXXX">
        </div>
        <div class="form-group">
          <label class="form-label">ที่อยู่จัดส่ง *</label>
          <textarea name="shipping_address" class="form-control" required rows="3" 
                    placeholder="บ้านเลขที่ ถนน แขวง/ตำบล เขต/อำเภอ จังหวัด รหัสไปรษณีย์"><?= htmlspecialchars($_POST['shipping_address'] ?? '') ?></textarea>
        </div>
      </div>

      <div class="card" style="margin-top:1.25rem;">
        <div class="card-header"><i class="fas fa-wallet" style="color:var(--primary)"></i> วิธีชำระเงิน</div>
        <?php foreach (['transfer'=>['icon'=>'fa-university','label'=>'โอนเงินผ่านธนาคาร'], 'cod'=>['icon'=>'fa-truck','label'=>'เก็บเงินปลายทาง (COD)'], 'cash'=>['icon'=>'fa-money-bill','label'=>'ชำระที่ร้าน']] as $k=>$v): ?>
        <label style="display:flex; align-items:center; gap:0.75rem; padding:0.75rem; border-radius:8px; cursor:pointer; margin-bottom:0.5rem; border:1px solid var(--border); transition:all 0.2s;" 
               onclick="this.style.borderColor='var(--primary)'">
          <input type="radio" name="payment_method" value="<?= $k ?>" <?= ($k==='transfer')?'checked':'' ?> style="accent-color:var(--primary);">
          <i class="fas <?= $v['icon'] ?>" style="color:var(--primary); width:20px;"></i>
          <span><?= $v['label'] ?></span>
        </label>
        <?php endforeach; ?>
      </div>

      <div class="card" style="margin-top:1.25rem;">
        <div class="card-header"><i class="fas fa-comment" style="color:var(--primary)"></i> หมายเหตุ</div>
        <textarea name="notes" class="form-control" rows="2" placeholder="หมายเหตุเพิ่มเติม (ถ้ามี)"><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
      </div>

      <div style="margin-top:1.5rem; display:flex; gap:1rem;">
        <a href="cart.php" class="btn btn-secondary btn-lg"><i class="fas fa-arrow-left"></i> กลับ</a>
        <button type="submit" class="btn btn-primary btn-lg" style="flex:1; justify-content:center;">
          <i class="fas fa-check-circle"></i> ยืนยันการสั่งซื้อ <?= formatPrice($total) ?>
        </button>
      </div>
    </form>

    <!-- Order Summary -->
    <div class="card">
      <div class="card-header"><i class="fas fa-receipt" style="color:var(--primary)"></i> สรุปสินค้า</div>
      <?php foreach ($cart as $item): ?>
      <div style="display:flex; justify-content:space-between; align-items:start; padding:0.75rem 0; border-bottom:1px solid var(--border);">
        <div>
          <div style="font-size:0.9rem; font-weight:600;"><?= htmlspecialchars($item['name']) ?></div>
          <div style="font-size:0.8rem; color:var(--text-muted);">x<?= $item['qty'] ?></div>
        </div>
        <div style="color:var(--primary); font-weight:700; white-space:nowrap;">
          <?= formatPrice($item['price'] * $item['qty']) ?>
        </div>
      </div>
      <?php endforeach; ?>
      <div style="display:flex; justify-content:space-between; padding-top:1rem; font-size:1.2rem; font-weight:700;">
        <span>ยอดรวม</span>
        <span style="color:var(--primary); font-family:'Rajdhani',sans-serif; font-size:1.5rem;"><?= formatPrice($total) ?></span>
      </div>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
