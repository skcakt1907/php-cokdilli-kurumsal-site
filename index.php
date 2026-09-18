<?php
require_once __DIR__ . '/inc/header.php';

// Sektör aileleri (grup) + her ailedeki alanlar
$aileler = [];
foreach (getList('hizmetler', 'durum=1', 'sira ASC') as $fa) {
    $anahtar = $fa['grup'] ?: '—';
    $aileler[$anahtar]['ad']      = tf($fa, 'grup');
    $aileler[$anahtar]['slug']    = slugify($fa['grup']);
    $aileler[$anahtar]['ikon']    = $aileler[$anahtar]['ikon'] ?? $fa['ikon'];
    $aileler[$anahtar]['alanlar'][] = $fa;
}

$nedenler = getList('bloklar', "durum=1 AND tip='neden'", 'sira ASC');
$surec    = getList('bloklar', "durum=1 AND tip='surec'", 'sira ASC');
$bolgeler = getList('bloklar', "durum=1 AND tip='bolge'", 'sira ASC');
$haberler = getList('blog', 'durum=1', 'tarih DESC', 3);

$sayaclar = [
    ['bi-award',       ayar('yil'),           t('sayac_yil')],
    ['bi-people',      ayar('personel_sayi'), t('sayac_personel')],
    ['bi-diagram-3',   ayar('sirket_sayi'),   t('sayac_sirket')],
    ['bi-grid-3x3-gap',ayar('sektor_sayi'),   t('sayac_sektor')],
];
$sayacVar = array_sum(array_map(fn($s) => (int)$s[1], $sayaclar)) > 0;
?>

<section class="hero">
  <div class="container">
    <div class="hero-grid">
      <div class="hero-metin">
        <span class="hero-badge"><i class="bi bi-circle-fill"></i> <?= e(ta('sektor_seridi')) ?></span>
        <h1><?= e(ayar('site_adi')) ?></h1>
        <p class="hero-slogan"><?= e(ta('slogan')) ?></p>
        <p class="hero-aciklama"><?= e(ta('hero_alt')) ?></p>
        <div class="hero-cta">
          <a href="<?= SITE_URL ?>/kurumsal" class="btn btn-orange me-md-2"><?= t('biz_kimiz') ?></a>
          <a href="<?= SITE_URL ?>/faaliyet-alanlari" class="btn btn-line"><?= t('nav_faaliyet') ?> <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
      </div>
      <div class="hero-amblem">
        <img src="<?= e(logoUrl()) ?>" class="marka-logo" alt="<?= e(ayar('resmi_unvan')) ?>">
      </div>
    </div>
  </div>

</section>

<!-- ===== BİZ KİMİZ ===== -->
<section>
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-5">
        <div class="about-img-wrap">
          <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=900&q=80" alt="<?= e(ayar('site_adi')) ?>">
        </div>
      </div>
      <div class="col-lg-7">
        <div class="section-head" style="margin-bottom:1.1rem">
          <span class="mini"><?= t('nav_kurumsal') ?></span>
          <h2><?= t('biz_kimiz') ?></h2>
        </div>
        <p class="lead" style="color:var(--gray)"><?= e(ta('hakkimizda_kisa')) ?></p>
        <div class="rich-text"><?= zenginMetin(ta('hakkimizda_uzun')) ?></div>
        <a href="<?= SITE_URL ?>/kurumsal" class="btn btn-orange mt-3"><?= t('devamini_oku') ?> <i class="bi bi-arrow-right ms-1"></i></a>
      </div>
    </div>
  </div>
</section>

<!-- ===== TEMEL YAKLAŞIM (formül şeridi) ===== -->
<?php if (ta('yaklasim')): ?>
<div class="formula-strip">
  <div class="container">
    <span class="fs-label"><?= t('yaklasim_baslik') ?></span>
    <div class="fs-parts">
      <?php foreach (array_map('trim', explode('+', ta('yaklasim'))) as $i => $parca): ?>
        <?php if ($i > 0): ?><span class="fs-plus">+</span><?php endif; ?>
        <span class="fs-part"><?= e($parca) ?></span>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- ===== NELER YAPARIZ (sektör aileleri) ===== -->
<?php if ($aileler): ?>
<section class="services-grid">
  <div class="container">
    <div class="section-head center">
      <span class="mini"><?= t('faaliyet_alt') ?></span>
      <h2><?= t('ne_yapariz') ?></h2>
    </div>
    <div class="row g-4">
      <?php foreach ($aileler as $aile): ?>
      <div class="col-lg-4 col-md-6">
        <a class="family-card" href="<?= SITE_URL ?>/faaliyet-alanlari#<?= e($aile['slug']) ?>">
          <div class="fc-head">
            <div class="ac-icon"><i class="bi <?= e($aile['ikon']) ?>"></i></div>
            <div>
              <h4><?= e($aile['ad']) ?></h4>
              <span class="fc-adet"><?= count($aile['alanlar']) ?> <?= t('sayac_sektor') ?></span>
            </div>
          </div>
          <?php
            // Kartları eşit yükseklikte tutmak için en fazla 4 kalem gösterilir;
            // kalanlar "+N daha" olarak özetlenir (1 kalemli kartla 6 kalemli
            // kart yan yana durunca boşluk dengesizliği oluşuyordu).
            $gosterilecek = array_slice($aile['alanlar'], 0, 4);
            $kalan        = count($aile['alanlar']) - count($gosterilecek);
          ?>
          <ul class="fc-list">
            <?php foreach ($gosterilecek as $fa): ?>
              <li><?= e(tf($fa, 'baslik')) ?></li>
            <?php endforeach; ?>
            <?php if ($kalan > 0): ?>
              <li class="fc-daha">+<?= $kalan ?> <?= t('daha') ?></li>
            <?php endif; ?>
          </ul>
          <span class="ac-link"><?= t('incele') ?> <i class="bi bi-arrow-right"></i></span>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

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

<!-- ===== ULUSLARARASI İŞ AĞI ===== -->
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

<!-- ===== RAKAMLARLA ===== -->
<?php if ($sayacVar): ?>
<section class="stats">
  <div class="container">
    <div class="row g-4 justify-content-center">
      <?php foreach ($sayaclar as [$ikon, $deger, $etiket]): ?>
        <?php if (!(int)$deger) continue; ?>
        <div class="col-md-3 col-6">
          <div class="stat">
            <i class="bi <?= $ikon ?>"></i>
            <div><h3><?= e($deger) ?></h3><p><?= e($etiket) ?></p></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ===== HABERLER ===== -->
<?php if ($haberler): ?>
<section style="background:var(--light)">
  <div class="container">
    <div class="section-head center">
      <span class="mini"><?= e(ayar('site_adi')) ?></span>
      <h2><?= t('nav_haberler') ?></h2>
    </div>
    <div class="row g-4">
      <?php foreach ($haberler as $hb): ?>
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
  </div>
</section>
<?php endif; ?>

<section class="cta-strip">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-8">
        <h3><?= t('bize_ulasin') ?></h3>
        <p><?= e(ta('kapanis')) ?></p>
      </div>
      <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
        <a href="<?= SITE_URL ?>/iletisim" class="btn"><?= t('nav_iletisim') ?> <i class="bi bi-arrow-right ms-2"></i></a>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
