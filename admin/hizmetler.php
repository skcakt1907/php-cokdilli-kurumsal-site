<?php
$adminTitle='Faaliyet Alanları';
require_once __DIR__ . '/inc/crud.php';
require_once __DIR__ . '/inc/header.php';
$liste = $db->query("SELECT * FROM hizmetler ORDER BY sira ASC, id DESC")->fetchAll();
$flash = adminFlashCek();
?>
<?php if($flash): ?><div class="alert alert-<?= $flash['tip']==='success'?'success':'danger' ?>"><?= e($flash['msg']) ?></div><?php endif; ?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    Faaliyet Alanları (<?= count($liste) ?>)
    <a href="hizmet-form.php" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg"></i> Yeni Faaliyet Alanı</a>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead><tr><th width="60">İkon</th><th>Başlık</th><th>Özet</th><th width="80">Sıra</th><th width="90">Durum</th><th width="140"></th></tr></thead>
      <tbody>
      <?php foreach($liste as $h): ?>
        <tr>
          <td><i class="bi <?= e($h['ikon']) ?>" style="font-size:1.5rem;color:#b48a3f"></i></td>
          <td><strong><?= e($h['baslik']) ?></strong><br><small class="text-muted"><?= e($h['slug']) ?></small></td>
          <td><small><?= e(mb_substr($h['ozet'],0,90)) ?>...</small></td>
          <td><?= (int)$h['sira'] ?></td>
          <td><?= $h['durum']?'<span class="badge bg-success">Aktif</span>':'<span class="badge bg-secondary">Pasif</span>' ?></td>
          <td>
            <a href="hizmet-form.php?id=<?= $h['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
            <a href="hizmet-sil.php?id=<?= $h['id'] ?>&t=<?= csrf_token() ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Silinsin mi?')"><i class="bi bi-trash"></i></a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__ . '/inc/footer.php'; ?>
