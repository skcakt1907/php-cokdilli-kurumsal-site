<?php
require_once __DIR__ . '/helpers.php';

// Sunucuda inc/helpers.php eski kalırsa site çökmesin diye emniyet:
// logoUrl() yoksa burada tanımlanır.
if (!function_exists('logoUrl')) {
    function logoUrl(): string {
        $yol = trim(ayar('logo'));
        if ($yol === '') $yol = 'img/logo-fgg-256.png';
        if (preg_match('#^(https?:)?//#i', $yol)) return $yol;
        return rtrim(SITE_URL, '/') . '/' . ltrim($yol, '/');
    }
}

$current  = basename($_SERVER['SCRIPT_NAME']);
$haberVar = (int)$db->query("SELECT COUNT(*) FROM blog WHERE durum=1")->fetchColumn() > 0;

// Faaliyet alanları menüde sektör ailelerine göre gruplanır.
// DİKKAT: Bu dosya her sayfanın içine dahil ediliyor. Buradaki döngü
// değişkenleri sayfanın kendi değişkenlerini EZMEMELİ — bu yüzden hepsi
// "mega" ön ekiyle yalıtıldı. ($fa kullanılırsa faaliyet-detay.php bozulur.)
$faaliyetGruplu = [];
foreach (getList('hizmetler', 'durum=1', 'sira ASC') as $megaSatir) {
    $faaliyetGruplu[tf($megaSatir, 'grup') ?: '—'][] = $megaSatir;
}
unset($megaSatir);

$pageTitle = $pageTitle ?? ta('site_baslik');
$pageDesc  = $pageDesc  ?? ta('site_aciklama');
?>
<!doctype html>
<html lang="<?= e(lang()) ?>" data-b="<?= imzaKodla(imzaMetni()) ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($pageTitle) ?></title>
<meta name="description" content="<?= e($pageDesc) ?>">
<link rel="canonical" href="<?= e(SITE_URL . strtok($_SERVER['REQUEST_URI'], '?')) ?>">
<?php foreach (array_keys(LANGS) as $l): ?>
<link rel="alternate" hreflang="<?= e($l) ?>" href="<?= e(SITE_URL . lang_url($l)) ?>">
<?php endforeach; ?>
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= e(ayar('site_adi')) ?>">
<meta property="og:title" content="<?= e($pageTitle) ?>">
<meta property="og:description" content="<?= e($pageDesc) ?>">
<meta property="og:image" content="<?= SITE_URL ?>/img/logo-fgg.png">
<link rel="icon" href="<?= SITE_URL ?>/img/logo-fgg-128.png" type="image/png">
<?php /* Site fontu: Montserrat (müşteri talebi). Yönetim paneli ayrı, Inter kullanır. */ ?>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<?php /* Sayfaya özel ek font — ilgili sayfa header'dan ÖNCE $ekstraFont tanımlar */ ?>
<?php if (!empty($ekstraFont)): ?>
<link href="<?= e($ekstraFont) ?>" rel="stylesheet">
<?php endif; ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
<link href="<?= SITE_URL ?>/css/style.css?v=<?= @filemtime(__DIR__ . '/../css/style.css') ?>" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="<?= SITE_URL ?>/">
      <img src="<?= e(logoUrl()) ?>" class="marka-logo" alt="<?= e(ayar('site_adi')) ?>">
      <span class="brand-lockup">
        <strong>FGG HOLDING</strong>
        <small><?= e(ayar('resmi_unvan')) ?></small>
      </span>
    </a>
    <button class="navbar-toggler border-0" data-bs-toggle="collapse" data-bs-target="#nav" aria-label="Menü"><i class="bi bi-list" style="font-size:1.8rem"></i></button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link <?= $current === 'index.php' ? 'active' : '' ?>" href="<?= SITE_URL ?>/"><?= t('nav_anasayfa') ?></a></li>
        <li class="nav-item"><a class="nav-link <?= $current === 'kurumsal.php' ? 'active' : '' ?>" href="<?= SITE_URL ?>/kurumsal"><?= t('nav_kurumsal') ?></a></li>

        <li class="nav-item nav-dropdown">
          <a class="nav-link <?= in_array($current, ['faaliyet-alanlari.php', 'faaliyet-detay.php']) ? 'active' : '' ?>" href="<?= SITE_URL ?>/faaliyet-alanlari"><?= t('nav_faaliyet') ?> <i class="bi bi-chevron-down ms-1" style="font-size:.7rem"></i></a>
          <?php if ($faaliyetGruplu): ?>
          <div class="dropdown-panel mega">
            <div class="mega-grid">
              <?php foreach ($faaliyetGruplu as $megaGrupAdi => $megaAlanlar): ?>
              <div class="mega-col">
                <h6>
                  <a href="<?= SITE_URL ?>/faaliyet-alanlari#<?= e(slugify($megaGrupAdi)) ?>">
                    <?= e($megaGrupAdi) ?><span class="mg-adet"><?= count($megaAlanlar) ?></span>
                  </a>
                </h6>
                <?php foreach ($megaAlanlar as $megaOge): ?>
                  <a href="<?= SITE_URL ?>/faaliyet-detay?slug=<?= e($megaOge['slug']) ?>"><i class="bi <?= e($megaOge['ikon']) ?>"></i><span><?= e(tf($megaOge, 'baslik')) ?></span></a>
                <?php endforeach; ?>
              </div>
              <?php endforeach; ?>
              <?php unset($megaGrupAdi, $megaAlanlar, $megaOge); ?>
            </div>
            <a class="mega-tumu" href="<?= SITE_URL ?>/faaliyet-alanlari"><?= t('tum_sektorler') ?> <i class="bi bi-arrow-right"></i></a>
          </div>
          <?php endif; ?>
        </li>

        <li class="nav-item"><a class="nav-link <?= in_array($current, ['is-ortaklari.php', 'ortak-detay.php']) ? 'active' : '' ?>" href="<?= SITE_URL ?>/is-ortaklari"><?= t('nav_ortaklar') ?></a></li>

        <?php if ($haberVar): ?>
        <li class="nav-item"><a class="nav-link <?= in_array($current, ['haberler.php', 'haber-detay.php']) ? 'active' : '' ?>" href="<?= SITE_URL ?>/haberler"><?= t('nav_haberler') ?></a></li>
        <?php endif; ?>

        <!-- Dil seçimi — dropdown -->
        <li class="nav-item dropdown nav-dil">
          <button class="dil-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-globe2"></i><span><?= e(strtoupper(lang())) ?></span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end dil-menu">
            <?php foreach (LANGS as $kod => $adi): ?>
              <li>
                <a class="dropdown-item <?= lang() === $kod ? 'active' : '' ?>" href="<?= e(lang_url($kod)) ?>" hreflang="<?= e($kod) ?>">
                  <span class="dil-kod"><?= e(strtoupper($kod)) ?></span><?= e($adi) ?>
                  <?php if (lang() === $kod): ?><i class="bi bi-check2 ms-auto"></i><?php endif; ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </li>

        <li class="nav-item"><a class="nav-link nav-cta <?= $current === 'iletisim.php' ? 'active' : '' ?>" href="<?= SITE_URL ?>/iletisim"><?= t('nav_iletisim') ?></a></li>
      </ul>
    </div>
  </div>
</nav>
