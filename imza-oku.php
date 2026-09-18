<?php
/**
 * İMZA OKUYUCU — bir adresteki veya dosyadaki görünmez filigranı çözer.
 *
 * Kullanım (komut satırı):
 *   php imza-oku.php https://sub.ornek-holding.com
 *   php imza-oku.php C:/yol/index.html
 *
 * Bu dosyayı SUNUCUYA YÜKLEME — yalnızca kendi makinende kullan.
 */
require __DIR__ . '/inc/imza.php';

$hedef = $argv[1] ?? null;
if (!$hedef) {
    fwrite(STDERR, "Kullanim: php imza-oku.php <adres veya dosya>\n");
    exit(1);
}

$icerik = @file_get_contents($hedef);
if ($icerik === false) {
    fwrite(STDERR, "Okunamadi: $hedef\n");
    exit(1);
}

$cozulen = imzaCoz($icerik);

echo "Hedef  : $hedef\n";
echo "Boyut  : " . number_format(strlen($icerik)) . " bayt\n";
echo str_repeat('-', 52) . "\n";

if ($cozulen === '') {
    echo "SONUC  : imza BULUNAMADI\n";
    echo "\nOlasi sebepler: kod yeniden bicimlendirilmis (minify/prettier),\n";
    echo "imza kaldirilmis, ya da bu sayfa bizim uretimimiz degil.\n";
    exit(2);
}

echo "SONUC  : *** IMZA BULUNDU ***\n\n";
echo "  $cozulen\n\n";
$adet = preg_match_all('/' . IMZA_SINIR . '/u', $icerik);
echo "Belge icinde " . $adet . " sinir isareti var.\n";
