<?php
require_once __DIR__ . '/auth.php';
$current    = basename($_SERVER['SCRIPT_NAME']);
$adminTitle = $adminTitle ?? 'Yönetim Paneli';
$yeniMesaj  = (int)$db->query("SELECT COUNT(*) FROM mesajlar WHERE okundu=0")->fetchColumn();
?>
<!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<title><?= e($adminTitle) ?> — <?= e(ayar('site_adi')) ?></title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
<link rel="icon" href="<?= SITE_URL ?>/img/logo-fgg-128.png" type="image/png">
<link href="<?= SITE_URL ?>/css/admin.css?v=<?= @filemtime(__DIR__ . '/../../css/admin.css') ?>" rel="stylesheet">
</head>
<body>
<aside class="sidebar">
  <div class="brand"><?= e(ayar('site_adi')) ?> <span>Admin</span></div>
  <?php
  $menu = [
    ['grp', 'Genel'],
    ['dashboard.php',   'bi-speedometer2',  'Dashboard',        0],
    ['grp', 'İçerik'],
    ['hizmetler.php',   'bi-grid-1x2',      'Faaliyet Alanları', 0],
    ['projeler.php',    'bi-diagram-3',     'İş Ortakları',      0],
    ['bloklar.php',     'bi-collection',    'Site Blokları',     0],
    ['yoneticiler.php', 'bi-people',        'Yönetim Kadrosu',   0],
    ['blog.php',        'bi-newspaper',     'Haberler',          0],
    ['grp', 'Yönetim'],
    ['mesajlar.php',    'bi-envelope',      'Mesajlar',          $yeniMesaj],
    ['ayarlar.php',     'bi-gear',          'Ayarlar',           0],
  ];
  foreach ($menu as $it):
      if ($it[0] === 'grp'): ?>
        <div class="grp"><?= e($it[1]) ?></div>
  <?php continue; endif;
      [$dosya, $ikon, $etiket, $rozet] = $it;
      $active = str_starts_with($current, explode('.', $dosya)[0]) ? 'active' : '';
  ?>
    <a href="<?= SITE_URL ?>/admin/<?= $dosya ?>" class="<?= $active ?>"><i class="bi <?= $ikon ?>"></i><span><?= e($etiket) ?></span><?php if ($rozet): ?><span class="badge"><?= (int)$rozet ?></span><?php endif; ?></a>
  <?php endforeach; ?>
  <div class="grp">&nbsp;</div>
  <a href="<?= SITE_URL ?>/" target="_blank"><i class="bi bi-eye"></i><span>Siteyi Görüntüle</span></a>
  <a href="<?= SITE_URL ?>/admin/logout.php"><i class="bi bi-box-arrow-right"></i><span>Çıkış Yap</span></a>
</aside>
<main class="main">
  <div class="topbar">
    <h1><?= e($adminTitle) ?></h1>
    <div class="user"><i class="bi bi-person-circle"></i><?= e($adminAd) ?></div>
  </div>
  <div class="content">
