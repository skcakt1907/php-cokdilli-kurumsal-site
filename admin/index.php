<?php
require_once __DIR__ . '/../inc/helpers.php';
if(!empty($_SESSION['admin_id'])){ header('Location: ' . SITE_URL . '/admin/dashboard.php'); exit; }

// Brute-force koruma: 5 başarısız denemeden sonra 15 dakika kilit
$_SESSION['login_attempts']  = $_SESSION['login_attempts']  ?? 0;
$_SESSION['login_locked_at'] = $_SESSION['login_locked_at'] ?? 0;
$kilitliKalan = 0;
if($_SESSION['login_locked_at'] > 0){
    $kilitliKalan = 900 - (time() - $_SESSION['login_locked_at']);
    if($kilitliKalan <= 0){
        $_SESSION['login_attempts']  = 0;
        $_SESSION['login_locked_at'] = 0;
        $kilitliKalan = 0;
    }
}

$hata = '';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    csrf_check();
    if($kilitliKalan > 0){
        $hata = 'Çok fazla başarısız deneme. ' . ceil($kilitliKalan/60) . ' dakika sonra tekrar deneyin.';
    } else {
        $k = trim($_POST['kullanici'] ?? '');
        $s = $_POST['sifre'] ?? '';
        $stmt = $db->prepare("SELECT * FROM admin WHERE kullanici=? LIMIT 1");
        $stmt->execute([$k]);
        $a = $stmt->fetch();
        if($a && password_verify($s, $a['sifre_hash'])){
            // Session fixation koruması — yeni session ID
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $a['id'];
            $_SESSION['admin_ad'] = $a['ad_soyad'];
            $_SESSION['login_attempts']  = 0;
            $_SESSION['login_locked_at'] = 0;
            header('Location: ' . SITE_URL . '/admin/dashboard.php'); exit;
        }
        $_SESSION['login_attempts']++;
        if($_SESSION['login_attempts'] >= 5){
            $_SESSION['login_locked_at'] = time();
            $hata = 'Çok fazla başarısız deneme. 15 dakika kilitlendi.';
        } else {
            $kalan = 5 - $_SESSION['login_attempts'];
            $hata = "Kullanıcı adı veya şifre hatalı. ({$kalan} hak kaldı)";
        }
        sleep(1);
    }
}
?>
<!doctype html>
<html lang="tr"><head>
<meta charset="utf-8"><title>Admin Giriş — <?= e(ayar('site_adi')) ?></title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="icon" href="<?= SITE_URL ?>/img/logo-fgg-128.png" type="image/png">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
<style>
/* Giriş ekranı — panelle aynı siyah/altın kimlik.
   Bu sayfa oturum açılmadan önce geldiği için stilleri kasten gömülü:
   harici bir dosya yüklenemese bile giriş ekranı bozulmasın. */
:root{color-scheme:dark}
body{
  font-family:'Poppins',system-ui,-apple-system,'Segoe UI',sans-serif;
  background:#0b0b0b;color:#ddd7cc;
  min-height:100vh;display:flex;align-items:center;justify-content:center;
  margin:0;padding:1.25rem;position:relative;overflow:hidden;
}
/* Zeminde hafif altın ışıma */
body::before{
  content:"";position:absolute;inset:0;pointer-events:none;
  background:
    radial-gradient(60% 55% at 50% 0%,rgba(212,175,55,.10),transparent 70%),
    radial-gradient(45% 45% at 50% 100%,rgba(212,175,55,.05),transparent 70%);
}
.login-card{
  position:relative;
  background:#131313;
  border:1px solid rgba(212,175,55,.16);
  border-radius:16px;padding:2.5rem;
  width:100%;max-width:420px;
  box-shadow:0 24px 70px rgba(0,0,0,.75);
}
.login-card .logo{text-align:center;margin-bottom:1.6rem}
.login-card p{text-align:center;color:#948d81;margin-bottom:1.8rem;font-size:.9rem}
.form-label{font-weight:600;color:#ddd7cc;font-size:.88rem;margin-bottom:.4rem}
.form-control{
  background:#1a1a1a;border:1px solid rgba(255,255,255,.09);color:#ddd7cc;
  padding:.85rem 1rem;border-radius:8px;transition:.2s;
}
.form-control::placeholder{color:#6d675e}
.form-control:focus{
  background:#212121;border-color:#d4af37;color:#fff;
  box-shadow:0 0 0 .2rem rgba(212,175,55,.16);
}
.btn-primary{
  background:#d4af37;border:none;color:#0b0b0b;
  padding:.85rem;border-radius:8px;font-weight:700;width:100%;transition:.22s;
}
.btn-primary:hover,.btn-primary:focus{background:#e8c65a;color:#0b0b0b}
.btn-primary:active{background:#b48a3f!important;color:#0b0b0b!important}
.alert-danger{
  background:rgba(224,82,96,.1);border:1px solid rgba(224,82,96,.35);
  color:#f0a6ad;border-radius:10px;font-size:.9rem;
}
:focus-visible{outline:2px solid #d4af37;outline-offset:2px}
</style></head><body>
<div class="login-card">
  <div class="logo"><img src="<?= SITE_URL ?>/img/logo-fgg.png" alt="<?= e(ayar('site_adi')) ?>" style="max-height:96px"></div>
  <p style="margin-top:1rem">Yönetim Paneli Girişi</p>
  <?php if($hata): ?><div class="alert alert-danger"><?= e($hata) ?></div><?php endif; ?>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <div class="mb-3"><label class="form-label">Kullanıcı Adı</label><input class="form-control" name="kullanici" required autofocus></div>
    <div class="mb-3"><label class="form-label">Şifre</label><input class="form-control" type="password" name="sifre" required></div>
    <button class="btn btn-primary"><i class="bi bi-box-arrow-in-right"></i> Giriş Yap</button>
  </form>
</div>
</body></html>
