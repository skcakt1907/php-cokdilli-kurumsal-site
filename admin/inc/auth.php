<?php
require_once __DIR__ . '/../../inc/helpers.php';

// Admin sayfalarını cache'leme — eski 301 redirect'leri de invalidate eder
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

if(empty($_SESSION['admin_id'])){
    header('Location: ' . SITE_URL . '/admin/index.php');
    exit;
}
$adminAd = $_SESSION['admin_ad'] ?? 'Yönetici';
