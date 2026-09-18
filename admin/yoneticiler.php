<?php
$adminTitle = 'Yönetim Kadrosu';
require_once __DIR__ . '/inc/crud.php';

if($_SERVER['REQUEST_METHOD']==='POST'){
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);
    $data = [
        'ad'          => trim($_POST['ad']),
        'unvan'       => trim($_POST['unvan']),
        'unvan_en'    => trim($_POST['unvan_en'] ?? ''),
        'ozgecmis'    => trim($_POST['ozgecmis'] ?? ''),
        'ozgecmis_en' => trim($_POST['ozgecmis_en'] ?? ''),
        'linkedin'    => trim($_POST['linkedin'] ?? ''),
        'foto'        => adminGorselYukle('foto_file') ?: trim($_POST['foto_url'] ?? ''),
        'sira'        => (int)($_POST['sira'] ?? 0),
        'durum'       => isset($_POST['durum']) ? 1 : 0,
    ];
    if($id){
        $eski = $db->prepare("SELECT foto FROM yoneticiler WHERE id=?");
        $eski->execute([$id]); $r = $eski->fetch();
        if(!$data['foto']) $data['foto'] = $r['foto'] ?? '';
        $stmt = $db->prepare("UPDATE yoneticiler SET ad=:ad,unvan=:unvan,unvan_en=:unvan_en,ozgecmis=:ozgecmis,ozgecmis_en=:ozgecmis_en,linkedin=:linkedin,foto=:foto,sira=:sira,durum=:durum WHERE id=:id");
        $data['id']=$id; $stmt->execute($data);
        adminFlash('Yönetici güncellendi.');
    } else {
        $stmt = $db->prepare("INSERT INTO yoneticiler(ad,unvan,unvan_en,ozgecmis,ozgecmis_en,linkedin,foto,sira,durum) VALUES(:ad,:unvan,:unvan_en,:ozgecmis,:ozgecmis_en,:linkedin,:foto,:sira,:durum)");
        $stmt->execute($data);
        adminFlash('Yönetici eklendi.');
    }
    header('Location: ' . SITE_URL . '/admin/yoneticiler.php'); exit;
}

if(isset($_GET['sil'])){
    csrf_check_get();
    $id = (int)$_GET['sil'];
    $stmt = $db->prepare("SELECT foto FROM yoneticiler WHERE id=?");
    $stmt->execute([$id]); $r = $stmt->fetch();
    if($r && $r['foto'] && str_starts_with($r['foto'], UPLOADS_URL.'/')){
        $yol = UPLOADS . '/' . basename($r['foto']);
        if(is_file($yol)) @unlink($yol);
    }
    $db->prepare("DELETE FROM yoneticiler WHERE id=?")->execute([$id]);
    adminFlash('Yönetici silindi.');
    header('Location: ' . SITE_URL . '/admin/yoneticiler.php'); exit;
}

$duzId = (int)($_GET['id'] ?? 0);
$d = ['id'=>0,'ad'=>'','unvan'=>'','unvan_en'=>'','ozgecmis'=>'','ozgecmis_en'=>'','linkedin'=>'','foto'=>'','sira'=>0,'durum'=>1];
if($duzId){ $st=$db->prepare("SELECT * FROM yoneticiler WHERE id=?"); $st->execute([$duzId]); $d=$st->fetch()?:$d; }
$liste = $db->query("SELECT * FROM yoneticiler ORDER BY sira ASC, id ASC")->fetchAll();
$flash = adminFlashCek();
require_once __DIR__ . '/inc/header.php';
?>
<?php if($flash): ?><div class="alert alert-<?= $flash['tip']==='success'?'success':'danger' ?>"><?= e($flash['msg']) ?></div><?php endif; ?>
<div class="row g-4">
  <div class="col-lg-5">
    <form method="post" enctype="multipart/form-data" class="card p-4">
      <h5 class="mb-3"><?= $duzId ? 'Yönetici Düzenle' : 'Yeni Yönetici' ?></h5>
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <input type="hidden" name="id" value="<?= (int)$d['id'] ?>">

      <div class="mb-3"><label class="form-label">Ad Soyad *</label><input class="form-control" name="ad" value="<?= e($d['ad']) ?>" required></div>

      <ul class="nav nav-tabs lang-tabs" role="tablist">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tr" type="button">🇹🇷 TR</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#en" type="button">🇬🇧 EN</button></li>
      </ul>
      <div class="tab-content mb-2">
        <div class="tab-pane fade show active" id="tr">
          <div class="mb-2"><label class="form-label">Ünvan</label><input class="form-control" name="unvan" value="<?= e($d['unvan']) ?>" placeholder="Yönetim Kurulu Başkanı"></div>
          <div class="mb-2"><label class="form-label">Kısa Özgeçmiş</label><textarea class="form-control" name="ozgecmis" rows="3"><?= e($d['ozgecmis']) ?></textarea></div>
        </div>
        <div class="tab-pane fade" id="en">
          <p class="en-hint"><i class="bi bi-info-circle me-1"></i>Boşsa Türkçesi gösterilir.</p>
          <div class="mb-2"><label class="form-label">Title</label><input class="form-control" name="unvan_en" value="<?= e($d['unvan_en']) ?>" placeholder="Chairman of the Board"></div>
          <div class="mb-2"><label class="form-label">Short Bio</label><textarea class="form-control" name="ozgecmis_en" rows="3"><?= e($d['ozgecmis_en']) ?></textarea></div>
        </div>
      </div>

      <div class="row g-2 mb-2">
        <div class="col-8"><label class="form-label">LinkedIn</label><input class="form-control" name="linkedin" value="<?= e($d['linkedin']) ?>" placeholder="https://"></div>
        <div class="col-4"><label class="form-label">Sıra</label><input type="number" class="form-control" name="sira" value="<?= (int)$d['sira'] ?>"></div>
      </div>
      <div class="mb-2"><label class="form-label">Fotoğraf Yükle</label><input type="file" class="form-control" name="foto_file" accept="image/*"></div>
      <div class="mb-2"><label class="form-label">veya Fotoğraf URL</label><input class="form-control" name="foto_url" value="<?= e($d['foto']) ?>"></div>
      <?php if($d['foto']): ?><img src="<?= e(gorselUrl($d['foto'])) ?>" class="mb-2" style="width:70px;height:70px;border-radius:50%;object-fit:cover"><?php endif; ?>
      <div class="form-check mb-3"><input type="checkbox" class="form-check-input" id="durum" name="durum" <?= $d['durum']?'checked':'' ?>><label class="form-check-label" for="durum">Sitede göster</label></div>

      <button class="btn btn-primary"><i class="bi bi-save"></i> Kaydet</button>
      <?php if($duzId): ?><a href="yoneticiler.php" class="btn btn-link">İptal</a><?php endif; ?>
    </form>
  </div>

  <div class="col-lg-7">
    <div class="card">
      <div class="card-header">Yönetim Kadrosu (<?= count($liste) ?>)</div>
      <?php if(!$liste): ?>
        <div class="p-4 text-center text-muted">Henüz yönetici eklenmedi. Kayıt yoksa site üzerindeki "Yönetim Kadromuz" bölümü otomatik gizlenir.</div>
      <?php else: ?>
      <ul class="list-group list-group-flush">
        <?php foreach($liste as $y): ?>
        <li class="list-group-item d-flex align-items-center gap-3">
          <?php if($y['foto']): ?><img src="<?= e(gorselUrl($y['foto'])) ?>" style="width:52px;height:52px;border-radius:50%;object-fit:cover"><?php endif; ?>
          <div class="flex-grow-1">
            <strong><?= e($y['ad']) ?></strong>
            <div class="text-muted" style="font-size:.86rem"><?= e($y['unvan']) ?></div>
            <?php if(!$y['durum']): ?><span class="badge bg-secondary">Pasif</span><?php endif; ?>
          </div>
          <a href="?id=<?= $y['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
          <a href="?sil=<?= $y['id'] ?>&t=<?= csrf_token() ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Silinsin mi?')"><i class="bi bi-trash"></i></a>
        </li>
        <?php endforeach; ?>
      </ul>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/inc/footer.php'; ?>
