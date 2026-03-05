<?php
require_once '../includes/config.php';
unset($_SESSION['admin_id'], $_SESSION['admin_name']);
header('Location: dashboard.php');
exit;
