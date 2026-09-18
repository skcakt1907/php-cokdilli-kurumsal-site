<?php
require_once __DIR__ . '/inc/helpers.php';
$pageTitle = t('nav_ortaklar') . ' — ' . ayar('site_adi');

// Kartvizitteki "Group of Companies" listesi -> grup; diğerleri -> iş ortağı
$gruplar = [
    'grup'  => ['baslik' => t('grup_sirketleri'), 'alt' => t('grup_sirketleri_alt'),
                'kayitlar' => getList('projeler', "durum=1 AND tur='grup'", 'sira ASC')],
    'ortak' => ['baslik' => t('is_ortaklari'),    'alt' => t('is_ortaklari_alt'),
                'kayitlar' => getList('projeler', "durum=1 AND tur='ortak'", 'sira ASC')],
];
$toplam = array_sum(array_map(fn($g) => count($g['kayitlar']), $gruplar));

require_once __DIR__ . '/inc/header.php';
?>
<section class="page-head">
  <div class="container">
    <h1><?= t('nav_ortaklar') ?></h1>
    <div class="crumb"><a href="<?= SITE_URL ?>/"><?= t('nav_anasayfa') ?></a> &rsaquo; <?= t('nav_ortaklar') ?></div>
  </div>
</section>

<section>
  <div class="container">
    <?php if (ta('isbirligi')): ?>
      <div class="rich-text mx-auto mb-5" style="max-width:840px;text-align:center"><?= zenginMetin(ta('isbirligi')) ?></div>
    <?php endif; ?>

    <?php if (!$toplam): ?>
      <div class="empty-state"><i class="bi bi-people"></i><?= t('ortak_yok') ?></div>
    <?php endif; ?>

    <?php $ilk = true; foreach ($gruplar as $anahtar => $g): ?>
      <?php if (!$g['kayitlar']) continue; ?>
      <div class="section-head center<?= $ilk ? '' : ' mt-5 pt-4' ?>">
        <span class="mini"><?= e($g['alt']) ?></span>
        <h2><?= e($g['baslik']) ?></h2>
      </div>
      <div class="row g-4">
        <?php foreach ($g['kayitlar'] as $o): ?>
        <div class="col-lg-3 col-md-6">
          <div class="company-card">
            <div class="cc-img">
              <?php if ($o['gorsel']): ?>
                <img src="<?= e(gorselUrl($o['gorsel'])) ?>" alt="<?= e(tf($o, 'baslik')) ?>">
              <?php else: ?>
                <span class="cc-mono"><?= e(mb_substr(tf($o, 'baslik'), 0, 2)) ?></span>
              <?php endif; ?>
            </div>
            <div class="cc-body">
              <?php if (tf($o, 'ulke')): ?><span class="cc-sector"><i class="bi bi-geo-alt me-1"></i><?= e(tf($o, 'ulke')) ?></span><?php endif; ?>
              <h5><?= e(tf($o, 'baslik')) ?></h5>
              <?php if (tf($o, 'kategori')): ?><div class="cc-kategori"><?= e(tf($o, 'kategori')) ?></div><?php endif; ?>
              <?php if (tf($o, 'aciklama')): ?><p><?= e(mb_strimwidth(tf($o, 'aciklama'), 0, 130, '…')) ?></p><?php endif; ?>
              <div class="cc-links">
                <?php if (tf($o, 'aciklama')): ?>
                  <a href="<?= SITE_URL ?>/ortak-detay?slug=<?= e($o['slug']) ?>"><?= t('detay') ?> <i class="bi bi-arrow-right"></i></a>
                <?php endif; ?>
                <?php if ($o['website']): ?><a href="<?= e($o['website']) ?>" target="_blank" rel="noopener" title="<?= t('web_sitesi') ?>"><i class="bi bi-box-arrow-up-right"></i></a><?php endif; ?>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    <?php $ilk = false; endforeach; ?>
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
