<?php
$adminTitle = 'Site Ayarları';
require_once __DIR__ . '/inc/crud.php';

// Panelden yazılmasına izin verilen ayar anahtarları (beyaz liste)
$IZINLI = [
    'site_adi','resmi_unvan','marka_slogan','site_baslik','site_baslik_en','site_aciklama','site_aciklama_en',
    'slogan','slogan_en','hero_alt','hero_alt_en','sektor_seridi','sektor_seridi_en','bolgeler_seridi',
    'telefon','telefon2','whatsapp','mail','adres','adres_en','calisma_saati','calisma_saati_en','harita_url',
    'adres_etiket','adres_etiket_en','telefonlar','telefonlar_en','departman_mailler','departman_mailler_en','grup_mailler','grup_mailler_en',
    'hakkimizda_kisa','hakkimizda_kisa_en','hakkimizda_uzun','hakkimizda_uzun_en','yaklasim','yaklasim_en',
    'vizyon_baslik','vizyon_baslik_en','vizyon','vizyon_en','misyon','misyon_en',
    'baskan_mesaji','baskan_mesaji_en','baskan_mesaj_baslik','baskan_mesaj_baslik_en',
    'baskan_mesaj_alt','baskan_mesaj_alt_en',
    'isbirligi','isbirligi_en','kapanis','kapanis_en',
    'yil','sirket_sayi','personel_sayi','sektor_sayi',
    'facebook','instagram','twitter','linkedin','youtube',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $stmt = $db->prepare("INSERT INTO ayarlar(anahtar,deger) VALUES(?,?) ON DUPLICATE KEY UPDATE deger=VALUES(deger)");

    // Logo yüklemesi — dosya seçildiyse mevcut değerin üstüne yazar
    $mevcutLogo = $db->query("SELECT deger FROM ayarlar WHERE anahtar='logo'")->fetchColumn() ?: '';
    if (!empty($_POST['logo_sil'])) {
        $stmt->execute(['logo', '']);
    } else {
        $yeniLogo = adminGorselYukle('logo_file', $mevcutLogo);
        if ($yeniLogo !== $mevcutLogo) $stmt->execute(['logo', $yeniLogo]);
    }

    foreach ($IZINLI as $anahtar) {
        if (!array_key_exists($anahtar, $_POST)) continue;
        $stmt->execute([$anahtar, trim($_POST[$anahtar])]);
    }
    adminFlash('Ayarlar kaydedildi.');
    header('Location: ' . SITE_URL . '/admin/ayarlar.php'); exit;
}

$a = [];
foreach ($db->query("SELECT * FROM ayarlar")->fetchAll() as $r) $a[$r['anahtar']] = $r['deger'];
$v = fn($k) => e($a[$k] ?? '');
$flash = adminFlashCek();
require_once __DIR__ . '/inc/header.php';
?>
<?php if ($flash): ?><div class="alert alert-success"><?= e($flash['msg']) ?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data" class="card p-4">
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

  <h5 class="mb-3"><i class="bi bi-image me-2"></i>Logo</h5>
  <div class="row g-3 mb-4 align-items-start">
    <div class="col-md-5">
      <label class="form-label">Logo Yükle</label>
      <input type="file" class="form-control" name="logo_file" accept="image/png,image/jpeg,image/webp">
      <div class="form-text">
        Şeffaf zeminli <strong>PNG</strong> önerilir. Site zemini siyah olduğu için
        <strong>açık renkli / altın</strong> bir logo kullanın. Yüklemezseniz mevcut logo korunur.
      </div>
      <?php if ($v('logo')): ?>
        <div class="form-check mt-3">
          <input type="checkbox" class="form-check-input" id="logo_sil" name="logo_sil" value="1">
          <label class="form-check-label" for="logo_sil">Yüklenen logoyu sil, varsayılana dön</label>
        </div>
      <?php endif; ?>
    </div>
    <div class="col-md-4">
      <label class="form-label">Sitede Nasıl Görünüyor</label>
      <div class="p-3 rounded text-center" style="background:#0b0b0b;border:1px solid #e2e8f0">
        <img src="<?= e(logoUrl()) ?>" alt="logo" style="max-height:80px;max-width:100%">
      </div>
      <div class="form-text"><?= $v('logo') ? 'Panelden yüklendi' : 'Varsayılan: img/logo-fgg-256.png' ?></div>
    </div>
  </div>

  <h5 class="mb-3"><i class="bi bi-building me-2"></i>Kimlik ve SEO</h5>
  <div class="row g-3 mb-4">
    <div class="col-md-6"><label class="form-label">Site Adı</label><input class="form-control" name="site_adi" value="<?= $v('site_adi') ?>"></div>
    <div class="col-md-4"><label class="form-label">Resmî Ünvan</label><input class="form-control" name="resmi_unvan" value="<?= $v('resmi_unvan') ?>" placeholder="Future Gate Gulf Holding"></div>
    <div class="col-md-4"><label class="form-label">Marka Sloganı <small class="text-muted">(footer)</small></label><input class="form-control" name="marka_slogan" value="<?= $v('marka_slogan') ?>" placeholder="VISION • INTEGRITY • GROWTH"></div>
    <div class="col-md-6"><label class="form-label">Site Başlık (SEO) — TR</label><input class="form-control" name="site_baslik" value="<?= $v('site_baslik') ?>"></div>
    <div class="col-md-6"><label class="form-label">Site Title (SEO) — EN</label><input class="form-control" name="site_baslik_en" value="<?= $v('site_baslik_en') ?>"></div>
    <div class="col-md-6"><label class="form-label">Site Açıklama (SEO) — TR</label><textarea class="form-control" name="site_aciklama" rows="2"><?= $v('site_aciklama') ?></textarea></div>
    <div class="col-md-6"><label class="form-label">Site Description (SEO) — EN</label><textarea class="form-control" name="site_aciklama_en" rows="2"><?= $v('site_aciklama_en') ?></textarea></div>
  </div>

  <h5 class="mb-3"><i class="bi bi-window-fullscreen me-2"></i>Anasayfa Hero</h5>
  <div class="row g-3 mb-4">
    <div class="col-md-6"><label class="form-label">Slogan — TR</label><input class="form-control" name="slogan" value="<?= $v('slogan') ?>"></div>
    <div class="col-md-6"><label class="form-label">Slogan — EN</label><input class="form-control" name="slogan_en" value="<?= $v('slogan_en') ?>"></div>
    <div class="col-md-6"><label class="form-label">Hero Alt Metni — TR</label><textarea class="form-control" name="hero_alt" rows="2"><?= $v('hero_alt') ?></textarea></div>
    <div class="col-md-6"><label class="form-label">Hero Alt Metni — EN</label><textarea class="form-control" name="hero_alt_en" rows="2"><?= $v('hero_alt_en') ?></textarea></div>
    <div class="col-md-6"><label class="form-label">Sektör Şeridi — TR</label><input class="form-control" name="sektor_seridi" value="<?= $v('sektor_seridi') ?>" placeholder="Enerji • Ticaret • Teknoloji"></div>
    <div class="col-md-6"><label class="form-label">Sektör Şeridi — EN</label><input class="form-control" name="sektor_seridi_en" value="<?= $v('sektor_seridi_en') ?>"></div>
    <div class="col-12"><label class="form-label">Üst Şerit Bölgeleri</label><input class="form-control" name="bolgeler_seridi" value="<?= $v('bolgeler_seridi') ?>" placeholder="Oman | GCC | Middle East | Europe | Asia | Africa"></div>
  </div>

  <h5 class="mb-3"><i class="bi bi-telephone me-2"></i>İletişim</h5>
  <div class="row g-3 mb-4">
    <div class="col-md-3"><label class="form-label">Telefon 1</label><input class="form-control" name="telefon" value="<?= $v('telefon') ?>"></div>
    <div class="col-md-3"><label class="form-label">Telefon 2</label><input class="form-control" name="telefon2" value="<?= $v('telefon2') ?>"></div>
    <div class="col-md-3"><label class="form-label">WhatsApp</label><input class="form-control" name="whatsapp" value="<?= $v('whatsapp') ?>" placeholder="+968 ..."></div>
    <div class="col-md-3"><label class="form-label">E-posta</label><input class="form-control" type="email" name="mail" value="<?= $v('mail') ?>"></div>
    <div class="col-md-3"><label class="form-label">Adres etiketi — TR</label><input class="form-control" name="adres_etiket" value="<?= $v('adres_etiket') ?>" placeholder="Merkez Ofis"></div>
    <div class="col-md-3"><label class="form-label">Address label — EN</label><input class="form-control" name="adres_etiket_en" value="<?= $v('adres_etiket_en') ?>" placeholder="Head Office"></div>
    <div class="col-md-6"><label class="form-label">Adres — TR</label><input class="form-control" name="adres" value="<?= $v('adres') ?>"></div>
    <div class="col-md-6"><label class="form-label">Address — EN</label><input class="form-control" name="adres_en" value="<?= $v('adres_en') ?>"></div>

    <div class="col-12"><hr class="my-2"></div>
    <div class="col-12">
      <div class="alert alert-secondary py-2 mb-2" style="font-size:.86rem">
        <strong>Liste biçimi:</strong> her satıra bir kayıt —
        <code>Etiket|değer</code>. WhatsApp numarası için satır sonuna
        <code>|wa</code> ekleyin: <code>BAE (WhatsApp)|+971 54 580 9889|wa</code>.
        Satır ekleyip çıkararak istediğiniz kadar numara/adres tanımlayabilirsiniz.
      </div>
    </div>
    <div class="col-md-6">
      <label class="form-label">Telefon listesi — TR</label>
      <textarea class="form-control" name="telefonlar" rows="5" placeholder="Umman|+968 72 71 0311"><?= $v('telefonlar') ?></textarea>
    </div>
    <div class="col-md-6">
      <label class="form-label">Phone list — EN</label>
      <textarea class="form-control" name="telefonlar_en" rows="5" placeholder="Oman|+968 72 71 0311"><?= $v('telefonlar_en') ?></textarea>
    </div>
    <div class="col-md-6">
      <label class="form-label">Departman e-postaları — TR</label>
      <textarea class="form-control" name="departman_mailler" rows="5" placeholder="Enerji|energy@ornek-holding.com"><?= $v('departman_mailler') ?></textarea>
    </div>
    <div class="col-md-6">
      <label class="form-label">Sektör ailesi e-postaları — TR</label>
      <textarea class="form-control" name="grup_mailler" rows="6" placeholder="Enerji|energy@ornek-holding.com"><?= $v('grup_mailler') ?></textarea>
      <div class="form-text">Etiket, <strong>Faaliyet Alanları</strong>ndaki grup adıyla birebir aynı olmalı.</div>
    </div>
    <div class="col-md-6">
      <label class="form-label">Sector family e-mails — EN</label>
      <textarea class="form-control" name="grup_mailler_en" rows="6"><?= $v('grup_mailler_en') ?></textarea>
    </div>
    <div class="col-md-6">
      <label class="form-label">Department e-mails — EN</label>
      <textarea class="form-control" name="departman_mailler_en" rows="5" placeholder="Energy|energy@ornek-holding.com"><?= $v('departman_mailler_en') ?></textarea>
    </div>
    <div class="col-md-6"><label class="form-label">Çalışma Saatleri — TR</label><input class="form-control" name="calisma_saati" value="<?= $v('calisma_saati') ?>"></div>
    <div class="col-md-6"><label class="form-label">Working Hours — EN</label><input class="form-control" name="calisma_saati_en" value="<?= $v('calisma_saati_en') ?>"></div>
    <div class="col-12">
      <label class="form-label">Google Haritalar Embed Adresi</label>
      <input class="form-control" name="harita_url" value="<?= $v('harita_url') ?>" placeholder="https://www.google.com/maps/embed?pb=...">
      <div class="form-text">Google Haritalar &rsaquo; Paylaş &rsaquo; Haritayı yerleştir &rsaquo; HTML kodundaki <code>src="..."</code> adresi. Yalnızca google.com/maps/embed adresleri kabul edilir.</div>
    </div>
  </div>

  <h5 class="mb-3"><i class="bi bi-info-circle me-2"></i>Kurumsal Metinler</h5>
  <div class="alert alert-light border" style="font-size:.86rem">
    <i class="bi bi-lightbulb me-1"></i>Uzun metin alanlarında satır başına <code>- </code> yazarsanız sitede madde listesi olarak görünür. Boş satır yeni paragraf açar.
  </div>
  <div class="row g-3 mb-4">
    <div class="col-md-6"><label class="form-label">Kısa Tanıtım — TR <small class="text-muted">(hero altı + footer)</small></label><textarea class="form-control" name="hakkimizda_kisa" rows="3"><?= $v('hakkimizda_kisa') ?></textarea></div>
    <div class="col-md-6"><label class="form-label">Short Intro — EN</label><textarea class="form-control" name="hakkimizda_kisa_en" rows="3"><?= $v('hakkimizda_kisa_en') ?></textarea></div>
    <div class="col-md-6"><label class="form-label">Biz Kimiz / Uzun Metin — TR</label><textarea class="form-control" name="hakkimizda_uzun" rows="7"><?= $v('hakkimizda_uzun') ?></textarea></div>
    <div class="col-md-6"><label class="form-label">Who We Are — EN</label><textarea class="form-control" name="hakkimizda_uzun_en" rows="7"><?= $v('hakkimizda_uzun_en') ?></textarea></div>
    <div class="col-md-6">
      <label class="form-label">Temel Yaklaşım — TR</label>
      <input class="form-control" name="yaklasim" value="<?= $v('yaklasim') ?>">
      <div class="form-text"><code>+</code> ile ayrılan her parça anasayfada ayrı bir rozet olur.</div>
    </div>
    <div class="col-md-6"><label class="form-label">Core Approach — EN</label><input class="form-control" name="yaklasim_en" value="<?= $v('yaklasim_en') ?>"></div>
    <div class="col-md-6"><label class="form-label">Vizyon Başlığı — TR</label><input class="form-control" name="vizyon_baslik" value="<?= $v('vizyon_baslik') ?>"></div>
    <div class="col-md-6"><label class="form-label">Vision Heading — EN</label><input class="form-control" name="vizyon_baslik_en" value="<?= $v('vizyon_baslik_en') ?>"></div>
    <div class="col-md-6"><label class="form-label">Vizyon Metni — TR</label><textarea class="form-control" name="vizyon" rows="6"><?= $v('vizyon') ?></textarea></div>
    <div class="col-md-6"><label class="form-label">Vision Text — EN</label><textarea class="form-control" name="vizyon_en" rows="6"><?= $v('vizyon_en') ?></textarea></div>
    <div class="col-md-6"><label class="form-label">Misyon — TR</label><textarea class="form-control" name="misyon" rows="8"><?= $v('misyon') ?></textarea></div>
    <div class="col-md-6"><label class="form-label">Mission — EN</label><textarea class="form-control" name="misyon_en" rows="8"><?= $v('misyon_en') ?></textarea></div>
  </div>

  <hr class="my-4">
  <h5 class="mb-3"><i class="bi bi-quote me-2"></i>Başkan'ın Mesajı <span class="text-secondary" style="font-size:.8rem;font-weight:400">— /baskan-mesaji sayfası</span></h5>
  <div class="row g-3">
    <div class="col-12">
      <div class="alert alert-secondary py-2 mb-1" style="font-size:.86rem">
        Metin biçimi: boş satır yeni paragraf açar, <code>## Başlık</code> ara başlık yapar,
        <code>- madde</code> satırı listeye dönüşür. İmza ve unvan
        <strong>Yönetim Kadrosu</strong>ndaki ilk kayıttan otomatik gelir, buraya yazma.
        Metni boş bırakırsan sayfa bağlantıları da gizlenir.
      </div>
    </div>
    <div class="col-md-6"><label class="form-label">Sayfa Başlığı — TR</label><input class="form-control" name="baskan_mesaj_baslik" value="<?= $v('baskan_mesaj_baslik') ?>"></div>
    <div class="col-md-6"><label class="form-label">Page Title — EN</label><input class="form-control" name="baskan_mesaj_baslik_en" value="<?= $v('baskan_mesaj_baslik_en') ?>"></div>
    <div class="col-md-6"><label class="form-label">Üst Etiket — TR</label><input class="form-control" name="baskan_mesaj_alt" value="<?= $v('baskan_mesaj_alt') ?>"></div>
    <div class="col-md-6"><label class="form-label">Kicker — EN</label><input class="form-control" name="baskan_mesaj_alt_en" value="<?= $v('baskan_mesaj_alt_en') ?>"></div>
    <div class="col-md-6"><label class="form-label">Mesaj Metni — TR</label><textarea class="form-control" name="baskan_mesaji" rows="16"><?= $v('baskan_mesaji') ?></textarea></div>
    <div class="col-md-6"><label class="form-label">Message — EN</label><textarea class="form-control" name="baskan_mesaji_en" rows="16"><?= $v('baskan_mesaji_en') ?></textarea></div>
    <div class="col-md-6"><label class="form-label">Stratejik İş Birlikleri — TR</label><textarea class="form-control" name="isbirligi" rows="5"><?= $v('isbirligi') ?></textarea></div>
    <div class="col-md-6"><label class="form-label">Strategic Collaborations — EN</label><textarea class="form-control" name="isbirligi_en" rows="5"><?= $v('isbirligi_en') ?></textarea></div>
    <div class="col-md-6"><label class="form-label">Kapanış Cümlesi — TR <small class="text-muted">(CTA şeridi)</small></label><textarea class="form-control" name="kapanis" rows="3"><?= $v('kapanis') ?></textarea></div>
    <div class="col-md-6"><label class="form-label">Closing Statement — EN</label><textarea class="form-control" name="kapanis_en" rows="3"><?= $v('kapanis_en') ?></textarea></div>
  </div>

  <h5 class="mb-3"><i class="bi bi-bar-chart me-2"></i>Rakamlarla FGG <small class="text-muted fw-normal">(0 olan sayaç gösterilmez)</small></h5>
  <div class="row g-3 mb-4">
    <div class="col-md-3"><label class="form-label">Yıllık Tecrübe</label><input class="form-control" name="yil" value="<?= $v('yil') ?>"></div>
    <div class="col-md-3"><label class="form-label">Çalışan Sayısı</label><input class="form-control" name="personel_sayi" value="<?= $v('personel_sayi') ?>"></div>
    <div class="col-md-3"><label class="form-label">İş Ortağı Sayısı</label><input class="form-control" name="sirket_sayi" value="<?= $v('sirket_sayi') ?>"></div>
    <div class="col-md-3"><label class="form-label">Faaliyet Alanı Sayısı</label><input class="form-control" name="sektor_sayi" value="<?= $v('sektor_sayi') ?>"></div>
  </div>

  <h5 class="mb-3"><i class="bi bi-share me-2"></i>Sosyal Medya <small class="text-muted fw-normal">(boş bırakılan ikon gösterilmez)</small></h5>
  <div class="row g-3 mb-4">
    <div class="col-md-4"><label class="form-label">LinkedIn</label><input class="form-control" name="linkedin" value="<?= $v('linkedin') ?>"></div>
    <div class="col-md-4"><label class="form-label">Instagram</label><input class="form-control" name="instagram" value="<?= $v('instagram') ?>"></div>
    <div class="col-md-4"><label class="form-label">Facebook</label><input class="form-control" name="facebook" value="<?= $v('facebook') ?>"></div>
    <div class="col-md-4"><label class="form-label">X (Twitter)</label><input class="form-control" name="twitter" value="<?= $v('twitter') ?>"></div>
    <div class="col-md-4"><label class="form-label">YouTube</label><input class="form-control" name="youtube" value="<?= $v('youtube') ?>"></div>
  </div>

  <button class="btn btn-primary"><i class="bi bi-save"></i> Tüm Ayarları Kaydet</button>
</form>
<?php require_once __DIR__ . '/inc/footer.php'; ?>
