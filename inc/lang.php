<?php
// ============================================
// FGG Holding — Çift dil altyapısı (TR / EN)
//
// Kullanım:
//   lang()            -> 'tr' | 'en'
//   t('anahtar')      -> statik arayüz metni (menü, buton, başlık)
//   tf($satir,'ozet') -> DB satırından dile göre alan (EN boşsa TR'ye düşer)
//   ta('misyon')      -> ayarlar tablosundan dile göre değer
//   lang_url('en')    -> mevcut sayfanın diğer dildeki adresi
// ============================================
require_once __DIR__ . '/config.php';

/** Aktif dili belirler: ?lang= > çerez > varsayılan */
function lang(): string
{
    static $lang = null;
    if ($lang !== null) return $lang;

    $gecerli = array_keys(LANGS);

    if (isset($_GET['lang']) && in_array($_GET['lang'], $gecerli, true)) {
        $lang = $_GET['lang'];
        setcookie('site_lang', $lang, [
            'expires'  => time() + 60 * 60 * 24 * 365,
            'path'     => '/',
            'httponly' => false,
            'samesite' => 'Lax',
        ]);
        return $lang;
    }

    if (isset($_COOKIE['site_lang']) && in_array($_COOKIE['site_lang'], $gecerli, true)) {
        return $lang = $_COOKIE['site_lang'];
    }

    return $lang = DEFAULT_LANG;
}

/** Aktif dil İngilizce mi? */
function isEn(): bool { return lang() === 'en'; }

/**
 * DB satırından aktif dile uygun alanı döndürür.
 * EN seçiliyken `alan_en` boşsa Türkçesine düşer (fallback).
 */
function tf(?array $satir, string $alan): string
{
    if (!$satir) return '';
    if (isEn()) {
        $en = trim((string)($satir[$alan . '_en'] ?? ''));
        if ($en !== '') return $en;
    }
    return (string)($satir[$alan] ?? '');
}

/** Ayarlar tablosundan aktif dile uygun değeri döndürür (EN boşsa TR). */
function ta(string $anahtar, string $varsayilan = ''): string
{
    if (isEn()) {
        $en = trim(ayar($anahtar . '_en'));
        if ($en !== '') return $en;
    }
    return ayar($anahtar, $varsayilan);
}

/** Mevcut sayfanın verilen dildeki adresi */
function lang_url(string $hedefDil): string
{
    $q = $_GET;
    $q['lang'] = $hedefDil;
    $yol = strtok($_SERVER['REQUEST_URI'], '?');
    return $yol . '?' . http_build_query($q);
}

/** Statik arayüz metni */
function t(string $anahtar): string
{
    static $sozluk = null;
    if ($sozluk === null) $sozluk = sozluk();
    $satir = $sozluk[$anahtar] ?? null;
    if ($satir === null) return $anahtar;      // eksik anahtar görünür kalsın
    return $satir[lang()] ?? $satir['tr'];
}

/** Arayüz sözlüğü — yeni metin eklerken buraya bir satır ekle */
function sozluk(): array
{
    return [
        // --- Menü ---
        'nav_anasayfa'      => ['tr' => 'Anasayfa',                   'en' => 'Home'],
        'nav_kurumsal'      => ['tr' => 'Kurumsal',                   'en' => 'About Us'],
        'nav_faaliyet'      => ['tr' => 'Faaliyet Alanları',          'en' => 'Business Sectors'],
        'nav_ortaklar'      => ['tr' => 'Şirketler ve İş Ortakları',  'en' => 'Companies & Partners'],
        'nav_haberler'      => ['tr' => 'Haberler',                   'en' => 'News'],
        'nav_iletisim'      => ['tr' => 'İletişim',                   'en' => 'Contact'],
        'tum_sektorler'     => ['tr' => 'Tüm faaliyet alanları',      'en' => 'All business sectors'],

        // --- Genel ---
        'devamini_oku'      => ['tr' => 'Devamını Oku',       'en' => 'Read More'],
        'incele'            => ['tr' => 'İncele',             'en' => 'Explore'],
        'daha'              => ['tr' => 'alan daha',          'en' => 'more'],
        'grup_mail_etiket'  => ['tr' => 'Bu alan için iletişim', 'en' => 'Contact for this sector'],
        'detay'             => ['tr' => 'Detay',              'en' => 'Details'],
        'tumu'              => ['tr' => 'Tümünü Gör',         'en' => 'View All'],
        'geri'              => ['tr' => 'Geri Dön',           'en' => 'Go Back'],
        'web_sitesi'        => ['tr' => 'Web Sitesi',         'en' => 'Website'],
        'kurulus_yili'      => ['tr' => 'Kuruluş Yılı',       'en' => 'Founded'],
        'sektor'            => ['tr' => 'Sektör',             'en' => 'Sector'],
        'ulke'              => ['tr' => 'Ülke',               'en' => 'Country'],
        'bulunamadi'        => ['tr' => 'Kayıt bulunamadı.',  'en' => 'No records found.'],

        // --- Anasayfa / kurumsal ---
        'biz_kimiz'         => ['tr' => 'Biz Kimiz?',         'en' => 'Who We Are'],
        'ne_yapariz'        => ['tr' => 'Neler Yaparız?',     'en' => 'What We Do'],
        'faaliyet_alt'      => ['tr' => 'Değer ürettiğimiz sektörler',  'en' => 'The sectors where we create value'],
        'ortaklar_alt'      => ['tr' => 'Uluslararası iş ağımız',       'en' => 'Our international network'],
        'yaklasim_baslik'   => ['tr' => 'Temel Yaklaşımımız',           'en' => 'Our Core Approach'],
        'neden_baslik'      => ['tr' => 'Neden FGG Holding?',           'en' => 'Why FGG Holding?'],
        'neden_alt'         => ['tr' => 'Stratejik ortaklık · Güçlü ağ · Uluslararası vizyon', 'en' => 'Strategic partnership · Strong network · International vision'],
        'surec_baslik'      => ['tr' => 'Proje Geliştirme Modelimiz',   'en' => 'Our Project Development Model'],
        'surec_alt'         => ['tr' => 'Fikirden gerçek projeye',      'en' => 'From concept to real project'],
        'bolge_baslik'      => ['tr' => 'Uluslararası İş Ağımız',       'en' => 'Our International Network'],
        'bolge_alt'         => ['tr' => 'Odaklandığımız bölgeler',      'en' => 'Our focus regions'],
        'isbirligi_baslik'  => ['tr' => 'Stratejik İş Birlikleri',      'en' => 'Strategic Collaborations'],
        'isbirligi_alt'     => ['tr' => 'Birlikte daha büyük projeler', 'en' => 'Bigger projects, together'],
        'rakamlarla'        => ['tr' => 'Rakamlarla FGG Holding',       'en' => 'FGG Holding in Numbers'],

        // --- Sayaçlar ---
        'sayac_yil'         => ['tr' => 'Yıllık Tecrübe',     'en' => 'Years of Experience'],
        'sayac_sirket'      => ['tr' => 'İş Ortağı',          'en' => 'Business Partners'],
        'sayac_personel'    => ['tr' => 'Çalışan',            'en' => 'Employees'],
        'sayac_sektor'      => ['tr' => 'Faaliyet Alanı',     'en' => 'Business Sectors'],
        'sayac_aile'        => ['tr' => 'Sektör Ailesi',      'en' => 'Sector Families'],
        'sayac_bolge'       => ['tr' => 'Odak Bölge',         'en' => 'Focus Regions'],

        // --- Kurumsal ---
        'hikayemiz'         => ['tr' => 'Hikâyemiz',          'en' => 'Our Story'],
        'misyon'            => ['tr' => 'Misyonumuz',         'en' => 'Our Mission'],
        'vizyon'            => ['tr' => 'Vizyonumuz',         'en' => 'Our Vision'],
        'yonetim'           => ['tr' => 'Yönetim Kadromuz',   'en' => 'Our Management'],

        // --- İş ortakları ---
        'grup_sirketleri'    => ['tr' => 'Grup Şirketlerimiz',   'en' => 'Our Group Companies'],
        'grup_sirketleri_alt'=> ['tr' => 'Çatımız altındaki markalar', 'en' => 'Brands under our roof'],
        'is_ortaklari'       => ['tr' => 'İş Ortaklarımız',     'en' => 'Our Business Partners'],
        'is_ortaklari_alt'   => ['tr' => 'Birlikte çalıştığımız şirketler', 'en' => 'Companies we work with'],
        'ortak_yok'         => ['tr' => 'İş ortağı bilgileri, ilgili ticari iş birliğinin niteliğine göre yakında bu sayfada yayınlanacaktır.', 'en' => 'Partner details will be published on this page in line with the nature of each commercial collaboration.'],

        // --- İletişim ---
        'bize_ulasin'       => ['tr' => 'Bizimle İletişime Geçin',        'en' => 'Get in Touch'],
        'iletisim_alt'      => ['tr' => 'Projenizi birlikte geliştirelim', 'en' => 'Let us develop your project together'],
        'iletisim_giris'    => ['tr' => 'Yeni bir proje geliştiriyor, yatırım arıyor, uluslararası pazarlara açılmak istiyor veya güvenilir bir ticari ortak arıyorsanız FGG Holding ile iletişime geçebilirsiniz.', 'en' => 'If you are developing a new project, seeking investment, planning to enter international markets or looking for a reliable commercial partner, please get in touch with FGG Holding.'],
        'departman_baslik'  => ['tr' => 'Departman E-postaları', 'en' => 'Department E-mails'],
        'departman_alt'     => ['tr' => 'Doğrudan ilgili birime yazın', 'en' => 'Write directly to the relevant unit'],
        'adres'             => ['tr' => 'Adres',              'en' => 'Address'],
        'telefon'           => ['tr' => 'Telefon',            'en' => 'Phone'],
        'eposta'            => ['tr' => 'E-posta',            'en' => 'E-mail'],
        'calisma_saati'     => ['tr' => 'Çalışma Saatleri',   'en' => 'Working Hours'],
        'form_ad'           => ['tr' => 'Adınız Soyadınız',   'en' => 'Full Name'],
        'form_mail'         => ['tr' => 'E-posta Adresiniz',  'en' => 'Your E-mail'],
        'form_tel'          => ['tr' => 'Telefon',            'en' => 'Phone'],
        'form_konu'         => ['tr' => 'Konu',               'en' => 'Subject'],
        'form_mesaj'        => ['tr' => 'Mesajınız',          'en' => 'Your Message'],
        'form_gonder'       => ['tr' => 'Mesajı Gönder',      'en' => 'Send Message'],
        'form_baslik'       => ['tr' => 'Bize Mesaj Gönderin', 'en' => 'Send Us a Message'],
        'form_alt'          => ['tr' => 'Formu doldurun, en kısa sürede dönelim', 'en' => 'Fill in the form and we will get back to you'],
        'form_basarili'     => ['tr' => 'Mesajınız iletildi. En kısa sürede dönüş yapacağız.', 'en' => 'Your message has been sent. We will get back to you shortly.'],
        'form_hata'         => ['tr' => 'Lütfen zorunlu alanları doldurun.', 'en' => 'Please fill in the required fields.'],

        // --- Footer ---
        'footer_kurumsal'   => ['tr' => 'Kurumsal',           'en' => 'Corporate'],
        'footer_haklar'     => ['tr' => 'Tüm hakları saklıdır.', 'en' => 'All rights reserved.'],
    ];
}
