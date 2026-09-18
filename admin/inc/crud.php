<?php
// Basit CRUD yardımcıları (admin için)
require_once __DIR__ . '/../../inc/helpers.php';

function adminGorselYukle($field, $eski=null){
    if(empty($_FILES[$field]['name'])) return $eski;
    $f = $_FILES[$field];
    if($f['error'] !== UPLOAD_ERR_OK) return $eski;
    if($f['size'] > 5*1024*1024) return $eski; // 5MB

    $izinExt = ['jpg','jpeg','png','webp','gif'];
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    if(!in_array($ext, $izinExt, true)) return $eski;

    // Gerçek MIME doğrulama (uzantı sahteliğini engeller)
    $izinMime = ['image/jpeg','image/png','image/webp','image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $f['tmp_name']);
    finfo_close($finfo);
    if(!in_array($mime, $izinMime, true)) return $eski;

    // Görsel içeriği gerçekten geçerli mi (gizli PHP/script koruması)
    if(@getimagesize($f['tmp_name']) === false) return $eski;

    // Tahmin edilemeyen rastgele dosya adı
    $ad = bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;
    $hedef = UPLOADS . '/' . $ad;

    if(move_uploaded_file($f['tmp_name'], $hedef)){
        @chmod($hedef, 0644);
        if($eski && str_starts_with($eski, UPLOADS_URL . '/')){
            $eskiAd = basename($eski);
            if(preg_match('/^[a-zA-Z0-9_.-]+$/', $eskiAd)){
                $yol = UPLOADS . '/' . $eskiAd;
                if(is_file($yol)) @unlink($yol);
            }
        }
        return UPLOADS_URL . '/' . $ad;
    }
    return $eski;
}

function adminFlash($msg, $tip='success'){
    $_SESSION['flash'] = ['msg'=>$msg,'tip'=>$tip];
}
function adminFlashCek(){
    if(empty($_SESSION['flash'])) return null;
    $f = $_SESSION['flash']; unset($_SESSION['flash']); return $f;
}
