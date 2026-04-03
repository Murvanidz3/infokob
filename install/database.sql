-- InfoKobuleti — MySQL 8.0 schema (UTF-8)
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS property_images;
DROP TABLE IF EXISTS property_translations;
DROP TABLE IF EXISTS properties;
DROP TABLE IF EXISTS kobuleti_info;
DROP TABLE IF EXISTS settings;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  phone VARCHAR(50),
  whatsapp_number VARCHAR(50) DEFAULT NULL,
  telegram_username VARCHAR(100) DEFAULT NULL,
  password VARCHAR(255) NOT NULL,
  avatar VARCHAR(255) DEFAULT NULL,
  role ENUM('user','admin') DEFAULT 'user',
  is_active TINYINT(1) DEFAULT 1,
  email_verified TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_users_email (email),
  INDEX idx_users_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE properties (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  slug VARCHAR(255) NOT NULL,
  type ENUM('apartment','house','cottage','land','commercial','hotel_room') NOT NULL,
  deal_type ENUM('sale','rent','daily_rent') DEFAULT 'sale',
  status ENUM('pending','active','rejected','sold','archived') DEFAULT 'pending',
  price DECIMAL(12,2) DEFAULT NULL,
  currency ENUM('USD','GEL','EUR') DEFAULT 'USD',
  price_negotiable TINYINT(1) DEFAULT 0,
  area_m2 DECIMAL(8,2) DEFAULT NULL,
  rooms INT DEFAULT NULL,
  bedrooms INT DEFAULT NULL,
  bathrooms INT DEFAULT NULL,
  floors_total INT DEFAULT NULL,
  floor_number INT DEFAULT NULL,
  has_pool TINYINT(1) DEFAULT 0,
  has_garage TINYINT(1) DEFAULT 0,
  has_balcony TINYINT(1) DEFAULT 0,
  has_garden TINYINT(1) DEFAULT 0,
  sea_distance_m INT DEFAULT NULL,
  address VARCHAR(500) DEFAULT NULL,
  district VARCHAR(255) DEFAULT NULL COMMENT 'e.g. ჩაქვი, სანახარებო, ეკო-პარკი',
  lat DECIMAL(10,8) DEFAULT NULL,
  lng DECIMAL(11,8) DEFAULT NULL,
  contact_name VARCHAR(255) DEFAULT NULL,
  contact_phone VARCHAR(50) DEFAULT NULL,
  contact_whatsapp VARCHAR(50) DEFAULT NULL,
  contact_email VARCHAR(255) DEFAULT NULL,
  contact_telegram VARCHAR(100) DEFAULT NULL,
  is_featured TINYINT(1) DEFAULT 0,
  featured_until DATETIME DEFAULT NULL,
  featured_paid TINYINT(1) DEFAULT 0,
  admin_note TEXT COMMENT 'Admin note when rejecting',
  views INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_slug (slug),
  INDEX idx_prop_status (status),
  INDEX idx_prop_created (created_at),
  INDEX idx_prop_user (user_id),
  INDEX idx_prop_status_created (status, created_at),
  INDEX idx_prop_featured (is_featured, featured_until),
  CONSTRAINT fk_prop_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE property_translations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  property_id INT NOT NULL,
  lang ENUM('ka','ru','en') NOT NULL,
  title VARCHAR(500) NOT NULL,
  description TEXT,
  UNIQUE KEY uniq_lang (property_id, lang),
  INDEX idx_pt_prop (property_id),
  CONSTRAINT fk_pt_prop FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE property_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  property_id INT NOT NULL,
  filename VARCHAR(255) NOT NULL,
  is_main TINYINT(1) DEFAULT 0,
  sort_order INT DEFAULT 0,
  INDEX idx_pi_prop (property_id),
  CONSTRAINT fk_pi_prop FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE kobuleti_info (
  id INT AUTO_INCREMENT PRIMARY KEY,
  section VARCHAR(100) NOT NULL,
  lang ENUM('ka','ru','en') NOT NULL,
  title VARCHAR(500) DEFAULT NULL,
  content LONGTEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_section_lang (section, lang)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE settings (
  key_name VARCHAR(100) PRIMARY KEY,
  value TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default superadmin (password: admin123 — change immediately)
INSERT INTO users (name, email, phone, whatsapp_number, telegram_username, password, role) VALUES
('Super Admin', 'admin@infokobuleti.com', '+995555000001', NULL, NULL, '$2y$12$bhNEDopvrAOEF7c/U/M1Z.dhH69w9wNI3tbksuintJ4nl0JSXf8IS', 'admin');

-- Regular users (password: user123)
INSERT INTO users (name, email, phone, whatsapp_number, telegram_username, password, role) VALUES
('ნიკა მელაძე', 'nika.meladze@example.ge', '+995555123456', '995555123456', 'nika_kobuleti', '$2y$12$8jx9sDYc2yTUWYnnbNQxYO8FF6CtHVPSsvD.ZTfvsxBoWEc4pTgra', 'user'),
('გიორგი ბერიძე', 'giorgi.beridze@example.ge', '+995555234567', NULL, NULL, '$2y$12$8jx9sDYc2yTUWYnnbNQxYO8FF6CtHVPSsvD.ZTfvsxBoWEc4pTgra', 'user'),
('მარიამი ხუციშვილი', 'mariam.khutsishvili@example.ge', '+995555345678', '995555345678', NULL, '$2y$12$8jx9sDYc2yTUWYnnbNQxYO8FF6CtHVPSsvD.ZTfvsxBoWEc4pTgra', 'user');

INSERT INTO settings (key_name, value) VALUES
('site_name_ka', 'ინფო ქობულეთი'),
('site_name_ru', 'Инфо Кобулети'),
('site_name_en', 'Info Kobuleti'),
('google_maps_key', ''),
('contact_phone', '+995555000000'),
('contact_email', 'info@infokobuleti.com'),
('facebook_url', ''),
('instagram_url', ''),
('featured_price_gel', '25'),
('featured_duration_days', '30');

-- Sample properties (user_id 2 = Nika)
INSERT INTO properties (id, user_id, slug, type, deal_type, status, price, currency, price_negotiable, area_m2, rooms, bedrooms, bathrooms, floors_total, floor_number, has_pool, has_garage, has_balcony, has_garden, sea_distance_m, address, district, lat, lng, contact_name, contact_phone, contact_whatsapp, contact_email, contact_telegram, is_featured, featured_until, featured_paid, views) VALUES
(1, 2, '2-otaxiani-bina-chakvshi-sale', 'apartment', 'sale', 'active', 45000.00, 'USD', 0, 65.00, 2, 2, 1, 9, 4, 0, 0, 1, 0, 250, 'ჩაქვი, ზღვის ქუჩა 12', 'ჩაქვი', 41.82000000, 41.78000000, 'ნიკა მ.', '+995555123456', '995555123456', 'nika@example.ge', 'nika_kobuleti', 1, DATE_ADD(NOW(), INTERVAL 20 DAY), 0, 128),
(2, 2, '3-otaxiani-bina-chakvshi-panorama', 'apartment', 'sale', 'active', 78000.00, 'USD', 1, 85.00, 3, 2, 2, 9, 7, 0, 1, 1, 0, 200, 'ჩაქვი, პანორამა', 'ჩაქვი', 41.82100000, 41.78100000, 'ნიკა მ.', '+995555123456', NULL, 'nika@example.ge', NULL, 0, NULL, 0, 245),
(3, 3, 'saxli-centrshi-120k', 'house', 'sale', 'active', 120000.00, 'USD', 0, 180.00, 5, 4, 2, 2, NULL, 0, 1, 0, 1, 800, 'ქობულეთი, ცენტრი', 'ცენტრი', 41.82250000, 41.77550000, 'გიორგი ბ.', '+995555234567', NULL, 'giorgi@example.ge', NULL, 0, NULL, 0, 89),
(4, 3, 'saxli-dzveli-chakvi-180k', 'house', 'sale', 'active', 180000.00, 'USD', 0, 220.00, 6, 4, 3, 2, NULL, 1, 1, 1, 1, 400, 'ჩაქვი, ძველი უბანი', 'ჩაქვი', 41.81900000, 41.77900000, 'გიორგი ბ.', '+995555234567', NULL, NULL, NULL, 1, DATE_ADD(NOW(), INTERVAL 15 DAY), 1, 312),
(5, 4, 'agaraki-sanaxareblo-95k', 'cottage', 'sale', 'active', 95000.00, 'USD', 0, 150.00, 4, 3, 2, 1, NULL, 0, 0, 1, 1, 600, 'სანახარებო', 'სანახარებო', 41.82500000, 41.77000000, 'მარიამი ხ.', '+995555345678', '995555345678', 'mariam@example.ge', NULL, 0, NULL, 0, 56),
(6, 4, 'miwa-eco-park-25k', 'land', 'sale', 'active', 25000.00, 'USD', 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 1200, 'ეკო-პარკის მიმდებარე', 'ეკო-პარკი', 41.81800000, 41.77200000, 'მარიამი ხ.', '+995555345678', NULL, NULL, NULL, 0, NULL, 0, 34),
(7, 2, 'dguriuri-bina-chakvi-50', 'apartment', 'daily_rent', 'active', 50.00, 'USD', 0, 55.00, 2, 1, 1, 12, 5, 0, 0, 1, 0, 150, 'ჩაქვი, სანაპირო', 'ჩაქვი', 41.82050000, 41.78050000, 'ნიკა მ.', '+995555123456', '995555123456', 'nika@example.ge', NULL, 0, NULL, 0, 412),
(8, 3, 'moicavs-ganacileba-pending', 'apartment', 'sale', 'pending', 92000.00, 'USD', 0, 72.00, 3, 2, 1, 9, 3, 0, 0, 1, 0, 300, 'ქობულეთი', 'ცენტრი', 41.82200000, 41.77600000, 'გიორგი ბ.', '+995555234567', NULL, NULL, NULL, 0, NULL, 0, 0);

INSERT INTO property_translations (property_id, lang, title, description) VALUES
(1, 'ka', '2-ოთახიანი ბინა ჩაქვში, ზღვასთან ახლოს', 'კომფორტული ბინა ახალ სახლში. ბალკონიდან ზღვის ხედი. იდეალურია საზაფხულო ცხოვრებისთვის.'),
(1, 'ru', '2-комнатная квартира в Чакви у моря', 'Уютная квартира в новом доме. Вид на море с балкона. Отличный вариант для отдыха.'),
(1, 'en', '2-room apartment in Chakvi near the sea', 'Comfortable apartment in a new building. Sea view from the balcony. Ideal for coastal living.'),
(2, 'ka', '3-ოთახიანი ბინა ჩაქვში — პანორამული ხედი', 'დიდი საერთო ოთახი, ორი სველი წერტილი, გარაჟი. ფასი სასაუბაროა.'),
(2, 'ru', '3-комнатная квартира в Чакви — панорамный вид', 'Просторная гостиная, два санузла, гараж. Цена договорная.'),
(2, 'en', '3-room apartment in Chakvi — panoramic view', 'Large living room, two bathrooms, garage. Price negotiable.'),
(3, 'ka', 'ორი სართულიანი სახლი ქობულეთის ცენტრში', 'კერძო ეზო, ბაღი, პარკინგი. ახლოს სკოლა და სავაჭრო ობიექტები.'),
(3, 'ru', 'Двухэтажный дом в центре Кобулети', 'Частный двор, сад, парковка. Рядом школа и магазины.'),
(3, 'en', 'Two-storey house in central Kobuleti', 'Private yard, garden, parking. Close to schools and shops.'),
(4, 'ka', 'კერძო სახლი ჩაქვში — აუზით და ბაღით', 'პრემიუმ კლასის რემონტი, აუზი, დიდი ვერანდა. ზღვამდე 400 მ.'),
(4, 'ru', 'Частный дом в Чакви — с бассейном и садом', 'Ремонт премиум, бассейн, большая веранда. До моря 400 м.'),
(4, 'en', 'Private house in Chakvi — pool and garden', 'Premium renovation, pool, large terrace. 400 m to the sea.'),
(5, 'ka', 'აგარაკი სანახარებოში — ხე-ტყით გარშემორტყმული', 'შიდა ეზო, ბალკონი, ხილის ხეები. მშვიდი გარემო.'),
(5, 'ru', 'Дача в Санахаребло — в окружении зелени', 'Внутренний двор, балкон, фруктовые деревья. Тихое место.'),
(5, 'en', 'Cottage in Sakhareblo — surrounded by trees', 'Inner yard, balcony, fruit trees. Quiet area.'),
(6, 'ka', 'სასოფლო მიწის ნაკვეთი ეკო-პარკთან', 'საუკეთესო ინვესტიცია. კომუნიკაციები მიმდინარეობს.'),
(6, 'ru', 'Земельный участок у эко-парка', 'Отличная инвестиция. Коммуникации в процессе.'),
(6, 'en', 'Land plot near eco-park', 'Great investment. Utilities in progress.'),
(7, 'ka', 'დღიური ქირა — ბინა ზღვის სანაპიროზე', 'სრული კომპლექტაცია, Wi‑Fi, კონდიციონერი. მინიმუმ 3 დღე.'),
(7, 'ru', 'Посуточная аренда — квартира у моря', 'Полная комплектация, Wi‑Fi, кондиционер. Минимум 3 дня.'),
(7, 'en', 'Daily rent — apartment on the beach', 'Fully equipped, Wi‑Fi, A/C. Minimum 3 nights.'),
(8, 'ka', '3-ოთახიანი ბინა ცენტრში (მოლოდინში)', 'ახალი განცხადება, ფოტოები იტვირთება. დაელოდეთ დამტკიცებას.'),
(8, 'ru', '3-комнатная в центре (на модерации)', 'Новое объявление, фото загружаются. Ожидайте проверки.'),
(8, 'en', '3-room in center (pending)', 'New listing, photos uploading. Awaiting approval.');

INSERT INTO property_images (property_id, filename, is_main, sort_order) VALUES
(1, 'sample-1-main.jpg', 1, 0),
(2, 'sample-2-main.jpg', 1, 0),
(3, 'sample-3-main.jpg', 1, 0),
(4, 'sample-4-main.jpg', 1, 0),
(5, 'sample-5-main.jpg', 1, 0),
(6, 'sample-6-main.jpg', 1, 0),
(7, 'sample-7-main.jpg', 1, 0),
(8, 'sample-8-main.jpg', 1, 0);

INSERT INTO kobuleti_info (section, lang, title, content) VALUES
('about', 'ka', 'ქობულეთი', 'ქობულეთი აჭარის სანაპირო ქალაქია შავი ზღვის პირას.'),
('about', 'ru', 'Кобулети', 'Кобулети — приморский город в Аджарии.'),
('about', 'en', 'Kobuleti', 'Kobuleti is a seaside city on the Black Sea coast of Adjara.'),
('tourism', 'ka', 'ტურიზმი', 'ზაფხულში ქობულეთი სრულდება სტუმრებით.'),
('tourism', 'ru', 'Туризм', 'Летом Кобулети наполняется гостями.'),
('tourism', 'en', 'Tourism', 'In summer Kobuleti fills with visitors.');

ALTER TABLE users AUTO_INCREMENT = 5;
ALTER TABLE properties AUTO_INCREMENT = 9;
