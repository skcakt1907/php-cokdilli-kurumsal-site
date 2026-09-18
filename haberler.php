<?php
require_once __DIR__ . '/inc/helpers.php';
$pageTitle = t('nav_haberler') . ' — ' . ayar('site_adi');
$liste = getList('blog', 'durum=1', 'tarih DESC, id DESC');
require_once __DIR__ . '/inc/header.php';
?>
<section class="page-head">
  <div class="container">
    <h1><?= t('nav_haberler') ?></h1>
    <div class="crumb"><a href="<?= SITE_URL ?>/"><?= t('nav_anasayfa') ?></a> &rsaquo; <?= t('nav_haberler') ?></div>
  </div>
</section>

<section>
  <div class="container">
    <?php if (!$liste): ?>
      <div class="empty-state"><i class="bi bi-newspaper"></i><?= t('bulunamadi') ?></div>
    <?php else: ?>
    <div class="row g-4">
      <?php foreach ($liste as $hb): ?>
      <div class="col-lg-4 col-md-6">
        <div class="blog-card">
          <div class="img">
            <img src="<?= e(gorselUrl($hb['gorsel'])) ?>" alt="<?= e(tf($hb, 'baslik')) ?>">
            <?php if (tf($hb, 'kategori')): ?><span class="cat"><?= e(tf($hb, 'kategori')) ?></span><?php endif; ?>
          </div>
          <div class="blog-body">
            <div class="meta"><i class="bi bi-calendar3"></i><?= e(tarihYaz($hb['tarih'])) ?></div>
            <h5><a href="<?= SITE_URL ?>/haber-detay?slug=<?= e($hb['slug']) ?>"><?= e(tf($hb, 'baslik')) ?></a></h5>
            <p><?= e(tf($hb, 'ozet')) ?></p>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
