<?php
require_once __DIR__ . '/inc/helpers.php';

$slug = $_GET['slug'] ?? '';
$stmt = $db->prepare("SELECT * FROM blog WHERE slug=? AND durum=1 LIMIT 1");
$stmt->execute([$slug]);
$hb = $stmt->fetch();
if (!$hb) { header('Location: ' . SITE_URL . '/haberler'); exit; }

$pageTitle = tf($hb, 'baslik') . ' — ' . ayar('site_adi');
$pageDesc  = tf($hb, 'ozet');
$digerler  = getList('blog', 'durum=1 AND id<>' . (int)$hb['id'], 'tarih DESC', 4);

require_once __DIR__ . '/inc/header.php';
?>
<section class="page-head">
  <div class="container">
    <h1><?= e(tf($hb, 'baslik')) ?></h1>
    <div class="crumb">
      <a href="<?= SITE_URL ?>/"><?= t('nav_anasayfa') ?></a> &rsaquo;
      <a href="<?= SITE_URL ?>/haberler"><?= t('nav_haberler') ?></a> &rsaquo;
      <?= e(tarihYaz($hb['tarih'])) ?>
    </div>
  </div>
</section>

<section>
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-8">
        <?php if ($hb['gorsel']): ?>
          <img src="<?= e(gorselUrl($hb['gorsel'])) ?>" class="w-100 rounded mb-4" alt="<?= e(tf($hb, 'baslik')) ?>">
        <?php endif; ?>
        <div class="meta mb-3" style="color:var(--metin-soluk);font-size:.85rem">
          <i class="bi bi-calendar3 me-1" style="color:var(--accent)"></i><?= e(tarihYaz($hb['tarih'])) ?>
          <?php if (tf($hb, 'kategori')): ?> &nbsp;·&nbsp; <?= e(tf($hb, 'kategori')) ?><?php endif; ?>
        </div>
        <p class="lead" style="color:var(--gray)"><?= e(tf($hb, 'ozet')) ?></p>
        <div class="rich-text"><?= zenginMetin(tf($hb, 'icerik')) ?></div>
        <a href="<?= SITE_URL ?>/haberler" class="btn btn-orange mt-4"><i class="bi bi-arrow-left me-1"></i> <?= t('geri') ?></a>
      </div>

      <div class="col-lg-4">
        <?php if ($digerler): ?>
        <div class="yan-liste">
          <h4><?= t('nav_haberler') ?></h4>
          <?php foreach ($digerler as $d): ?>
            <a class="yl-oge yl-yigin" href="<?= SITE_URL ?>/haber-detay?slug=<?= e($d['slug']) ?>">
              <span><?= e(tf($d, 'baslik')) ?></span>
              <span class="yl-alt"><?= e(tarihYaz($d['tarih'])) ?></span>
            </a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
