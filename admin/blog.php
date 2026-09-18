<?php
$adminTitle='Haberler';
require_once __DIR__ . '/inc/crud.php';
require_once __DIR__ . '/inc/header.php';
$liste = $db->query("SELECT * FROM blog ORDER BY tarih DESC")->fetchAll();
$flash = adminFlashCek();
?>
<?php if($flash): ?><div class="alert alert-<?= $flash['tip']==='success'?'success':'danger' ?>"><?= e($flash['msg']) ?></div><?php endif; ?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    Haberler (<?= count($liste) ?>)
    <a href="blog-form.php" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg"></i> Yeni Haber</a>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead><tr><th width="80">Görsel</th><th>Başlık</th><th>Kategori</th><th>Tarih</th><th width="90">Durum</th><th width="140"></th></tr></thead>
      <tbody>
      <?php foreach($liste as $b): ?>
        <tr>
          <td><img src="<?= e(gorselUrl($b['gorsel'])) ?>" style="width:60px;height:45px;object-fit:cover;border-radius:6px"></td>
          <td><strong><?= e($b['baslik']) ?></strong><br><small class="text-muted"><?= e($b['slug']) ?></small></td>
          <td><span class="badge bg-light text-dark"><?= e($b['kategori']) ?></span></td>
          <td><?= trTarih($b['tarih']) ?></td>
          <td><?= $b['durum']?'<span class="badge bg-success">Yayında</span>':'<span class="badge bg-secondary">Taslak</span>' ?></td>
          <td>
            <a href="blog-form.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
            <a href="blog-sil.php?id=<?= $b['id'] ?>&t=<?= csrf_token() ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Silinsin mi?')"><i class="bi bi-trash"></i></a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__ . '/inc/footer.php'; ?>
