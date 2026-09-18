<?php
// Kurucu / Yönetim Kurulu Başkanı ve Grup CEO'sunun mesajı.
// Metin panelden düzenlenir: Ayarlar > Başkan'ın Mesajı
require_once __DIR__ . '/inc/helpers.php';

// Bu sayfaya özel serif başlık fontu (yalnızca burada yüklenir)
$ekstraFont = 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap';

$mesaj = ta('baskan_mesaji');

// İmza bilgisi yönetim kadrosundaki kurucu kaydından gelir (tek kaynak)
$baskan = getList('yoneticiler', 'durum=1', 'sira ASC, id ASC')[0] ?? null;

$pageTitle = ta('baskan_mesaj_baslik') . ' — ' . ayar('site_adi');
$pageDesc  = mb_substr(trim(preg_replace('/\s+/u', ' ', strip_tags($mesaj))), 0, 155);

require_once __DIR__ . '/inc/header.php';
?>

<section class="page-head">
  <div class="container">
    <h1><?= e(ta('baskan_mesaj_baslik')) ?></h1>
    <div class="crumb">
      <a href="<?= SITE_URL ?>/"><?= t('nav_anasayfa') ?></a> &rsaquo;
      <a href="<?= SITE_URL ?>/kurumsal"><?= t('nav_kurumsal') ?></a> &rsaquo;
      <?= e(ta('baskan_mesaj_baslik')) ?>
    </div>
  </div>
</section>

<section class="baskan-mesaj">
  <div class="container">

    <?php if ($baskan): ?>
    <header class="bm-kimlik">
      <?php if (!empty($baskan['foto'])): ?>
        <img class="bm-foto" src="<?= e(gorselUrl($baskan['foto'])) ?>" alt="<?= e($baskan['ad']) ?>">
      <?php else: ?>
        <div class="bm-foto bm-foto-bos"><i class="bi bi-person"></i></div>
      <?php endif; ?>
      <div>
        <span class="bm-ust"><?= e(ta('baskan_mesaj_alt')) ?></span>
        <h2 class="bm-ad"><?= e($baskan['ad']) ?></h2>
        <p class="bm-unvan"><?= e(tf($baskan, 'unvan')) ?></p>
        <p class="bm-sirket"><?= e(ayar('resmi_unvan')) ?></p>
      </div>
    </header>
    <?php endif; ?>

    <article class="bm-govde">
      <?= zenginMetin($mesaj) ?>
    </article>

    <?php if ($baskan): ?>
    <footer class="bm-imza">
      <span class="bm-imza-ad"><?= e($baskan['ad']) ?></span>
      <span class="bm-imza-unvan"><?= e(tf($baskan, 'unvan')) ?></span>
      <span class="bm-imza-sirket"><?= e(ayar('resmi_unvan')) ?></span>
    </footer>
    <?php endif; ?>

    <div class="bm-cta">
      <a href="<?= SITE_URL ?>/kurumsal" class="btn btn-line"><i class="bi bi-arrow-left me-2"></i><?= t('nav_kurumsal') ?></a>
      <a href="<?= SITE_URL ?>/iletisim" class="btn btn-orange"><?= t('bize_ulasin') ?> <i class="bi bi-arrow-right ms-1"></i></a>
    </div>

  </div>
</section>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
