<?php
$adminTitle = 'Dashboard';
require_once __DIR__ . '/inc/header.php';

$sayilar = [
    'faaliyet'  => (int)$db->query("SELECT COUNT(*) FROM hizmetler")->fetchColumn(),
    'ortak'     => (int)$db->query("SELECT COUNT(*) FROM projeler")->fetchColumn(),
    'yonetici'  => (int)$db->query("SELECT COUNT(*) FROM yoneticiler")->fetchColumn(),
    'haber'     => (int)$db->query("SELECT COUNT(*) FROM blog")->fetchColumn(),
    'mesaj'     => (int)$db->query("SELECT COUNT(*) FROM mesajlar")->fetchColumn(),
    'yeniMesaj' => (int)$db->query("SELECT COUNT(*) FROM mesajlar WHERE okundu=0")->fetchColumn(),
];
$sonMesaj = $db->query("SELECT * FROM mesajlar ORDER BY tarih DESC LIMIT 5")->fetchAll();

// İçerik eksikleri — müşteri içeriği geldikçe bu uyarılar kaybolur
$eksikler = [];
if (!trim(ayar('adres')) || str_contains(ayar('adres'), 'müşteriden')) $eksikler[] = ['Adres bilgisi girilmemiş', 'ayarlar.php'];
if (preg_match('/0000/', ayar('telefon')))                       $eksikler[] = ['Telefon numarası girilmemiş', 'ayarlar.php'];
if ((int)ayar('yil') === 0 && (int)ayar('personel_sayi') === 0)        $eksikler[] = ['Rakamlarla FGG bölümü boş (anasayfada gizli)', 'ayarlar.php'];
if ($sayilar['ortak'] === 0)                                          $eksikler[] = ['İş ortağı eklenmemiş', 'projeler.php'];
if ($sayilar['yonetici'] === 0)                                        $eksikler[] = ['Yönetim kadrosu eklenmemiş (Kurumsal sayfasında gizli)', 'yoneticiler.php'];
if ($sayilar['haber'] === 0)                                           $eksikler[] = ['Henüz haber yok (menüde gizli)', 'blog.php'];
?>
<div class="row g-3 mb-4">
  <div class="col-md-4 col-lg-2"><div class="stat-card"><div class="icon"><i class="bi bi-grid-1x2"></i></div><div><h3><?= $sayilar['faaliyet'] ?></h3><p>Faaliyet Alanı</p></div></div></div>
  <div class="col-md-4 col-lg-2"><div class="stat-card"><div class="icon"><i class="bi bi-diagram-3"></i></div><div><h3><?= $sayilar['ortak'] ?></h3><p>İş Ortağı</p></div></div></div>
  <div class="col-md-4 col-lg-2"><div class="stat-card"><div class="icon"><i class="bi bi-people"></i></div><div><h3><?= $sayilar['yonetici'] ?></h3><p>Yönetici</p></div></div></div>
  <div class="col-md-4 col-lg-2"><div class="stat-card"><div class="icon"><i class="bi bi-newspaper"></i></div><div><h3><?= $sayilar['haber'] ?></h3><p>Haber</p></div></div></div>
  <div class="col-md-4 col-lg-2"><div class="stat-card"><div class="icon"><i class="bi bi-envelope"></i></div><div><h3><?= $sayilar['mesaj'] ?></h3><p>Toplam Mesaj</p></div></div></div>
  <div class="col-md-4 col-lg-2"><div class="stat-card"><div class="icon" style="background:rgba(230,57,70,.12);color:#e63946"><i class="bi bi-envelope-exclamation"></i></div><div><h3><?= $sayilar['yeniMesaj'] ?></h3><p>Okunmamış</p></div></div></div>
</div>

<?php if ($eksikler): ?>
<div class="card mb-4">
  <div class="card-header"><i class="bi bi-clipboard-check me-2"></i>Tamamlanmayı Bekleyen İçerikler</div>
  <ul class="list-group list-group-flush">
    <?php foreach ($eksikler as [$metin, $link]): ?>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <span><i class="bi bi-dash-circle me-2 text-muted"></i><?= e($metin) ?></span>
        <a href="<?= e($link) ?>" class="btn btn-sm btn-outline-primary">Düzenle</a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    Son Mesajlar
    <a href="mesajlar.php" class="btn btn-sm btn-primary">Tümü</a>
  </div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead><tr><th>Ad</th><th>E-posta</th><th>Konu</th><th>Tarih</th><th>Durum</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($sonMesaj as $m): ?>
        <tr>
          <td><?= e($m['ad']) ?></td>
          <td><?= e($m['mail']) ?></td>
          <td><?= e($m['konu']) ?></td>
          <td><?= date('d.m.Y H:i', strtotime($m['tarih'])) ?></td>
          <td><?php if ($m['okundu']): ?><span class="badge bg-success">Okundu</span><?php else: ?><span class="badge bg-warning text-dark">Yeni</span><?php endif; ?></td>
          <td><a href="mesajlar.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a></td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$sonMesaj): ?><tr><td colspan="6" class="text-center text-muted py-4">Henüz mesaj yok.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__ . '/inc/footer.php'; ?>
