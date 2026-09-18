<?php
/**
 * ============================================================
 *  GÖRÜNMEZ İMZA (sıfır-genişlikli filigran)
 * ============================================================
 *  Metni sıfır-genişlikli Unicode karakterlere çevirir. Sonuç:
 *    - sayfada görünmez (karakterlerin genişliği yok)
 *    - kod editöründe boş görünür (data-* değeri boş sanılır)
 *    - kaynak kopyalanınca birlikte taşınır
 *
 *  Kodlama: her bayt 8 bit; 0 -> U+200B, 1 -> U+200C
 *           başta ve sonda U+2060 sınır işareti
 *
 *  UYARI: minify / yeniden biçimlendirme bu karakterleri siler.
 *  Delil değeri sınırlıdır; asıl dayanağın git geçmişidir.
 * ============================================================
 */

const IMZA_SIFIR  = "\u{200B}";  // zero width space        -> bit 0
const IMZA_BIR    = "\u{200C}";  // zero width non-joiner   -> bit 1
const IMZA_SINIR  = "\u{2060}";  // word joiner             -> sınır

/** Düz metni görünmez karakter dizisine çevirir. */
function imzaKodla(string $metin): string
{
    $bit = '';
    foreach (str_split($metin) as $karakter) {
        $bit .= str_pad(decbin(ord($karakter)), 8, '0', STR_PAD_LEFT);
    }
    $gorunmez = strtr($bit, ['0' => IMZA_SIFIR, '1' => IMZA_BIR]);
    return IMZA_SINIR . $gorunmez . IMZA_SINIR;
}

/** Görünmez diziyi tekrar okunur metne çevirir. */
function imzaCoz(string $icerik): string
{
    if (!preg_match('/' . IMZA_SINIR . '(.*?)' . IMZA_SINIR . '/u', $icerik, $e)) {
        return '';
    }
    $bit = strtr($e[1], [IMZA_SIFIR => '0', IMZA_BIR => '1']);
    $bit = preg_replace('/[^01]/', '', $bit);
    $metin = '';
    foreach (str_split($bit, 8) as $sekiz) {
        if (strlen($sekiz) === 8) $metin .= chr(bindec($sekiz));
    }
    return $metin;
}

/** Bu projenin imza metni — değiştirmek istersen tek yer burası. */
function imzaMetni(): string
{
    return 'DN Kreatif | ornek.com | FGG Holding | 2026';
}
