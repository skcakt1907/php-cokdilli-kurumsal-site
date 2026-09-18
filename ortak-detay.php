<?php
require_once __DIR__ . '/inc/helpers.php';

$slug = $_GET['slug'] ?? '';
$stmt = $db->prepare("SELECT * FROM projeler WHERE slug=? AND durum=1 LIMIT 1");
$stmt->execute([$slug]);
$o = $stmt->fetch();
if (!$o) { header('Location: ' . SITE_URL . '/is-ortaklari'); exit; }

$pageTitle = tf($o, 'baslik') . ' — ' . ayar('site_adi');
$pageDesc  = mb_strimwidth(tf($o, 'aciklama'), 0, 155, '…');
$digerler  = getList('projeler', 'durum=1 AND id<>' . (int)$o['id'], 'sira ASC', 6);

require_once __DIR__ . '/inc/header.php';
?>
<section class="page-head">
  <div class="container">
    <h1><?= e(tf($o, 'baslik')) ?></h1>
    <div class="crumb">
      <a href="<?= SITE_URL ?>/"><?= t('nav_anasayfa') ?></a> &rsaquo;
      <a href="<?= SITE_URL ?>/is-ortaklari"><?= t('nav_ortaklar') ?></a> &rsaquo;
      <?= e(tf($o, 'baslik')) ?>
    </div>
  </div>
</section>

<section>
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-8">
        <?php if ($o['gorsel']): ?>
          <img src="<?= e(gorselUrl($o['gorsel'])) ?>" class="ortak-logo mb-4" alt="<?= e(tf($o, 'baslik')) ?>">
        <?php endif; ?>
        <div class="section-head" style="margin-bottom:1rem">
          <span class="mini"><?= t('nav_ortaklar') ?></span>
          <h2><?= e(tf($o, 'baslik')) ?></h2>
        </div>
        <div class="rich-text"><?= zenginMetin(tf($o, 'aciklama')) ?></div>
      </div>

      <div class="col-lg-4">
        <div class="yan-liste mb-4">
          <h4><?= e(tf($o, 'baslik')) ?></h4>
          <ul class="yl-kunye">
            <?php if (tf($o, 'ulke')): ?>
              <li><span><?= t('ulke') ?></span><strong><?= e(tf($o, 'ulke')) ?></strong></li>
            <?php endif; ?>
            <?php if (tf($o, 'kategori')): ?>
              <li><span><?= t('sektor') ?></span><strong><?= e(tf($o, 'kategori')) ?></strong></li>
            <?php endif; ?>
            <?php if ($o['tarih']): ?>
              <li><span><?= t('kurulus_yili') ?></span><strong><?= e($o['tarih']) ?></strong></li>
            <?php endif; ?>
            <?php if ($o['website']): ?>
              <li><span><?= t('web_sitesi') ?></span>
                <a href="<?= e($o['website']) ?>" target="_blank" rel="noopener"><?= e(preg_replace('#^https?://#', '', $o['website'])) ?> <i class="bi bi-box-arrow-up-right"></i></a>
              </li>
            <?php endif; ?>
          </ul>
        </div>

        <?php if ($digerler): ?>
        <div class="yan-liste">
          <h4><?= t('nav_ortaklar') ?></h4>
          <?php foreach ($digerler as $d): ?>
            <a class="yl-oge" href="<?= SITE_URL ?>/ortak-detay?slug=<?= e($d['slug']) ?>">
              <i class="bi bi-diagram-2"></i><span><?= e(tf($d, 'baslik')) ?></span>
            </a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
