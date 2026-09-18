-- ============================================
-- FGG Holding — Kartvizit kaynaklı güncelleme (13.08.2026)
-- Kaynak: Naghi Shahabnia kurumsal kartviziti
-- ============================================
SET NAMES utf8mb4;

-- ---------- 1) Grup şirketi / iş ortağı ayrımı ----------
-- MySQL 8 "ADD COLUMN IF NOT EXISTS" desteklemez; kolon varsa hata vermesin diye
-- information_schema üzerinden koşullu çalıştırılır.
SET @v = (SELECT COUNT(*) FROM information_schema.COLUMNS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'projeler' AND COLUMN_NAME = 'tur');
SET @s = IF(@v = 0,
  "ALTER TABLE `projeler` ADD COLUMN `tur` ENUM('grup','ortak') NOT NULL DEFAULT 'ortak' AFTER `slug`",
  "SELECT 'tur kolonu zaten var'");
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- slug benzersiz olsun (ON DUPLICATE KEY çalışsın, tekrar import güvenli olsun)
SET @v = (SELECT COUNT(*) FROM information_schema.STATISTICS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'projeler'
            AND INDEX_NAME = 'uq_slug');
SET @s = IF(@v = 0,
  "ALTER TABLE `projeler` DROP INDEX `idx_slug`, ADD UNIQUE KEY `uq_slug` (`slug`)",
  "SELECT 'uq_slug zaten var'");
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- ---------- 2) İletişim bilgileri (karttan) ----------
INSERT INTO `ayarlar` (`anahtar`,`deger`) VALUES
('telefon','+971 54 580 9889'),
('telefon2','+90 500 000 00 00'),
('whatsapp','+971 54 580 9889'),
('mail','ceo@ornek-holding.com'),
('adres','Al Manara Tower, No:1312, Business Bay, Dubai / BAE'),
('adres_en','Al Manara Tower, No:1312, Business Bay, Dubai / UAE'),
('marka_slogan','VISION • INTEGRITY • GROWTH')
ON DUPLICATE KEY UPDATE `deger` = VALUES(`deger`);

-- ---------- 3) Kurucu ünvanı (kart: CEO & Chairman of the Board) ----------
UPDATE `yoneticiler`
SET `unvan`    = 'Kurucu, Yönetim Kurulu Başkanı ve Grup CEO''su',
    `unvan_en` = 'Founder, Chairman of the Board & Group CEO'
WHERE `ad` = 'Naghi Shahabnia';

-- ---------- 4) Mevcut kayıtların türü ----------
-- FİBOX brief'te "iş ortakları arasında yer alan" diye tanımlanıyor -> ortak
UPDATE `projeler` SET `tur`='ortak' WHERE `slug`='fibox-marine-shipbuilding';
-- Kartvizitteki "Group of Companies" listesindekiler -> grup
UPDATE `projeler` SET `tur`='grup', `durum`=1 WHERE `slug` IN
  ('khazar-oil-azarbaijan','ns-teknoloji','ns-trading-sweden','ns-media-soft');

-- ---------- 5) Karttan gelen yeni grup şirketleri ----------
-- Tanıtım metinleri müşteriden bekleniyor; isim/ülke/sektör girildi.
INSERT INTO `projeler`
  (`baslik`,`baslik_en`,`slug`,`tur`,`kategori`,`kategori_en`,`ulke`,`ulke_en`,`gorsel`,`aciklama`,`aciklama_en`,`website`,`sira`,`durum`)
VALUES
('NS Holding','NS Holding','ns-holding','grup','','','','','','','','',2,1),
('Crown Edge','Crown Edge','crown-edge','grup','','','','','','','','',7,1),
('YokYook','YokYook','yokyook','grup','E-Ticaret','E-Commerce','','','img/partners/yokyook.png','','','https://yokyook.com',8,1)
ON DUPLICATE KEY UPDATE
  `tur`=VALUES(`tur`), `gorsel`=VALUES(`gorsel`), `website`=VALUES(`website`), `durum`=VALUES(`durum`);

-- ---------- 6) Sıralama (kartvizitteki sırayla) ----------
UPDATE `projeler` SET `sira`=1 WHERE `slug`='ns-holding';
UPDATE `projeler` SET `sira`=2 WHERE `slug`='ns-trading-sweden';
UPDATE `projeler` SET `sira`=3 WHERE `slug`='ns-teknoloji';
UPDATE `projeler` SET `sira`=4 WHERE `slug`='ns-media-soft';
UPDATE `projeler` SET `sira`=5 WHERE `slug`='crown-edge';
UPDATE `projeler` SET `sira`=6 WHERE `slug`='khazar-oil-azarbaijan';
UPDATE `projeler` SET `sira`=7 WHERE `slug`='yokyook';
UPDATE `projeler` SET `sira`=1 WHERE `slug`='fibox-marine-shipbuilding';

-- ---------- 7) Sayaç ----------
UPDATE `ayarlar` SET `deger` = (SELECT COUNT(*) FROM `projeler` WHERE `durum`=1)
WHERE `anahtar`='sirket_sayi';

-- ---------- 8) Temel yaklaşım rozetleri: her parça büyük harfle başlasın ----------
INSERT INTO `ayarlar` (`anahtar`,`deger`) VALUES
('yaklasim','Doğru Kaynak + Doğru Teknoloji + Doğru Partner + Doğru Pazar + Profesyonel Proje Yönetimi'),
('yaklasim_en','The Right Resource + The Right Technology + The Right Partner + The Right Market + Professional Project Management')
ON DUPLICATE KEY UPDATE `deger` = VALUES(`deger`);

-- ---------- 9) Crown Edge logosu ----------
-- Not: Site daha önce yanlışlıkla Crown Edge'in amblemini FGG logosu olarak kullanıyordu.
-- Gerçek FGG logosu img/logo-fgg*.png, Crown Edge'inki aşağıdaki yola taşındı.
UPDATE `projeler` SET `gorsel` = 'img/partners/crown-edge.png' WHERE `slug` = 'crown-edge';

-- ---------- 10) NS Media Soft / Crown Edge tanıtım metinleri, NS Teknoloji logosu ----------
UPDATE `projeler` SET `aciklama` = 'NS Media Soft, merkezi İstanbul, Türkiye\'de bulunan ve FGG Holding bünyesinde medya, etkinlik, gayrimenkul ve yapay zekâ teknolojileri alanlarında faaliyet gösteren bir şirkettir.\n\nŞirket; medya ve içerik projelerinin geliştirilmesinin yanı sıra konser, etkinlik, konferans ve seminerlerin planlanması ve organizasyonu alanlarında profesyonel hizmetler sunmaktadır. Bunun yanında gayrimenkul projeleri ve yatırımları ile yapay zekâ tabanlı teknolojilerin ve dijital çözümlerin geliştirilmesi ve uygulanması üzerine çalışmaktadır.\n\n## Ana Faaliyet Alanları\n\n- Medya ve içerik yönetimi\n- Etkinlik ve organizasyon yönetimi\n- Konser organizasyonu ve prodüksiyon\n- Konferans ve seminer organizasyonu\n- Kurumsal etkinlikler ve uluslararası organizasyonlar\n- Gayrimenkul yatırımları ve proje yönetimi\n- Yapay zekâ (AI) ve yapay zekâ tabanlı çözümler\n- Dijital medya ve teknoloji projeleri\n\nNS Media Soft, medya, etkinlik, gayrimenkul ve ileri teknoloji alanlarını bir araya getirerek FGG Holding\'in Türkiye\'deki medya, organizasyon ve teknoloji faaliyetlerini destekleyen stratejik şirketlerinden biridir.',
    `aciklama_en` = 'NS Media Soft, headquartered in Istanbul, Türkiye, is a company operating within FGG Holding across the fields of media, events, real estate and artificial intelligence technologies.\n\nThe company develops media and content projects and provides professional services for the planning, organization and execution of concerts, events, conferences and seminars. In addition, NS Media Soft is involved in real estate projects and investments, as well as the development and implementation of artificial intelligence-based technologies and digital solutions.\n\n## Main Areas of Activity\n\n- Media and content management\n- Event and event management\n- Concert organization and production\n- Conference and seminar organization\n- Corporate and international events\n- Real estate investments and project management\n- Artificial Intelligence (AI)\n- AI-based digital and technology solutions\n- Digital media and technology projects\n\nNS Media Soft brings together media, events, real estate and advanced technology, serving as one of the strategic companies supporting FGG Holding\'s media, event management and technology operations in Türkiye.',
    `ulke` = 'Türkiye',
    `ulke_en` = 'Türkiye',
    `kategori` = 'Sosyal Medya ve Dijital Pazarlama',
    `kategori_en` = 'Social Media & Digital Marketing',
    `durum` = '1'
WHERE `slug` = 'ns-media-soft';

UPDATE `projeler` SET `baslik` = 'Crown Edge Project Management Services',
    `baslik_en` = 'Crown Edge Project Management Services',
    `aciklama` = 'Crown Edge Project Management Services LLC, merkezi Dubai, Birleşik Arap Emirlikleri\'nde bulunan ve FGG Holding\'in proje yönetimi ve uygulama faaliyetlerini yürüten uzmanlaşmış bir şirkettir. Şirket, FGG Holding\'in farklı sektörlerde ve uluslararası pazarlarda gerçekleştirdiği küçük, orta ve büyük ölçekli projelerin planlanması, koordinasyonu, uygulanması ve yönetiminden sorumludur.\n\nŞirket; projelerin başlangıç aşamasından fizibilite, planlama, tedarik, satın alma, lojistik ve operasyonel uygulamaya kadar tüm süreçlerini yöneterek proje yönetimi, uygulama yönetimi, ticari yönetim ve ithalat-ihracat yönetimi hizmetleri sunmaktadır.\n\n## Ana Faaliyet Alanları\n\n- Petrol ve Gaz Proje Yönetimi\n- Altın ve Kıymetli Maden Proje Yönetimi\n- Mücevherat Ticaret ve Proje Yönetimi\n- Tarım ve Gıda Ürünleri Projeleri\n- Kimyasal Ürünler ve Emtia Projeleri\n- İthalat ve İhracat Yönetimi\n- Uluslararası Ticaret ve Ticari Proje Yönetimi\n- Satın Alma ve Tedarik Zinciri Koordinasyonu\n- Lojistik ve Teslimat Yönetimi\n- Proje Uygulama ve Operasyon Yönetimi\n- Uluslararası Tedarikçi ve Alıcı Koordinasyonu\n- Stratejik Ticari Projelerin Yönetimi\n- FGG Holding iştirakleri, iş ortakları ve ortak girişim projelerinin koordinasyonu\n\nCrown Edge Project Management Services LLC, FGG Holding\'in farklı ülkelerde yürüttüğü ticari ve operasyonel projeler için merkezi bir proje yönetimi ve koordinasyon platformu olarak faaliyet göstermekte; proje planlama, uygulama, tedarik, uluslararası ticaret, lojistik ve taraflar arası koordinasyon süreçlerini profesyonel bir yönetim yapısı altında birleştirmektedir.',
    `aciklama_en` = 'Crown Edge Project Management Services LLC, headquartered in Dubai, United Arab Emirates, serves as a specialized project management and execution arm of FGG Holding, responsible for the planning, coordination, supervision and management of the Holding\'s small-, medium- and large-scale projects across multiple industries and international markets.\n\nThe company provides comprehensive project management, execution management, trade management and import & export management services, supporting FGG Holding and its subsidiaries, partners and joint-venture companies throughout the entire project lifecycle — from initial planning and feasibility assessment to execution, procurement, logistics, delivery and final completion.\n\n## Main Areas of Activity\n\n- Oil & Gas Project Management\n- Gold & Precious Metals Project Management\n- Jewelry Trading & Project Management\n- Agricultural & Food Products Projects\n- Chemical Products & Commodities Projects\n- Import & Export Management\n- International Trade & Commercial Project Management\n- Procurement & Supply Chain Coordination\n- Logistics and Delivery Management\n- Project Execution & Operational Management\n- International Supplier & Buyer Coordination\n- Management of Strategic Commercial Projects\n- Coordination of FGG Holding\'s subsidiaries, partners and joint-venture projects\n\nCrown Edge Project Management Services LLC acts as a central coordination platform for FGG Holding\'s commercial and operational projects, bringing together project planning, execution, procurement, international trade, logistics and stakeholder management under one professional management structure.',
    `ulke` = 'Birleşik Arap Emirlikleri',
    `ulke_en` = 'United Arab Emirates',
    `kategori` = 'Büyük Ölçekli Proje Yönetimi',
    `kategori_en` = 'Mega Project Management',
    `durum` = '1'
WHERE `slug` = 'crown-edge';

UPDATE `projeler` SET `gorsel` = 'img/partners/yokyook.png'
WHERE `slug` = 'ns-teknoloji';

-- ---------- 11) YokYook kaydı kaldırıldı (müşteri talebi) ----------
DELETE FROM `projeler` WHERE `slug` = 'yokyook';
UPDATE `ayarlar` SET `deger` = (SELECT COUNT(*) FROM `projeler` WHERE `durum`=1) WHERE `anahtar`='sirket_sayi';

-- ---------- 12) NS Holding kaydı kaldırıldı (müşteri talebi) ----------
DELETE FROM `projeler` WHERE `slug` = 'ns-holding';
UPDATE `ayarlar` SET `deger` = (SELECT COUNT(*) FROM `projeler` WHERE `durum`=1) WHERE `anahtar`='sirket_sayi';

-- ---------- 13) Logo panelden yönetilebilir hâle geldi ----------
-- 'logo' boşsa varsayılan img/logo-fgg-256.png kullanılır.
-- Panel: Ayarlar > Logo. Logo hiçbir işleme sokulmaz, olduğu gibi gösterilir;
-- site zemini siyah olduğu için açık renkli/altın bir logo yüklenmelidir.
INSERT INTO `ayarlar` (`anahtar`,`deger`) VALUES ('logo','')
ON DUPLICATE KEY UPDATE `deger` = VALUES(`deger`);
DELETE FROM `ayarlar` WHERE `anahtar` = 'logo_plaka';
