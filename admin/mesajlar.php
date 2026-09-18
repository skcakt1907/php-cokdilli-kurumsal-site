<?php
$adminTitle='Mesajlar';
require_once __DIR__ . '/inc/crud.php';

if(isset($_GET['oku'])){
    $db->prepare("UPDATE mesajlar SET okundu=1 WHERE id=?")->execute([(int)$_GET['oku']]);
    header('Location: ' . SITE_URL . '/admin/mesajlar.php'); exit;
}
if(isset($_GET['sil'])){
    csrf_check_get();
    $db->prepare("DELETE FROM mesajlar WHERE id=?")->execute([(int)$_GET['sil']]);
    adminFlash('Mesaj silindi.');
    header('Location: ' . SITE_URL . '/admin/mesajlar.php'); exit;
}
$detayId = (int)($_GET['id'] ?? 0);
$detay = null;
if($detayId){
    $stmt = $db->prepare("SELECT * FROM mesajlar WHERE id=?"); $stmt->execute([$detayId]);
    $detay = $stmt->fetch();
    if($detay && !$detay['okundu']){
        $db->prepare("UPDATE mesajlar SET okundu=1 WHERE id=?")->execute([$detayId]);
    }
}
$liste = $db->query("SELECT * FROM mesajlar ORDER BY tarih DESC")->fetchAll();
$flash = adminFlashCek();
require_once __DIR__ . '/inc/header.php';
?>
<?php if($flash): ?><div class="alert alert-success"><?= e($flash['msg']) ?></div><?php endif; ?>
<?php if($detay): ?>
<div class="card mb-4">
  <div class="card-header d-flex justify-content-between">
    <div><strong><?= e($detay['ad']) ?></strong> &lt;<?= e($detay['mail']) ?>&gt;</div>
    <div><small class="text-muted"><?= date('d.m.Y H:i', strtotime($detay['tarih'])) ?></small></div>
  </div>
  <div class="card-body">
    <p><strong>Konu:</strong> <?= e($detay['konu']) ?> <span class="ms-3"><strong>Tel:</strong> <?= e($detay['tel']) ?></span></p>
    <hr><p style="white-space:pre-wrap"><?= e($detay['mesaj']) ?></p>
    <a href="mailto:<?= e($detay['mail']) ?>" class="btn btn-primary"><i class="bi bi-reply"></i> Cevapla</a>
    <a href="?sil=<?= $detay['id'] ?>&t=<?= csrf_token() ?>" class="btn btn-outline-danger" onclick="return confirm('Silinsin mi?')"><i class="bi bi-trash"></i> Sil</a>
    <a href="mesajlar.php" class="btn btn-link">← Listeye dön</a>
  </div>
</div>
<?php endif; ?>

<div class="card">
  <div class="card-header">Tüm Mesajlar (<?= count($liste) ?>)</div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead><tr><th>Durum</th><th>Ad</th><th>E-posta</th><th>Konu</th><th>Tarih</th><th></th></tr></thead>
      <tbody>
      <?php foreach($liste as $m): ?>
        <tr style="<?= $m['okundu']?'':'font-weight:600' ?>">
          <td><?= $m['okundu']?'<i class="bi bi-envelope-open text-muted"></i>':'<i class="bi bi-envelope-fill text-warning"></i>' ?></td>
          <td><?= e($m['ad']) ?></td>
          <td><?= e($m['mail']) ?></td>
          <td><?= e($m['konu']) ?></td>
          <td><small><?= date('d.m.Y H:i', strtotime($m['tarih'])) ?></small></td>
          <td>
            <a href="?id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
            <a href="?sil=<?= $m['id'] ?>&t=<?= csrf_token() ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Silinsin mi?')"><i class="bi bi-trash"></i></a>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if(empty($liste)): ?><tr><td colspan="6" class="text-center text-muted py-4">Henüz mesaj yok.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__ . '/inc/footer.php'; ?>
