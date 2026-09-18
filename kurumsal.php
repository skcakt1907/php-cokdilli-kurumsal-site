<?php
require_once __DIR__ . '/inc/helpers.php';
$pageTitle = t('nav_kurumsal') . ' — ' . ayar('site_adi');
$pageDesc  = ta('hakkimizda_kisa');

$yoneticiler = getList('yoneticiler', 'durum=1', 'sira ASC, id ASC');
$nedenler    = getList('bloklar', "durum=1 AND tip='neden'", 'sira ASC');
$surec       = getList('bloklar', "durum=1 AND tip='surec'", 'sira ASC');
$bolgeler    = getList('bloklar', "durum=1 AND tip='bolge'", 'sira ASC');

require_once __DIR__ . '/inc/header.php';
?>
<section class="page-head">
  <div class="container">
    <h1><?= t('nav_kurumsal') ?></h1>
    <div class="crumb"><a href="<?= SITE_URL ?>/"><?= t('nav_anasayfa') ?></a> &rsaquo; <?= t('nav_kurumsal') ?></div>
  </div>
</section>

<!-- ===== HİKÂYEMİZ ===== -->
<section>
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-5">
        <div class="about-img-wrap">
          <img src="https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=900&q=80" alt="<?= e(ayar('site_adi')) ?>">
        </div>
      </div>
      <div class="col-lg-7">
        <div class="section-head" style="margin-bottom:1.1rem">
          <span class="mini"><?= e(ayar('resmi_unvan')) ?></span>
          <h2><?= t('biz_kimiz') ?></h2>
        </div>
        <p class="lead" style="color:var(--gray)"><?= e(ta('hakkimizda_kisa')) ?></p>
        <div class="rich-text"><?= zenginMetin(ta('hakkimizda_uzun')) ?></div>

        <?php if (ta('baskan_mesaji')): ?>
        <a class="bm-tanitim" href="<?= SITE_URL ?>/baskan-mesaji">
          <i class="bi bi-quote"></i>
          <span>
            <span class="bt-baslik"><?= e(ta('baskan_mesaj_baslik')) ?></span>
            <span class="bt-alt"><?= e(ta('baskan_mesaj_alt')) ?></span>
          </span>
          <i class="bi bi-arrow-right bt-ok"></i>
        </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- ===== VİZYON / MİSYON ===== -->
<section style="background:var(--light)">
  <div class="container">
    <div class="row g-4 align-items-stretch">
      <div class="col-lg-6">
        <div class="vm-card h-100">
          <i class="bi bi-compass"></i>
          <span class="mini d-block mb-1" style="color:var(--accent);font-weight:700;letter-spacing:1.6px;text-transform:uppercase;font-size:.72rem"><?= t('vizyon') ?></span>
          <h4><?= e(ta('vizyon_baslik')) ?></h4>
          <div class="rich-text mt-2"><?= zenginMetin(ta('vizyon')) ?></div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="vm-card h-100">
          <i class="bi bi-bullseye"></i>
          <h4><?= t('misyon') ?></h4>
          <div class="rich-text mt-2"><?= zenginMetin(ta('misyon')) ?></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===== NEDEN FGG HOLDING ===== -->
<?php if ($nedenler): ?>
<section>
  <div class="container">
    <div class="section-head center">
      <span class="mini"><?= t('neden_alt') ?></span>
      <h2><?= t('neden_baslik') ?></h2>
    </div>
    <div class="row g-4">
      <?php foreach ($nedenler as $n): ?>
      <div class="col-lg-4 col-md-6">
        <div class="vm-card h-100">
          <i class="bi <?= e($n['ikon']) ?>"></i>
          <h4><?= e(tf($n, 'baslik')) ?></h4>
          <p><?= e(tf($n, 'ozet')) ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ===== PROJE GELİŞTİRME MODELİ ===== -->
<?php if ($surec): ?>
<section style="background:var(--light)">
  <div class="container">
    <div class="section-head center">
      <span class="mini"><?= t('surec_alt') ?></span>
      <h2><?= t('surec_baslik') ?></h2>
    </div>
    <div class="surec-grid">
      <?php foreach ($surec as $s): ?>
      <div class="surec-item">
        <span class="sr-no"><?= e($s['etiket']) ?></span>
        <div>
          <h5><?= e(tf($s, 'baslik')) ?></h5>
          <p><?= e(tf($s, 'ozet')) ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ===== STRATEJİK İŞ BİRLİKLERİ ===== -->
<?php if (ta('isbirligi')): ?>
<section>
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-7">
        <div class="section-head" style="margin-bottom:1rem">
          <span class="mini"><?= t('isbirligi_alt') ?></span>
          <h2><?= t('isbirligi_baslik') ?></h2>
        </div>
        <div class="rich-text"><?= zenginMetin(ta('isbirligi')) ?></div>
      </div>
      <div class="col-lg-5">
        <div class="about-img-wrap">
          <img src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=900&q=80" alt="<?= t('isbirligi_baslik') ?>">
        </div>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ===== ODAK BÖLGELER ===== -->
<?php if ($bolgeler): ?>
<section class="bolge-band">
  <div class="container">
    <div class="section-head center">
      <span class="mini"><?= t('bolge_alt') ?></span>
      <h2><?= t('bolge_baslik') ?></h2>
    </div>
    <div class="bolge-grid">
      <?php foreach ($bolgeler as $b): ?>
      <div class="bolge-item">
        <i class="bi <?= e($b['ikon']) ?>"></i>
        <h5><?= e(tf($b, 'baslik')) ?></h5>
        <span><?= e(tf($b, 'ozet')) ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ===== YÖNETİM KADROSU (kayıt girildiğinde görünür) ===== -->
<?php if ($yoneticiler): ?>
<section>
  <div class="container">
    <div class="section-head center">
      <span class="mini"><?= t('nav_kurumsal') ?></span>
      <h2><?= t('yonetim') ?></h2>
    </div>
    <div class="row g-4 justify-content-center">
      <?php foreach ($yoneticiler as $y): ?>
      <div class="col-lg-3 col-md-6 col-6">
        <a class="team-card" href="<?= SITE_URL ?>/yonetici-detay?id=<?= (int)$y['id'] ?>">
          <?php if ($y['foto']): ?><img src="<?= e(gorselUrl($y['foto'])) ?>" alt="<?= e($y['ad']) ?>"><?php else: ?><div class="tc-bos"><i class="bi bi-person"></i></div><?php endif; ?>
          <div class="tb">
            <h6><?= e($y['ad']) ?></h6>
            <span><?= e(tf($y, 'unvan')) ?></span>
            <span class="tc-link"><?= t('devamini_oku') ?> <i class="bi bi-arrow-right"></i></span>
          </div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="cta-strip">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-8"><h3><?= t('bize_ulasin') ?></h3><p><?= e(ta('kapanis')) ?></p></div>
      <div class="col-lg-4 text-lg-end mt-3 mt-lg-0"><a href="<?= SITE_URL ?>/iletisim" class="btn"><?= t('nav_iletisim') ?> <i class="bi bi-arrow-right ms-2"></i></a></div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
