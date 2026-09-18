<?php
require_once __DIR__ . '/inc/crud.php';
$id = (int)($_GET['id'] ?? 0);
$p = ['id'=>0,'baslik'=>'','baslik_en'=>'','slug'=>'','tur'=>'ortak','kategori'=>'','kategori_en'=>'','ulke'=>'','ulke_en'=>'','gorsel'=>'','aciklama'=>'','aciklama_en'=>'','website'=>'','tarih'=>'','sira'=>0,'durum'=>1];
if($id){ $stmt = $db->prepare("SELECT * FROM projeler WHERE id=?"); $stmt->execute([$id]); $p = $stmt->fetch() ?: $p; }

// Kategori önerileri — faaliyet alanı başlıklarıyla eşleşirse detay sayfalarında otomatik bağlanır
$faaliyetler = $db->query("SELECT baslik, baslik_en FROM hizmetler ORDER BY sira ASC")->fetchAll();

if($_SERVER['REQUEST_METHOD']==='POST'){
    csrf_check();
    $data = [
        'baslik'      => trim($_POST['baslik']),
        'baslik_en'   => trim($_POST['baslik_en'] ?? ''),
        'slug'        => slugify($_POST['slug'] ?: $_POST['baslik']),
        'tur'         => ($_POST['tur'] ?? 'ortak') === 'grup' ? 'grup' : 'ortak',
        'kategori'    => trim($_POST['kategori']),
        'kategori_en' => trim($_POST['kategori_en'] ?? ''),
        'ulke'        => trim($_POST['ulke'] ?? ''),
        'ulke_en'     => trim($_POST['ulke_en'] ?? ''),
        'gorsel'      => adminGorselYukle('gorsel_file', $p['gorsel']) ?: trim($_POST['gorsel_url'] ?? $p['gorsel']),
        'aciklama'    => trim($_POST['aciklama']),
        'aciklama_en' => trim($_POST['aciklama_en'] ?? ''),
        'website'     => trim($_POST['website'] ?? ''),
        'tarih'       => trim($_POST['tarih']),
        'sira'        => (int)$_POST['sira'],
        'durum'       => isset($_POST['durum']) ? 1 : 0,
    ];
    if($id){
        $stmt = $db->prepare("UPDATE projeler SET baslik=:baslik,baslik_en=:baslik_en,slug=:slug,tur=:tur,kategori=:kategori,kategori_en=:kategori_en,ulke=:ulke,ulke_en=:ulke_en,gorsel=:gorsel,aciklama=:aciklama,aciklama_en=:aciklama_en,website=:website,tarih=:tarih,sira=:sira,durum=:durum WHERE id=:id");
        $data['id']=$id; $stmt->execute($data);
        adminFlash('İş ortağı güncellendi.');
    } else {
        $stmt = $db->prepare("INSERT INTO projeler(baslik,baslik_en,slug,tur,kategori,kategori_en,ulke,ulke_en,gorsel,aciklama,aciklama_en,website,tarih,sira,durum) VALUES(:baslik,:baslik_en,:slug,:tur,:kategori,:kategori_en,:ulke,:ulke_en,:gorsel,:aciklama,:aciklama_en,:website,:tarih,:sira,:durum)");
        $stmt->execute($data);
        adminFlash('İş ortağı eklendi.');
    }
    header('Location: ' . SITE_URL . '/admin/projeler.php'); exit;
}
$adminTitle = $id ? 'İş Ortağı Düzenle' : 'Yeni İş Ortağı';
require_once __DIR__ . '/inc/header.php';
?>
<form method="post" enctype="multipart/form-data" class="card p-4">
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

  <ul class="nav nav-tabs lang-tabs" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tr" type="button">🇹🇷 Türkçe</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#en" type="button">🇬🇧 English</button></li>
  </ul>

  <div class="tab-content">
    <div class="tab-pane fade show active" id="tr">
      <div class="row g-3">
        <div class="col-md-7"><label class="form-label">Şirket / İş Ortağı Adı *</label><input class="form-control" name="baslik" value="<?= e($p['baslik']) ?>" required></div>
        <div class="col-md-5">
          <label class="form-label">Sektör</label>
          <input class="form-control" name="kategori" list="faaliyetListe" value="<?= e($p['kategori']) ?>" placeholder="Faaliyet alanı başlığıyla aynı yazılırsa otomatik bağlanır">
          <datalist id="faaliyetListe"><?php foreach($faaliyetler as $f): ?><option value="<?= e($f['baslik']) ?>"><?php endforeach; ?></datalist>
        </div>
        <div class="col-md-4"><label class="form-label">Ülke</label><input class="form-control" name="ulke" value="<?= e($p['ulke']) ?>" placeholder="Umman / Türkiye / BAE"></div>
        <div class="col-12"><label class="form-label">Tanıtım Metni</label><textarea class="form-control" name="aciklama" rows="6"><?= e($p['aciklama']) ?></textarea></div>
      </div>
    </div>
    <div class="tab-pane fade" id="en">
      <p class="en-hint"><i class="bi bi-info-circle me-1"></i>Boş bırakılan alanlar sitede otomatik olarak Türkçesiyle gösterilir.</p>
      <div class="row g-3">
        <div class="col-md-7"><label class="form-label">Company Name</label><input class="form-control" name="baslik_en" value="<?= e($p['baslik_en']) ?>"></div>
        <div class="col-md-5"><label class="form-label">Sector</label><input class="form-control" name="kategori_en" value="<?= e($p['kategori_en']) ?>"></div>
        <div class="col-md-4"><label class="form-label">Country</label><input class="form-control" name="ulke_en" value="<?= e($p['ulke_en']) ?>" placeholder="Oman / Türkiye / UAE"></div>
        <div class="col-12"><label class="form-label">Description</label><textarea class="form-control" name="aciklama_en" rows="6"><?= e($p['aciklama_en']) ?></textarea></div>
      </div>
    </div>
  </div>

  <hr class="my-4">
  <div class="row g-3">
    <div class="col-md-3">
      <label class="form-label">Tür *</label>
      <select class="form-select" name="tur">
        <option value="grup"  <?= $p['tur']==='grup'  ? 'selected' : '' ?>>Grup Şirketi</option>
        <option value="ortak" <?= $p['tur']==='ortak' ? 'selected' : '' ?>>İş Ortağı</option>
      </select>
      <div class="form-text">Sitede ayrı başlıklar altında listelenir.</div>
    </div>
    <div class="col-md-4"><label class="form-label">Web Sitesi</label><input class="form-control" name="website" value="<?= e($p['website']) ?>" placeholder="https://"></div>
    <div class="col-md-3"><label class="form-label">Slug</label><input class="form-control" name="slug" value="<?= e($p['slug']) ?>"></div>
    <div class="col-md-2"><label class="form-label">Kuruluş Yılı</label><input class="form-control" name="tarih" value="<?= e($p['tarih']) ?>" placeholder="2015"></div>
    <div class="col-md-1"><label class="form-label">Sıra</label><input type="number" class="form-control" name="sira" value="<?= (int)$p['sira'] ?>"></div>
    <div class="col-md-2 d-flex align-items-end"><div class="form-check"><input type="checkbox" class="form-check-input" id="durum" name="durum" <?= $p['durum']?'checked':'' ?>><label class="form-check-label" for="durum">Aktif</label></div></div>
    <div class="col-md-6"><label class="form-label">Logo / Görsel Yükle</label><input type="file" class="form-control" name="gorsel_file" accept="image/*"></div>
    <div class="col-md-6"><label class="form-label">veya Görsel URL</label><input class="form-control" name="gorsel_url" value="<?= e($p['gorsel']) ?>"></div>
    <?php if($p['gorsel']): ?><div class="col-12"><img src="<?= e(gorselUrl($p['gorsel'])) ?>" style="max-height:120px;border-radius:8px"></div><?php endif; ?>
    <div class="col-12"><button class="btn btn-primary"><i class="bi bi-save"></i> Kaydet</button> <a href="projeler.php" class="btn btn-outline-secondary">İptal</a></div>
  </div>
</form>
<?php require_once __DIR__ . '/inc/footer.php'; ?>
