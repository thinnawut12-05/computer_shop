<?php
require_once '../includes/config.php';

// ถ้า login แล้ว ไป dashboard เลย
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = getDB();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $stmt = $db->prepare("SELECT * FROM admins WHERE username = ? LIMIT 1");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $admin = $stmt->get_result()->fetch_assoc();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            header('Location: dashboard.php');
            exit;
        }
    }
    $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login - TechShop Pro</title>
<link rel="stylesheet" href="<?= SITE_URL ?>/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body style="display:flex; align-items:center; justify-content:center; min-height:100vh;">
<div style="width:100%; max-width:400px; padding:2rem;">

  <div style="text-align:center; margin-bottom:2rem;">
    <a href="<?= SITE_URL ?>" class="logo" style="font-size:2rem; display:block; margin-bottom:0.5rem;">
      TECH<span>SHOP</span> PRO
    </a>
    <p style="color:var(--text-muted);">ระบบจัดการหลังบ้าน</p>
  </div>

  <div class="card">
    <div class="card-header">
      <i class="fas fa-lock" style="color:var(--primary)"></i> เข้าสู่ระบบ Admin
    </div>

    <?php if ($error): ?>
      <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> <?= $error ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="form-group">
        <label class="form-label">ชื่อผู้ใช้</label>
        <input type="text" name="username" class="form-control"
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
               placeholder="admin" autofocus required>
      </div>
      <div class="form-group">
        <label class="form-label">รหัสผ่าน</label>
        <input type="password" name="password" class="form-control"
               placeholder="••••••••" required>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; margin-top:0.5rem;">
        <i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ
      </button>
    </form>

    <div style="margin-top:1.25rem; padding-top:1rem; border-top:1px solid var(--border); text-align:center;">
      <a href="<?= SITE_URL ?>/setup.php" style="font-size:0.8rem; color:var(--text-muted); text-decoration:none;">
        <i class="fas fa-key"></i> ลืมรหัสผ่าน / ตั้งรหัสใหม่
      </a>
    </div>
  </div>

</div>
</body>
</html>
