<?php
// setup.php - ตั้งรหัสผ่าน Admin ครั้งแรก
// ** ลบไฟล์นี้ทิ้งหลังใช้งานแล้ว **

require_once 'includes/config.php';
$db = getDB();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? 'admin');
    $password = trim($_POST['password'] ?? '');
    $name     = trim($_POST['name'] ?? 'ผู้ดูแลระบบ');

    if (strlen($password) < 6) {
        $msg = ['type'=>'danger', 'text'=>'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร'];
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        // ลบ admin เดิมแล้วสร้างใหม่
        $db->query("DELETE FROM admins WHERE username='$username'");
        $stmt = $db->prepare("INSERT INTO admins (username, password, name, email) VALUES (?, ?, ?, 'admin@shop.com')");
        $stmt->bind_param('sss', $username, $hash, $name);
        $stmt->execute();
        $msg = ['type'=>'success', 'text'=>"✓ สร้างบัญชี admin สำเร็จ! username: <strong>$username</strong> | password: <strong>$password</strong>"];
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Setup Admin - TechShop Pro</title>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body style="display:flex; align-items:center; justify-content:center; min-height:100vh;">
<div style="width:100%; max-width:440px; padding:2rem;">
  <div style="text-align:center; margin-bottom:2rem;">
    <div class="logo" style="font-size:2rem; display:block; margin-bottom:0.5rem;">TECH<span>SHOP</span> PRO</div>
    <p style="color:var(--text-muted);">ตั้งค่า Admin ครั้งแรก</p>
  </div>

  <div class="card">
    <div class="card-header"><i class="fas fa-user-cog" style="color:var(--primary)"></i> สร้าง / รีเซ็ต Admin</div>

    <?php if ($msg): ?>
      <div class="alert alert-<?= $msg['type'] ?>"><?= $msg['text'] ?></div>
      <?php if ($msg['type']==='success'): ?>
        <a href="admin/dashboard.php" class="btn btn-primary" style="width:100%; justify-content:center; margin-bottom:1rem;">
          <i class="fas fa-sign-in-alt"></i> ไปหน้า Login Admin
        </a>
        <div class="alert alert-warning">
          <i class="fas fa-exclamation-triangle"></i>
          <strong>สำคัญ!</strong> กรุณาลบไฟล์ <code>setup.php</code> ออกหลังใช้งาน
        </div>
      <?php endif; ?>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" value="admin" required>
      </div>
      <div class="form-group">
        <label class="form-label">ชื่อที่แสดง</label>
        <input type="text" name="name" class="form-control" value="ผู้ดูแลระบบ">
      </div>
      <div class="form-group">
        <label class="form-label">รหัสผ่านใหม่ (อย่างน้อย 6 ตัว)</label>
        <input type="text" name="password" class="form-control" placeholder="กรอกรหัสผ่านที่ต้องการ" required>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
        <i class="fas fa-save"></i> บันทึกและสร้าง Admin
      </button>
    </form>
  </div>

  <p style="text-align:center; margin-top:1rem; font-size:0.8rem; color:var(--danger);">
    <i class="fas fa-shield-alt"></i> ลบไฟล์นี้ออกหลังใช้งานเสร็จ
  </p>
</div>
</body>
</html>
