<?php
require_once __DIR__ . '/inc/helpers.php';

$formMesaj = '';
$formHata  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $ad   = trim($_POST['ad'] ?? '');
    $mail = trim($_POST['mail'] ?? '');
    $tel  = trim($_POST['tel'] ?? '');
    $konu = trim($_POST['konu'] ?? '');
    $mes  = trim($_POST['mesaj'] ?? '');

    if (mb_strlen($ad) > 100 || mb_strlen($mail) > 150 || mb_strlen($tel) > 40 || mb_strlen($konu) > 200 || mb_strlen($mes) > 3000) {
        $formHata = t('form_hata');
    } elseif (!$ad || !$mail || !$mes) {
        $formHata = t('form_hata');
    } elseif (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $formHata = t('form_hata');
    } else {
        $db->prepare("INSERT INTO mesajlar(ad,mail,tel,konu,mesaj) VALUES(?,?,?,?,?)")
           ->execute([$ad, $mail, $tel, $konu, $mes]);
        $formMesaj = t('form_basarili');
    }
}

$pageTitle = t('nav_iletisim') . ' — ' . ayar('site_adi');
require_once __DIR__ . '/inc/header.php';
?>
<section class="page-head">
  <div class="container">
    <h1><?= t('nav_iletisim') ?></h1>
    <div class="crumb"><a href="<?= SITE_URL ?>/"><?= t('nav_anasayfa') ?></a> &rsaquo; <?= t('nav_iletisim') ?></div>
  </div>
</section>

<section>
  <div class="container">
    <div class="section-head center">
      <span class="mini"><?= t('iletisim_alt') ?></span>
      <h2><?= t('bize_ulasin') ?></h2>
      <p><?= t('iletisim_giris') ?></p>
    </div>

    <div class="row g-4 mb-5">
      <div class="col-lg-3 col-md-6">
        <div class="contact-box">
          <i class="bi bi-geo-alt-fill"></i>
          <h5><?= t('adres') ?></h5>
          <p>
            <?php if (ta('adres_etiket')): ?><span class="ct-etiket"><?= e(ta('adres_etiket')) ?></span><?php endif; ?>
            <?= e(ta('adres')) ?>
          </p>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <div class="contact-box">
          <i class="bi bi-telephone-fill"></i>
          <h5><?= t('telefon') ?></h5>
          <p>
            <?php $telefonlar = etiketliListe(ta('telefonlar')); ?>
            <?php if ($telefonlar): ?>
              <?php foreach ($telefonlar as $tl): ?>
                <span class="ct-etiket"><?= e($tl['etiket']) ?></span>
                <?php if ($tl['bayrak'] === 'wa'): ?>
                  <a href="https://wa.me/<?= e(telRakam($tl['deger'])) ?>" target="_blank" rel="noopener" style="color:inherit"><i class="bi bi-whatsapp me-1"></i><?= e($tl['deger']) ?></a>
                <?php else: ?>
                  <a href="tel:<?= e(telRakam($tl['deger'])) ?>" style="color:inherit"><?= e($tl['deger']) ?></a>
                <?php endif; ?>
                <br>
              <?php endforeach; ?>
            <?php else: /* liste boşsa eski tekil alanlara düş */ ?>
              <a href="tel:<?= e(ayar('telefon')) ?>" style="color:inherit"><?= e(ayar('telefon')) ?></a>
              <?php if (ayar('telefon2')): ?><br><a href="tel:<?= e(ayar('telefon2')) ?>" style="color:inherit"><?= e(ayar('telefon2')) ?></a><?php endif; ?>
              <?php if (ayar('whatsapp') && telRakam(ayar('whatsapp')) !== telRakam(ayar('telefon'))): ?><br><a href="https://wa.me/<?= e(telRakam(ayar('whatsapp'))) ?>" target="_blank" rel="noopener" style="color:inherit"><i class="bi bi-whatsapp me-1"></i><?= e(ayar('whatsapp')) ?></a><?php endif; ?>
            <?php endif; ?>
          </p>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <div class="contact-box">
          <i class="bi bi-envelope-fill"></i>
          <h5><?= t('eposta') ?></h5>
          <p><a href="mailto:<?= e(ayar('mail')) ?>" style="color:inherit"><?= e(ayar('mail')) ?></a></p>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <div class="contact-box">
          <i class="bi bi-clock-fill"></i>
          <h5><?= t('calisma_saati') ?></h5>
          <p><?= e(ta('calisma_saati')) ?></p>
        </div>
      </div>
    </div>

    <!-- ===== DEPARTMAN E-POSTALARI ===== -->
    <?php $departmanlar = etiketliListe(ta('departman_mailler')); ?>
    <?php if ($departmanlar): ?>
    <div class="section-head center" style="margin-bottom:1.4rem">
      <span class="mini"><?= t('departman_alt') ?></span>
      <h2><?= t('departman_baslik') ?></h2>
    </div>
    <div class="departman-grid mb-5">
      <?php foreach ($departmanlar as $dp): ?>
        <a class="departman-kart" href="mailto:<?= e($dp['deger']) ?>">
          <i class="bi bi-envelope-at"></i>
          <span class="dk-etiket"><?= e($dp['etiket']) ?></span>
          <span class="dk-mail"><?= e($dp['deger']) ?></span>
        </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="row g-5 align-items-start">
      <div class="col-lg-6">
        <div class="section-head" style="margin-bottom:1.4rem">
          <span class="mini"><?= t('form_alt') ?></span>
          <h2><?= t('form_baslik') ?></h2>
        </div>

        <?php if ($formMesaj): ?>
          <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i><?= e($formMesaj) ?></div>
        <?php endif; ?>
        <?php if ($formHata): ?>
          <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i><?= e($formHata) ?></div>
        <?php endif; ?>

        <form method="post" novalidate>
          <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label"><?= t('form_ad') ?> *</label>
              <input class="form-control" name="ad" maxlength="100" required value="<?= e($_POST['ad'] ?? '') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label"><?= t('form_mail') ?> *</label>
              <input type="email" class="form-control" name="mail" maxlength="150" required value="<?= e($_POST['mail'] ?? '') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label"><?= t('form_tel') ?></label>
              <input class="form-control" name="tel" maxlength="40" value="<?= e($_POST['tel'] ?? '') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label"><?= t('form_konu') ?></label>
              <input class="form-control" name="konu" maxlength="200" value="<?= e($_POST['konu'] ?? '') ?>">
            </div>
            <div class="col-12">
              <label class="form-label"><?= t('form_mesaj') ?> *</label>
              <textarea class="form-control" name="mesaj" rows="5" maxlength="3000" required><?= e($_POST['mesaj'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-orange"><?= t('form_gonder') ?> <i class="bi bi-send ms-1"></i></button>
            </div>
          </div>
        </form>
      </div>

      <div class="col-lg-6">
        <?php
        // Yalnızca Google Haritalar embed adresi kabul edilir (XSS'e kapalı)
        $haritaUrl = ayar('harita_url');
        $haritaGecerli = $haritaUrl && preg_match('#^https://www\.google\.com/maps/embed#', $haritaUrl);
        if ($haritaGecerli): ?>
          <div class="ratio ratio-4x3 rounded overflow-hidden" style="box-shadow:0 14px 32px rgba(11,31,58,.1)">
            <iframe src="<?= e($haritaUrl) ?>" style="border:0" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="<?= t('adres') ?>"></iframe>
          </div>
        <?php else: ?>
          <div class="vm-card h-100 d-flex flex-column justify-content-center">
            <i class="bi bi-map"></i>
            <h4><?= t('adres') ?></h4>
            <p><?= e(ta('adres')) ?></p>
            <p class="mt-3 mb-0" style="font-size:.85rem;color:#9aa6b5">
              <i class="bi bi-info-circle me-1"></i>
              Harita için: Yönetim Paneli &rsaquo; Ayarlar &rsaquo; Google Haritalar Embed kodu.
            </p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
