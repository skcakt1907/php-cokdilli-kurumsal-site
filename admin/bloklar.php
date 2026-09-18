<?php
// Site Blokları — "Neden FGG", "Proje Süreci" ve "Odak Bölgeler" tek sayfadan yönetilir.
$adminTitle = 'Site Blokları';
require_once __DIR__ . '/inc/crud.php';

$TIPLER = [
    'neden' => ['ad' => 'Neden FGG Holding?', 'aciklama' => 'Anasayfa ve Kurumsal sayfasındaki avantaj kartları.',       'etiketVar' => false],
    'surec' => ['ad' => 'Proje Geliştirme Modeli', 'aciklama' => 'Numaralı süreç adımları (01, 02, ...).',               'etiketVar' => true],
    'bolge' => ['ad' => 'Odak Bölgeler', 'aciklama' => 'Uluslararası iş ağı bölümündeki bölge kartları.',                'etiketVar' => false],
];

$tip = $_GET['tip'] ?? $_POST['tip'] ?? 'neden';
if (!isset($TIPLER[$tip])) $tip = 'neden';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);
    $data = [
        'tip'      => $tip,
        'baslik'   => trim($_POST['baslik']),
        'baslik_en'=> trim($_POST['baslik_en'] ?? ''),
        'ozet'     => trim($_POST['ozet'] ?? ''),
        'ozet_en'  => trim($_POST['ozet_en'] ?? ''),
        'ikon'     => trim($_POST['ikon'] ?: 'bi-check-lg'),
        'etiket'   => trim($_POST['etiket'] ?? ''),
        'sira'     => (int)($_POST['sira'] ?? 0),
        'durum'    => isset($_POST['durum']) ? 1 : 0,
    ];
    if ($id) {
        $stmt = $db->prepare("UPDATE bloklar SET tip=:tip,baslik=:baslik,baslik_en=:baslik_en,ozet=:ozet,ozet_en=:ozet_en,ikon=:ikon,etiket=:etiket,sira=:sira,durum=:durum WHERE id=:id");
        $data['id'] = $id; $stmt->execute($data);
        adminFlash('Blok güncellendi.');
    } else {
        $stmt = $db->prepare("INSERT INTO bloklar(tip,baslik,baslik_en,ozet,ozet_en,ikon,etiket,sira,durum) VALUES(:tip,:baslik,:baslik_en,:ozet,:ozet_en,:ikon,:etiket,:sira,:durum)");
        $stmt->execute($data);
        adminFlash('Blok eklendi.');
    }
    header('Location: ' . SITE_URL . '/admin/bloklar.php?tip=' . $tip); exit;
}

if (isset($_GET['sil'])) {
    csrf_check_get();
    $db->prepare("DELETE FROM bloklar WHERE id=?")->execute([(int)$_GET['sil']]);
    adminFlash('Blok silindi.');
    header('Location: ' . SITE_URL . '/admin/bloklar.php?tip=' . $tip); exit;
}

$duzId = (int)($_GET['id'] ?? 0);
$d = ['id'=>0,'tip'=>$tip,'baslik'=>'','baslik_en'=>'','ozet'=>'','ozet_en'=>'','ikon'=>'bi-check-lg','etiket'=>'','sira'=>0,'durum'=>1];
if ($duzId) {
    $st = $db->prepare("SELECT * FROM bloklar WHERE id=?");
    $st->execute([$duzId]);
    $d = $st->fetch() ?: $d;
    $tip = $d['tip'];
}

$stmt = $db->prepare("SELECT * FROM bloklar WHERE tip=? ORDER BY sira ASC, id ASC");
$stmt->execute([$tip]);
$liste = $stmt->fetchAll();
$flash = adminFlashCek();
require_once __DIR__ . '/inc/header.php';
?>
<?php if ($flash): ?><div class="alert alert-<?= $flash['tip']==='success'?'success':'danger' ?>"><?= e($flash['msg']) ?></div><?php endif; ?>

<ul class="nav nav-tabs lang-tabs mb-4">
  <?php foreach ($TIPLER as $kod => $bilgi): ?>
    <li class="nav-item"><a class="nav-link <?= $tip === $kod ? 'active' : '' ?>" href="?tip=<?= $kod ?>"><?= e($bilgi['ad']) ?></a></li>
  <?php endforeach; ?>
</ul>
<p class="text-muted" style="font-size:.9rem;margin-top:-1rem"><i class="bi bi-info-circle me-1"></i><?= e($TIPLER[$tip]['aciklama']) ?></p>

<div class="row g-4">
  <div class="col-lg-5">
    <form method="post" class="card p-4">
      <h5 class="mb-3"><?= $duzId ? 'Blok Düzenle' : 'Yeni Blok' ?></h5>
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <input type="hidden" name="id" value="<?= (int)$d['id'] ?>">
      <input type="hidden" name="tip" value="<?= e($tip) ?>">

      <ul class="nav nav-tabs lang-tabs" role="tablist">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tr" type="button">🇹🇷 TR</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#en" type="button">🇬🇧 EN</button></li>
      </ul>
      <div class="tab-content mb-3">
        <div class="tab-pane fade show active" id="tr">
          <div class="mb-2"><label class="form-label">Başlık *</label><input class="form-control" name="baslik" value="<?= e($d['baslik']) ?>" required></div>
          <div class="mb-2"><label class="form-label">Açıklama</label><textarea class="form-control" name="ozet" rows="3"><?= e($d['ozet']) ?></textarea></div>
        </div>
        <div class="tab-pane fade" id="en">
          <p class="en-hint"><i class="bi bi-info-circle me-1"></i>Boşsa Türkçesi gösterilir.</p>
          <div class="mb-2"><label class="form-label">Title</label><input class="form-control" name="baslik_en" value="<?= e($d['baslik_en']) ?>"></div>
          <div class="mb-2"><label class="form-label">Description</label><textarea class="form-control" name="ozet_en" rows="3"><?= e($d['ozet_en']) ?></textarea></div>
        </div>
      </div>

      <div class="row g-2 mb-3">
        <div class="col-<?= $TIPLER[$tip]['etiketVar'] ? '5' : '8' ?>">
          <label class="form-label">İkon</label>
          <input class="form-control" name="ikon" value="<?= e($d['ikon']) ?>" placeholder="bi-globe2">
        </div>
        <?php if ($TIPLER[$tip]['etiketVar']): ?>
          <div class="col-3"><label class="form-label">Adım No</label><input class="form-control" name="etiket" value="<?= e($d['etiket']) ?>" placeholder="01"></div>
        <?php endif; ?>
        <div class="col-2"><label class="form-label">Sıra</label><input type="number" class="form-control" name="sira" value="<?= (int)$d['sira'] ?>"></div>
        <div class="col-2 d-flex align-items-end"><div class="form-check"><input type="checkbox" class="form-check-input" id="durum" name="durum" <?= $d['durum']?'checked':'' ?>><label class="form-check-label" for="durum">Aktif</label></div></div>
      </div>
      <div class="form-text mb-3">İkon adları: <a href="https://icons.getbootstrap.com/" target="_blank" rel="noopener">icons.getbootstrap.com</a></div>

      <button class="btn btn-primary"><i class="bi bi-save"></i> Kaydet</button>
      <?php if ($duzId): ?><a href="?tip=<?= e($tip) ?>" class="btn btn-link">İptal</a><?php endif; ?>
    </form>
  </div>

  <div class="col-lg-7">
    <div class="card">
      <div class="card-header"><?= e($TIPLER[$tip]['ad']) ?> (<?= count($liste) ?>)</div>
      <?php if (!$liste): ?>
        <div class="p-4 text-center text-muted">Bu bölümde henüz kayıt yok. Kayıt yoksa sitedeki ilgili bölüm otomatik gizlenir.</div>
      <?php else: ?>
      <ul class="list-group list-group-flush">
        <?php foreach ($liste as $b): ?>
        <li class="list-group-item d-flex align-items-center gap-3">
          <?php if ($b['etiket']): ?>
            <span style="font-weight:800;color:var(--primary);min-width:2rem"><?= e($b['etiket']) ?></span>
          <?php else: ?>
            <i class="bi <?= e($b['ikon']) ?>" style="font-size:1.35rem;color:var(--primary);min-width:2rem"></i>
          <?php endif; ?>
          <div class="flex-grow-1">
            <strong><?= e($b['baslik']) ?></strong>
            <?php if (!$b['durum']): ?><span class="badge bg-secondary ms-2">Pasif</span><?php endif; ?>
            <div class="text-muted" style="font-size:.85rem"><?= e(mb_strimwidth($b['ozet'], 0, 90, '…')) ?></div>
          </div>
          <a href="?id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
          <a href="?tip=<?= e($tip) ?>&sil=<?= $b['id'] ?>&t=<?= csrf_token() ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Silinsin mi?')"><i class="bi bi-trash"></i></a>
        </li>
        <?php endforeach; ?>
      </ul>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/inc/footer.php'; ?>
