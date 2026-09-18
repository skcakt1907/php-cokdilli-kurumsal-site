<?php
require_once __DIR__ . '/inc/helpers.php';

$slug = $_GET['slug'] ?? '';
$stmt = $db->prepare("SELECT * FROM hizmetler WHERE slug=? AND durum=1 LIMIT 1");
$stmt->execute([$slug]);
$fa = $stmt->fetch();
if (!$fa) { header('Location: ' . SITE_URL . '/faaliyet-alanlari'); exit; }

$pageTitle = tf($fa, 'baslik') . ' — ' . ayar('site_adi');
$pageDesc  = tf($fa, 'ozet');

// Aynı sektör ailesindeki diğer alanlar
$kardesStmt = $db->prepare("SELECT * FROM hizmetler WHERE durum=1 AND grup=? AND id<>? ORDER BY sira ASC");
$kardesStmt->execute([$fa['grup'], $fa['id']]);
$kardesler = $kardesStmt->fetchAll();

// Bu alanda faaliyet gösteren iş ortakları
$ortakStmt = $db->prepare("SELECT * FROM projeler WHERE durum=1 AND kategori=? ORDER BY sira ASC");
$ortakStmt->execute([$fa['baslik']]);
$ortaklar = $ortakStmt->fetchAll();

require_once __DIR__ . '/inc/header.php';
?>
<section class="page-head">
  <div class="container">
    <h1><?= e(tf($fa, 'baslik')) ?></h1>
    <div class="crumb">
      <a href="<?= SITE_URL ?>/"><?= t('nav_anasayfa') ?></a> &rsaquo;
      <a href="<?= SITE_URL ?>/faaliyet-alanlari"><?= t('nav_faaliyet') ?></a> &rsaquo;
      <a href="<?= SITE_URL ?>/faaliyet-alanlari#<?= e(slugify($fa['grup'])) ?>"><?= e(tf($fa, 'grup')) ?></a> &rsaquo;
      <?= e(tf($fa, 'baslik')) ?>
    </div>
  </div>
</section>

<section>
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-8">
        <?php if ($fa['gorsel']): ?>
          <img src="<?= e(gorselUrl($fa['gorsel'])) ?>" class="w-100 rounded mb-4" alt="<?= e(tf($fa, 'baslik')) ?>">
        <?php endif; ?>
        <div class="section-head" style="margin-bottom:1rem">
          <span class="mini"><?= e(tf($fa, 'grup')) ?></span>
          <h2><?= e(tf($fa, 'baslik')) ?></h2>
        </div>
        <p class="lead" style="color:var(--gray)"><?= e(tf($fa, 'ozet')) ?></p>
        <div class="rich-text"><?= zenginMetin(tf($fa, 'icerik')) ?></div>

        <?php if ($ortaklar): ?>
          <h4 class="mt-5 mb-3"><?= t('nav_ortaklar') ?></h4>
          <div class="row g-4">
            <?php foreach ($ortaklar as $o): ?>
            <div class="col-md-6">
              <div class="company-card">
                <div class="cc-img"><?php if ($o['gorsel']): ?><img src="<?= e(gorselUrl($o['gorsel'])) ?>" alt="<?= e(tf($o, 'baslik')) ?>"><?php else: ?><span class="cc-mono"><?= e(mb_substr(tf($o, 'baslik'), 0, 2)) ?></span><?php endif; ?></div>
                <div class="cc-body">
                  <?php if (tf($o, 'ulke')): ?><span class="cc-sector"><?= e(tf($o, 'ulke')) ?></span><?php endif; ?>
                  <h5><?= e(tf($o, 'baslik')) ?></h5>
                  <p><?= e(mb_strimwidth(tf($o, 'aciklama'), 0, 110, '…')) ?></p>
                  <div class="cc-links"><a href="<?= SITE_URL ?>/ortak-detay?slug=<?= e($o['slug']) ?>"><?= t('detay') ?> <i class="bi bi-arrow-right"></i></a></div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <div class="col-lg-4">
        <?php if ($kardesler): ?>
        <!-- Kardeş faaliyet alanları. Kendi sınıfı var: .vm-card kullanılırsa
             o sınıfın "i" kuralı ikonu blok yapıp alt satıra atıyor. -->
        <div class="yan-liste mb-4">
          <h4><?= e(tf($fa, 'grup')) ?></h4>
          <?php foreach ($kardesler as $k): ?>
            <a class="yl-oge" href="<?= SITE_URL ?>/faaliyet-detay?slug=<?= e($k['slug']) ?>">
              <i class="bi <?= e($k['ikon']) ?>"></i><span><?= e(tf($k, 'baslik')) ?></span>
            </a>
          <?php endforeach; ?>
          <a class="yl-tumu" href="<?= SITE_URL ?>/faaliyet-alanlari"><?= t('tum_sektorler') ?> <i class="bi bi-arrow-right"></i></a>
        </div>
        <?php endif; ?>

        <div class="vm-card text-center">
          <i class="bi bi-chat-dots"></i>
          <h4 style="font-size:1.05rem"><?= t('bize_ulasin') ?></h4>
          <p class="mb-3"><?= t('iletisim_alt') ?></p>
          <a href="<?= SITE_URL ?>/iletisim" class="btn btn-orange w-100"><?= t('nav_iletisim') ?></a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
