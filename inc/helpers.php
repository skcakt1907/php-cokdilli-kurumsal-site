<?php
require_once __DIR__ . '/db.php';

// XSS koruma
function e($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

// Site ayarlarını tek seferde belleğe al
function ayarlar() {
    static $cache = null;
    if ($cache === null) {
        global $db;
        $cache = [];
        foreach ($db->query("SELECT anahtar, deger FROM ayarlar") as $r) {
            $cache[$r['anahtar']] = $r['deger'];
        }
    }
    return $cache;
}
function ayar($k, $varsayilan = '') {
    $a = ayarlar();
    return $a[$k] ?? $varsayilan;
}

// Liste çekme yardımcısı
function getList($tablo, $where = '1', $order = 'sira ASC, id DESC', $limit = null) {
    global $db;
    $sql = "SELECT * FROM `$tablo` WHERE $where ORDER BY $order";
    if ($limit) $sql .= " LIMIT " . (int)$limit;
    return $db->query($sql)->fetchAll();
}

// CSRF token
function csrf_token() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_check() {
    if (!hash_equals($_SESSION['csrf'] ?? '_', $_POST['csrf'] ?? '')) {
        http_response_code(403);
        die('Geçersiz form gönderimi (CSRF).');
    }
}
// Bağlantı ile yapılan (GET) silme işlemleri için token doğrulaması
function csrf_check_get() {
    if (!hash_equals($_SESSION['csrf'] ?? '_', $_GET['t'] ?? '')) {
        http_response_code(403);
        die('Geçersiz istek (CSRF).');
    }
}

// Tarih formatla
function trTarih($d) {
    if (!$d) return '';
    $aylar = ['','Ocak','Şubat','Mart','Nisan','Mayıs','Haziran','Temmuz','Ağustos','Eylül','Ekim','Kasım','Aralık'];
    $t = strtotime($d);
    return date('d', $t) . ' ' . $aylar[(int)date('m', $t)] . ' ' . date('Y', $t);
}

/**
 * DB'de tutulan görsel yolunu tarayıcıya uygun adrese çevirir.
 * Tam adresler (http/https, data:) olduğu gibi bırakılır; proje içi
 * göreli yollar (img/partners/x.jpg) SITE_URL ile öne eklenir.
 */
function gorselUrl(?string $yol): string {
    $yol = trim((string)$yol);
    if ($yol === '') return '';
    if (preg_match('#^(https?:)?//#i', $yol) || str_starts_with($yol, 'data:')) return $yol;
    return SITE_URL . '/' . ltrim($yol, '/');
}

/**
 * Site logosu — panelden yüklendiyse onu, yoksa varsayılan dosyayı döndürür.
 * Panel: Ayarlar > Logo
 */
function logoUrl(): string {
    $yol = trim(ayar('logo'));
    return gorselUrl($yol !== '' ? $yol : 'img/logo-fgg-256.png');
}

/**
 * Etiketli liste ayarlarını ayrıştırır (telefonlar, departman e-postaları).
 * Her satır:  Etiket|değer        -> ['etiket'=>…, 'deger'=>…, 'bayrak'=>'']
 *             Etiket|değer|wa     -> bayrak 'wa'  (WhatsApp bağlantısı üretilir)
 * Boş satırlar ve değeri olmayan satırlar atlanır.
 */
function etiketliListe(?string $metin): array {
    $cikti = [];
    foreach (preg_split('/\R/', (string)$metin) as $satir) {
        $satir = trim($satir);
        if ($satir === '') continue;
        $parca = array_map('trim', explode('|', $satir));
        $deger = $parca[1] ?? '';
        if ($deger === '') continue;
        $cikti[] = [
            'etiket' => $parca[0],
            'deger'  => $deger,
            'bayrak' => strtolower($parca[2] ?? ''),
        ];
    }
    return $cikti;
}

/** Telefon numarasından tel: / wa.me bağlantısı için sadece rakamları alır. */
function telRakam(string $no): string {
    return preg_replace('/\D/', '', $no);
}

/**
 * Uzun metin alanlarını güvenli HTML'e çevirir.
 * Boş satır  -> yeni paragraf
 * "- " satırı -> madde listesi
 * Girdi her zaman kaçışlanır; metin içinde HTML çalışmaz.
 */
function zenginMetin($metin) {
    $metin = trim((string)$metin);
    if ($metin === '') return '';

    $cikti    = '';
    $paragraf = [];
    $madde    = [];

    $paragrafKapat = function () use (&$cikti, &$paragraf) {
        if (!$paragraf) return;
        $cikti .= '<p>' . nl2br(e(implode("\n", $paragraf))) . '</p>';
        $paragraf = [];
    };
    $maddeKapat = function () use (&$cikti, &$madde) {
        if (!$madde) return;
        $cikti .= '<ul class="metin-liste">';
        foreach ($madde as $m) $cikti .= '<li>' . e($m) . '</li>';
        $cikti .= '</ul>';
        $madde = [];
    };

    foreach (preg_split('/\R/', $metin) as $satir) {
        $satir = rtrim($satir);
        if (preg_match('/^\s*##\s+(.*)$/u', $satir, $m)) {
            $maddeKapat();
            $paragrafKapat();
            $cikti .= '<h4 class="metin-baslik">' . e(trim($m[1])) . '</h4>';
        } elseif (preg_match('/^\s*[-*•]\s+(.*)$/u', $satir, $m)) {
            $paragrafKapat();
            $madde[] = trim($m[1]);
        } elseif (trim($satir) === '') {
            $maddeKapat();
            $paragrafKapat();
        } else {
            $maddeKapat();
            $paragraf[] = $satir;
        }
    }
    $maddeKapat();
    $paragrafKapat();

    return $cikti;
}

// Tarih formatla (aktif dile göre)
function tarihYaz($d) {
    if (!$d) return '';
    if (function_exists('isEn') && isEn()) return date('d M Y', strtotime($d));
    return trTarih($d);
}

// Slug üret
function slugify($s) {
    $tr = ['ç','Ç','ğ','Ğ','ı','İ','ö','Ö','ş','Ş','ü','Ü'];
    $en = ['c','c','g','g','i','i','o','o','s','s','u','u'];
    $s = str_replace($tr, $en, $s);
    $s = strtolower(trim($s));
    $s = preg_replace('/[^a-z0-9]+/', '-', $s);
    return trim($s, '-');
}

// Çift dil altyapısı (t(), tf(), ta(), lang())
require_once __DIR__ . '/lang.php';

// Görünmez imza (imzaKodla / imzaCoz / imzaMetni)
require_once __DIR__ . '/imza.php';
