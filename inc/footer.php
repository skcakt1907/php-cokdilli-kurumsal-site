<footer>
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4 col-md-6">
        <div class="brand">
          <div class="f-lockup">
            <img src="<?= e(logoUrl()) ?>" class="marka-logo" alt="<?= e(ayar('site_adi')) ?>">
            <span>
              <strong>FGG HOLDING</strong>
              <?php if (ayar('resmi_unvan')): ?><small><?= e(ayar('resmi_unvan')) ?></small><?php endif; ?>
            </span>
          </div>
          <?php if (ayar('marka_slogan')): ?>
            <div class="f-marka-slogan"><?= e(ayar('marka_slogan')) ?></div>
          <?php endif; ?>
          <p class="about-text"><?= e(ta('hakkimizda_kisa')) ?></p>
          <div class="social">
            <?php
            $sosyal = ['linkedin' => 'bi-linkedin', 'instagram' => 'bi-instagram', 'facebook' => 'bi-facebook', 'twitter' => 'bi-twitter-x', 'youtube' => 'bi-youtube'];
            foreach ($sosyal as $anahtar => $ikon):
                if (!ayar($anahtar)) continue; ?>
              <a href="<?= e(ayar($anahtar)) ?>" target="_blank" rel="noopener" aria-label="<?= e($anahtar) ?>"><i class="bi <?= $ikon ?>"></i></a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <div class="col-lg-2 col-md-6">
        <h5><?= t('footer_kurumsal') ?></h5>
        <a href="<?= SITE_URL ?>/kurumsal"><?= t('nav_kurumsal') ?></a>
        <?php if (ta('baskan_mesaji')): ?><a href="<?= SITE_URL ?>/baskan-mesaji"><?= e(ta('baskan_mesaj_baslik')) ?></a><?php endif; ?>
        <a href="<?= SITE_URL ?>/faaliyet-alanlari"><?= t('nav_faaliyet') ?></a>
        <a href="<?= SITE_URL ?>/is-ortaklari"><?= t('nav_ortaklar') ?></a>
        <?php if (!empty($haberVar)): ?><a href="<?= SITE_URL ?>/haberler"><?= t('nav_haberler') ?></a><?php endif; ?>
        <a href="<?= SITE_URL ?>/iletisim"><?= t('nav_iletisim') ?></a>
      </div>

      <div class="col-lg-3 col-md-6">
        <h5><?= t('nav_faaliyet') ?></h5>
        <?php
        // Menüdeki sektör aileleri — 23 alanın tamamı yerine grup başlıkları
        $gruplar = $db->query("SELECT DISTINCT grup, grup_en, MIN(sira) s FROM hizmetler WHERE durum=1 GROUP BY grup, grup_en ORDER BY s")->fetchAll();
        foreach ($gruplar as $g): ?>
          <a href="<?= SITE_URL ?>/faaliyet-alanlari#<?= e(slugify($g['grup'])) ?>"><?= e(tf($g, 'grup')) ?></a>
        <?php endforeach; ?>
      </div>

      <div class="col-lg-3 col-md-6">
        <h5><?= t('nav_iletisim') ?></h5>
        <ul class="f-contact p-0 m-0">
          <?php if (ta('adres')): ?><li><i class="bi bi-geo-alt-fill"></i><span><?= e(ta('adres')) ?></span></li><?php endif; ?>
          <?php if (ayar('telefon')): ?><li><i class="bi bi-telephone-fill"></i><a href="tel:<?= e(ayar('telefon')) ?>" style="display:inline;padding:0"><?= e(ayar('telefon')) ?></a></li><?php endif; ?>
          <?php if (ayar('whatsapp')): ?><li><i class="bi bi-whatsapp"></i><a href="https://wa.me/<?= e(preg_replace('/\D/', '', ayar('whatsapp'))) ?>" target="_blank" rel="noopener" style="display:inline;padding:0"><?= e(ayar('whatsapp')) ?></a></li><?php endif; ?>
          <?php if (ayar('mail')): ?><li><i class="bi bi-envelope-fill"></i><a href="mailto:<?= e(ayar('mail')) ?>" style="display:inline;padding:0"><?= e(ayar('mail')) ?></a></li><?php endif; ?>
          <?php if (ta('calisma_saati')): ?><li><i class="bi bi-clock-fill"></i><span><?= e(ta('calisma_saati')) ?></span></li><?php endif; ?>
        </ul>
      </div>
    </div>

    <div class="f-bottom">
      <span>&copy; <?= date('Y') ?> <?= e(ayar('site_adi')) ?>. <?= t('footer_haklar') ?></span>
      <span>Design &amp; Development <a href="https://ornek.com" target="_blank" rel="noopener">DN Kreatif</a></span>
    </div>
  </div>
</footer>

<?php if (ayar('whatsapp')): ?>
<a class="wa-float" href="https://wa.me/<?= e(preg_replace('/\D/', '', ayar('whatsapp'))) ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= SITE_URL ?>/js/main.js"></script>
</body>
</html>
