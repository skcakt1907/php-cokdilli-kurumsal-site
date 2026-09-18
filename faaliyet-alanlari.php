<?php
require_once __DIR__ . '/inc/helpers.php';
$pageTitle = t('nav_faaliyet') . ' — ' . ayar('site_adi');

// Sektör ailelerine göre grupla (sıra alanı aile sırasını da belirler)
$aileler = [];
foreach (getList('hizmetler', 'durum=1', 'sira ASC') as $fa) {
    $anahtar = $fa['grup'] ?: '—';
    $aileler[$anahtar]['ad']   = tf($fa, 'grup');
    $aileler[$anahtar]['slug'] = slugify($fa['grup']);
    $aileler[$anahtar]['alanlar'][] = $fa;
}
require_once __DIR__ . '/inc/header.php';
?>
<section class="page-head">
  <div class="container">
    <h1><?= t('nav_faaliyet') ?></h1>
    <div class="crumb"><a href="<?= SITE_URL ?>/"><?= t('nav_anasayfa') ?></a> &rsaquo; <?= t('nav_faaliyet') ?></div>
  </div>
</section>

<?php if (!$aileler): ?>
  <section><div class="container"><div class="empty-state"><i class="bi bi-inbox"></i><?= t('bulunamadi') ?></div></div></section>
<?php else: ?>

<!-- Aileler arası hızlı geçiş -->
<div class="anchor-bar">
  <div class="container">
    <?php foreach ($aileler as $aile): ?>
      <a href="#<?= e($aile['slug']) ?>"><?= e($aile['ad']) ?> <span><?= count($aile['alanlar']) ?></span></a>
    <?php endforeach; ?>
  </div>
</div>

<?php $sira = 0; foreach ($aileler as $aile): $sira++; ?>
<section id="<?= e($aile['slug']) ?>" <?= $sira % 2 === 0 ? 'style="background:var(--light)"' : '' ?>>
  <div class="container">
    <div class="section-head">
      <span class="mini"><?= t('faaliyet_alt') ?></span>
      <h2><?= e($aile['ad']) ?></h2>
    </div>

    <?php
      // Sektör ailesine atanmış iletişim adresi (Ayarlar > Grup e-postaları).
      // Eşleştirme grup adı üzerinden yapılır.
      $grupMail = '';
      foreach (etiketliListe(ta('grup_mailler')) as $gm) {
        if (mb_strtolower($gm['etiket']) === mb_strtolower($aile['ad'])) { $grupMail = $gm['deger']; break; }
      }
    ?>
    <?php if ($grupMail): ?>
      <a class="grup-mail" href="mailto:<?= e($grupMail) ?>">
        <i class="bi bi-envelope-at"></i>
        <span class="gm-etiket"><?= t('grup_mail_etiket') ?></span>
        <span class="gm-adres"><?= e($grupMail) ?></span>
      </a>
    <?php endif; ?>
    <div class="row g-4">
      <?php foreach ($aile['alanlar'] as $fa): ?>
      <div class="col-lg-4 col-md-6">
        <a class="area-card h-100" href="<?= SITE_URL ?>/faaliyet-detay?slug=<?= e($fa['slug']) ?>">
          <div class="ac-icon"><i class="bi <?= e($fa['ikon']) ?>"></i></div>
          <h4><?= e(tf($fa, 'baslik')) ?></h4>
          <p><?= e(tf($fa, 'ozet')) ?></p>
          <span class="ac-link"><?= t('incele') ?> <i class="bi bi-arrow-right"></i></span>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endforeach; ?>
<?php endif; ?>

<section class="cta-strip">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-8"><h3><?= t('bize_ulasin') ?></h3><p><?= t('iletisim_alt') ?></p></div>
      <div class="col-lg-4 text-lg-end mt-3 mt-lg-0"><a href="<?= SITE_URL ?>/iletisim" class="btn"><?= t('nav_iletisim') ?> <i class="bi bi-arrow-right ms-2"></i></a></div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
