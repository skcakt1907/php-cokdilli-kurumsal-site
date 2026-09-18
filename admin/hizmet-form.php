<?php
require_once __DIR__ . '/inc/crud.php';
$id = (int)($_GET['id'] ?? 0);
$h = ['id'=>0,'baslik'=>'','baslik_en'=>'','slug'=>'','grup'=>'','grup_en'=>'','ozet'=>'','ozet_en'=>'','icerik'=>'','icerik_en'=>'','ikon'=>'bi-building','gorsel'=>'','sira'=>0,'durum'=>1];
if($id){ $stmt = $db->prepare("SELECT * FROM hizmetler WHERE id=?"); $stmt->execute([$id]); $h = $stmt->fetch() ?: $h; }

// Mevcut sektör aileleri — yeni alan eklerken listeden seçilebilsin
$gruplar = $db->query("SELECT DISTINCT grup, grup_en FROM hizmetler WHERE grup<>'' ORDER BY grup")->fetchAll();

if($_SERVER['REQUEST_METHOD']==='POST'){
    csrf_check();
    $data = [
        'baslik'    => trim($_POST['baslik']),
        'baslik_en' => trim($_POST['baslik_en'] ?? ''),
        'slug'      => slugify($_POST['slug'] ?: $_POST['baslik']),
        'grup'      => trim($_POST['grup'] ?? ''),
        'grup_en'   => trim($_POST['grup_en'] ?? ''),
        'ozet'      => trim($_POST['ozet']),
        'ozet_en'   => trim($_POST['ozet_en'] ?? ''),
        'icerik'    => trim($_POST['icerik']),
        'icerik_en' => trim($_POST['icerik_en'] ?? ''),
        'ikon'      => trim($_POST['ikon'] ?: 'bi-building'),
        'gorsel'    => adminGorselYukle('gorsel_file', $h['gorsel']) ?: trim($_POST['gorsel_url'] ?? $h['gorsel']),
        'sira'      => (int)$_POST['sira'],
        'durum'     => isset($_POST['durum']) ? 1 : 0,
    ];
    if($id){
        $stmt = $db->prepare("UPDATE hizmetler SET baslik=:baslik,baslik_en=:baslik_en,slug=:slug,grup=:grup,grup_en=:grup_en,ozet=:ozet,ozet_en=:ozet_en,icerik=:icerik,icerik_en=:icerik_en,ikon=:ikon,gorsel=:gorsel,sira=:sira,durum=:durum WHERE id=:id");
        $data['id']=$id; $stmt->execute($data);
        adminFlash('Faaliyet alanı güncellendi.');
    } else {
        $stmt = $db->prepare("INSERT INTO hizmetler(baslik,baslik_en,slug,grup,grup_en,ozet,ozet_en,icerik,icerik_en,ikon,gorsel,sira,durum) VALUES(:baslik,:baslik_en,:slug,:grup,:grup_en,:ozet,:ozet_en,:icerik,:icerik_en,:ikon,:gorsel,:sira,:durum)");
        $stmt->execute($data);
        adminFlash('Faaliyet alanı eklendi.');
    }
    header('Location: ' . SITE_URL . '/admin/hizmetler.php'); exit;
}
$adminTitle = $id ? 'Faaliyet Alanı Düzenle' : 'Yeni Faaliyet Alanı';
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
        <div class="col-md-7"><label class="form-label">Başlık *</label><input class="form-control" name="baslik" value="<?= e($h['baslik']) ?>" required></div>
        <div class="col-md-5">
          <label class="form-label">Sektör Ailesi *</label>
          <input class="form-control" name="grup" list="grupListe" value="<?= e($h['grup']) ?>" required placeholder="Enerji / Ticaret ve Tedarik ...">
          <datalist id="grupListe"><?php foreach($gruplar as $g): ?><option value="<?= e($g['grup']) ?>"><?php endforeach; ?></datalist>
          <div class="form-text">Menüde ve listede bu başlık altında gruplanır.</div>
        </div>
        <div class="col-12"><label class="form-label">Özet (kartlarda görünür)</label><textarea class="form-control" name="ozet" rows="2"><?= e($h['ozet']) ?></textarea></div>
        <div class="col-12"><label class="form-label">İçerik (detay sayfası)</label><textarea class="form-control" name="icerik" rows="8"><?= e($h['icerik']) ?></textarea></div>
      </div>
    </div>
    <div class="tab-pane fade" id="en">
      <p class="en-hint"><i class="bi bi-info-circle me-1"></i>Boş bırakılan alanlar sitede otomatik olarak Türkçesiyle gösterilir.</p>
      <div class="row g-3">
        <div class="col-md-7"><label class="form-label">Title</label><input class="form-control" name="baslik_en" value="<?= e($h['baslik_en']) ?>"></div>
        <div class="col-md-5"><label class="form-label">Sector Family</label><input class="form-control" name="grup_en" value="<?= e($h['grup_en']) ?>" placeholder="Energy / Trade &amp; Supply ..."></div>
        <div class="col-12"><label class="form-label">Summary</label><textarea class="form-control" name="ozet_en" rows="2"><?= e($h['ozet_en']) ?></textarea></div>
        <div class="col-12"><label class="form-label">Content</label><textarea class="form-control" name="icerik_en" rows="8"><?= e($h['icerik_en']) ?></textarea></div>
      </div>
    </div>
  </div>

  <hr class="my-4">
  <div class="row g-3">
    <div class="col-md-4"><label class="form-label">Slug (boş bırakırsanız otomatik)</label><input class="form-control" name="slug" value="<?= e($h['slug']) ?>"></div>
    <div class="col-md-4"><label class="form-label">İkon (bootstrap-icons)</label><input class="form-control" name="ikon" value="<?= e($h['ikon']) ?>" placeholder="bi-buildings"></div>
    <div class="col-md-2"><label class="form-label">Sıra</label><input type="number" class="form-control" name="sira" value="<?= (int)$h['sira'] ?>"></div>
    <div class="col-md-2 d-flex align-items-end"><div class="form-check"><input type="checkbox" class="form-check-input" id="durum" name="durum" <?= $h['durum']?'checked':'' ?>><label class="form-check-label" for="durum">Aktif</label></div></div>
    <div class="col-md-6"><label class="form-label">Görsel Yükle</label><input type="file" class="form-control" name="gorsel_file" accept="image/*"></div>
    <div class="col-md-6"><label class="form-label">veya Görsel URL</label><input class="form-control" name="gorsel_url" value="<?= e($h['gorsel']) ?>"></div>
    <?php if($h['gorsel']): ?><div class="col-12"><img src="<?= e(gorselUrl($h['gorsel'])) ?>" style="max-height:120px;border-radius:8px"></div><?php endif; ?>
    <div class="col-12"><button class="btn btn-primary"><i class="bi bi-save"></i> Kaydet</button> <a href="hizmetler.php" class="btn btn-outline-secondary">İptal</a></div>
  </div>
</form>
<?php require_once __DIR__ . '/inc/footer.php'; ?>
