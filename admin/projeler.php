<?php
$adminTitle='Şirketler ve İş Ortakları';
require_once __DIR__ . '/inc/crud.php';
require_once __DIR__ . '/inc/header.php';
$liste = $db->query("SELECT * FROM projeler ORDER BY sira ASC, id DESC")->fetchAll();
$flash = adminFlashCek();
?>
<?php if($flash): ?><div class="alert alert-<?= $flash['tip']==='success'?'success':'danger' ?>"><?= e($flash['msg']) ?></div><?php endif; ?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    Şirketler ve İş Ortakları (<?= count($liste) ?>)
    <a href="proje-form.php" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg"></i> Yeni İş Ortağı</a>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead><tr><th width="80">Görsel</th><th>Başlık</th><th>Tür</th><th>Ülke</th><th>Sektör</th><th width="80">Sıra</th><th width="90">Durum</th><th width="140"></th></tr></thead>
      <tbody>
      <?php foreach($liste as $p): ?>
        <tr>
          <td><img src="<?= e(gorselUrl($p['gorsel'])) ?>" style="width:60px;height:45px;object-fit:cover;border-radius:6px"></td>
          <td><strong><?= e($p['baslik']) ?></strong></td>
          <td><span class="badge bg-light text-dark"><?= e($p['kategori']) ?></span></td>
          <td><?= $p['tur']==='grup'
                ? '<span class="badge" style="background:#b48a3f">Grup Şirketi</span>'
                : '<span class="badge bg-secondary">İş Ortağı</span>' ?></td>
          <td><?= e($p['ulke']) ?></td>
          <td><?= (int)$p['sira'] ?></td>
          <td><?= $p['durum']?'<span class="badge bg-success">Aktif</span>':'<span class="badge bg-secondary">Pasif</span>' ?></td>
          <td>
            <a href="proje-form.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
            <a href="proje-sil.php?id=<?= $p['id'] ?>&t=<?= csrf_token() ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Silinsin mi?')"><i class="bi bi-trash"></i></a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__ . '/inc/footer.php'; ?>
