<?php
require_once __DIR__ . '/inc/helpers.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare("SELECT * FROM yoneticiler WHERE id=? AND durum=1 LIMIT 1");
$stmt->execute([$id]);
$y = $stmt->fetch();
if (!$y) { header('Location: ' . SITE_URL . '/kurumsal'); exit; }

$pageTitle = $y['ad'] . ' — ' . tf($y, 'unvan') . ' — ' . ayar('site_adi');
$pageDesc  = mb_strimwidth(strip_tags(tf($y, 'ozgecmis')), 0, 155, '…');
$digerler  = getList('yoneticiler', 'durum=1 AND id<>' . (int)$y['id'], 'sira ASC', 6);

require_once __DIR__ . '/inc/header.php';
?>
<section class="page-head">
  <div class="container">
    <h1><?= e($y['ad']) ?></h1>
    <div class="crumb">
      <a href="<?= SITE_URL ?>/"><?= t('nav_anasayfa') ?></a> &rsaquo;
      <a href="<?= SITE_URL ?>/kurumsal"><?= t('yonetim') ?></a> &rsaquo;
      <?= e(tf($y, 'unvan')) ?>
    </div>
  </div>
</section>

<section>
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-4">
        <div class="yonetici-kart">
          <?php if ($y['foto']): ?>
            <img src="<?= e(gorselUrl($y['foto'])) ?>" alt="<?= e($y['ad']) ?>">
          <?php else: ?>
            <div class="yk-bos"><i class="bi bi-person"></i></div>
          <?php endif; ?>
          <h3><?= e($y['ad']) ?></h3>
          <span class="yk-unvan"><?= e(tf($y, 'unvan')) ?></span>
          <?php if ($y['linkedin']): ?>
            <a class="yk-linkedin" href="<?= e($y['linkedin']) ?>" target="_blank" rel="noopener"><i class="bi bi-linkedin me-1"></i>LinkedIn</a>
          <?php endif; ?>
        </div>

        <?php if ($digerler): ?>
        <div class="yan-liste mt-4">
          <h4><?= t('yonetim') ?></h4>
          <?php foreach ($digerler as $d): ?>
            <a class="yl-oge yl-yigin" href="<?= SITE_URL ?>/yonetici-detay?id=<?= (int)$d['id'] ?>">
              <span><strong><?= e($d['ad']) ?></strong></span>
              <span class="yl-alt"><?= e(tf($d, 'unvan')) ?></span>
            </a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <div class="col-lg-8">
        <div class="section-head" style="margin-bottom:1.2rem">
          <span class="mini"><?= e(ayar('site_adi')) ?></span>
          <h2><?= e(tf($y, 'unvan')) ?></h2>
        </div>
        <div class="rich-text"><?= zenginMetin(tf($y, 'ozgecmis')) ?></div>
        <a href="<?= SITE_URL ?>/kurumsal" class="btn btn-orange mt-4"><i class="bi bi-arrow-left me-1"></i> <?= t('geri') ?></a>
      </div>
    </div>
  </div>
</section>

<section class="cta-strip">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-8"><h3><?= t('bize_ulasin') ?></h3><p><?= t('iletisim_alt') ?></p></div>
      <div class="col-lg-4 text-lg-end mt-3 mt-lg-0"><a href="<?= SITE_URL ?>/iletisim" class="btn"><?= t('nav_iletisim') ?> <i class="bi bi-arrow-right ms-2"></i></a></div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
