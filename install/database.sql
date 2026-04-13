-- =====================================================
-- InfoKobuleti Database Schema
-- Real Estate Marketplace for Kobuleti, Georgia
-- =====================================================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ─── Users ─────────────────────────────────────────────
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) UNIQUE NOT NULL,
  `phone` VARCHAR(50),
  `whatsapp_number` VARCHAR(50),
  `telegram_username` VARCHAR(100),
  `password` VARCHAR(255) NOT NULL,
  `avatar` VARCHAR(255),
  `role` ENUM('user','admin') DEFAULT 'user',
  `is_active` TINYINT(1) DEFAULT 1,
  `email_verified` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Properties ────────────────────────────────────────
DROP TABLE IF EXISTS `properties`;
CREATE TABLE `properties` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `slug` VARCHAR(255) UNIQUE NOT NULL,
  `type` ENUM('apartment','house','cottage','land','commercial','hotel_room') NOT NULL,
  `deal_type` ENUM('sale','rent','daily_rent') DEFAULT 'sale',
  `status` ENUM('pending','active','rejected','sold','archived') DEFAULT 'pending',
  `price` DECIMAL(12,2),
  `currency` ENUM('USD','GEL','EUR') DEFAULT 'USD',
  `price_negotiable` TINYINT(1) DEFAULT 0,
  `area_m2` DECIMAL(8,2),
  `rooms` INT,
  `bedrooms` INT,
  `bathrooms` INT,
  `floors_total` INT,
  `floor_number` INT,
  `has_pool` TINYINT(1) DEFAULT 0,
  `has_garage` TINYINT(1) DEFAULT 0,
  `has_balcony` TINYINT(1) DEFAULT 0,
  `has_garden` TINYINT(1) DEFAULT 0,
  `has_furniture` TINYINT(1) DEFAULT 0,
  `sea_distance_m` INT,
  `address` VARCHAR(500),
  `district` VARCHAR(255) COMMENT 'e.g. ჩაქვი, სანახარებო, ეკო-პარკი',
  `lat` DECIMAL(10,8),
  `lng` DECIMAL(11,8),
  `contact_name` VARCHAR(255),
  `contact_phone` VARCHAR(50),
  `contact_whatsapp` VARCHAR(50),
  `contact_telegram` VARCHAR(100),
  `contact_email` VARCHAR(255),
  `is_featured` TINYINT(1) DEFAULT 0,
  `featured_until` DATETIME NULL,
  `featured_paid` TINYINT(1) DEFAULT 0,
  `admin_note` TEXT COMMENT 'Admin note when rejecting',
  `views` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Property Translations ─────────────────────────────
DROP TABLE IF EXISTS `property_translations`;
CREATE TABLE `property_translations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `property_id` INT NOT NULL,
  `lang` ENUM('ka','ru','en') NOT NULL,
  `title` VARCHAR(500) NOT NULL,
  `description` TEXT,
  FOREIGN KEY (`property_id`) REFERENCES `properties`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `uniq_lang` (`property_id`, `lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Property Images ───────────────────────────────────
DROP TABLE IF EXISTS `property_images`;
CREATE TABLE `property_images` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `property_id` INT NOT NULL,
  `filename` VARCHAR(255) NOT NULL,
  `is_main` TINYINT(1) DEFAULT 0,
  `sort_order` INT DEFAULT 0,
  FOREIGN KEY (`property_id`) REFERENCES `properties`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Kobuleti Info (CMS) ───────────────────────────────
DROP TABLE IF EXISTS `kobuleti_info`;
CREATE TABLE `kobuleti_info` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `section` VARCHAR(100) NOT NULL,
  `lang` ENUM('ka','ru','en') NOT NULL,
  `title` VARCHAR(500),
  `content` LONGTEXT,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_section_lang` (`section`, `lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Settings ──────────────────────────────────────────
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `key_name` VARCHAR(100) PRIMARY KEY,
  `value` TEXT,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Indexes ───────────────────────────────────────────
ALTER TABLE `properties` ADD INDEX `idx_status` (`status`);
ALTER TABLE `properties` ADD INDEX `idx_created_at` (`created_at`);
ALTER TABLE `properties` ADD INDEX `idx_user_id` (`user_id`);
ALTER TABLE `properties` ADD INDEX `idx_type` (`type`);
ALTER TABLE `properties` ADD INDEX `idx_deal_type` (`deal_type`);
ALTER TABLE `properties` ADD INDEX `idx_is_featured` (`is_featured`);
ALTER TABLE `properties` ADD INDEX `idx_district` (`district`);
ALTER TABLE `properties` ADD INDEX `idx_price` (`price`);
ALTER TABLE `properties` ADD INDEX `idx_featured_until` (`featured_until`);

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- SEED DATA
-- =====================================================

-- ─── Admin User (password: admin123) ───────────────────
INSERT INTO `users` (`name`, `email`, `phone`, `password`, `role`) VALUES 
('Super Admin', 'admin@infokobuleti.com', '+995 555 000 001', '$2y$12$LJ3m4ys3Gl.M5xoB4vCVXOVfOJKlFj0pVDBmqRZ5.k1sW4yvCfyYa', 'admin');

-- ─── Regular Users ─────────────────────────────────────
INSERT INTO `users` (`name`, `email`, `phone`, `whatsapp_number`, `telegram_username`, `password`, `role`) VALUES 
('ნიკა მურვანიძე', 'nika@example.com', '+995 555 123 456', '+995555123456', 'nikamurvanidze', '$2y$12$LJ3m4ys3Gl.M5xoB4vCVXOVfOJKlFj0pVDBmqRZ5.k1sW4yvCfyYa', 'user'),
('მარიამ ბერიძე', 'mariam@example.com', '+995 557 789 012', '+995557789012', NULL, '$2y$12$LJ3m4ys3Gl.M5xoB4vCVXOVfOJKlFj0pVDBmqRZ5.k1sW4yvCfyYa', 'user'),
('გიორგი ხარაზიშვილი', 'giorgi@example.com', '+995 551 345 678', NULL, 'giorgi_k', '$2y$12$LJ3m4ys3Gl.M5xoB4vCVXOVfOJKlFj0pVDBmqRZ5.k1sW4yvCfyYa', 'user');

-- ─── Properties ────────────────────────────────────────

-- Property 1: Apartment in Chakvi (active, sale)
INSERT INTO `properties` (`user_id`, `slug`, `type`, `deal_type`, `status`, `price`, `currency`, `area_m2`, `rooms`, `bedrooms`, `bathrooms`, `floors_total`, `floor_number`, `has_balcony`, `sea_distance_m`, `address`, `district`, `lat`, `lng`, `contact_name`, `contact_phone`, `contact_whatsapp`, `is_featured`, `featured_until`, `views`, `created_at`) VALUES
(2, '2-otaxiani-bina-chaqvshi-45000', 'apartment', 'sale', 'active', 45000.00, 'USD', 65.00, 2, 1, 1, 9, 5, 1, 200, 'ჩაქვი, მთავარი ქუჩა 15', 'chakvi', 41.7200, 41.7450, 'ნიკა მ.', '+995 555 123 456', '+995555123456', 1, '2026-05-13 00:00:00', 156, '2026-04-10 10:00:00');

-- Property 2: Apartment in Center (active, sale)
INSERT INTO `properties` (`user_id`, `slug`, `type`, `deal_type`, `status`, `price`, `currency`, `area_m2`, `rooms`, `bedrooms`, `bathrooms`, `floors_total`, `floor_number`, `has_balcony`, `has_furniture`, `sea_distance_m`, `address`, `district`, `lat`, `lng`, `contact_name`, `contact_phone`, `is_featured`, `views`, `created_at`) VALUES
(3, '3-otaxiani-bina-centrshi-78000', 'apartment', 'sale', 'active', 78000.00, 'USD', 95.00, 3, 2, 1, 12, 8, 1, 1, 100, 'ქობულეთი, რუსთაველის 22', 'center', 41.8114, 41.7700, 'მარიამ ბ.', '+995 557 789 012', 0, 243, '2026-04-08 14:30:00');

-- Property 3: House in Center (active, sale)
INSERT INTO `properties` (`user_id`, `slug`, `type`, `deal_type`, `status`, `price`, `currency`, `area_m2`, `rooms`, `bedrooms`, `bathrooms`, `floors_total`, `has_garage`, `has_garden`, `has_pool`, `sea_distance_m`, `address`, `district`, `lat`, `lng`, `contact_name`, `contact_phone`, `contact_telegram`, `is_featured`, `featured_until`, `views`, `created_at`) VALUES
(2, 'saxli-centrshi-auzi-120000', 'house', 'sale', 'active', 120000.00, 'USD', 180.00, 5, 3, 2, 2, 1, 1, 1, 500, 'ქობულეთი, აღმაშენებლის 8', 'center', 41.8120, 41.7680, 'ნიკა მ.', '+995 555 123 456', 'nikamurvanidze', 1, '2026-05-13 00:00:00', 312, '2026-04-05 09:00:00');

-- Property 4: House in Center (active, sale)
INSERT INTO `properties` (`user_id`, `slug`, `type`, `deal_type`, `status`, `price`, `currency`, `area_m2`, `rooms`, `bedrooms`, `bathrooms`, `floors_total`, `has_garage`, `has_garden`, `sea_distance_m`, `address`, `district`, `lat`, `lng`, `contact_name`, `contact_phone`, `views`, `created_at`) VALUES
(4, 'didi-saxli-centrshi-180000', 'house', 'sale', 'active', 180000.00, 'USD', 250.00, 7, 4, 3, 3, 1, 1, 300, 'ქობულეთი, ჭავჭავაძის 45', 'center', 41.8130, 41.7720, 'გიორგი ხ.', '+995 551 345 678', 89, '2026-04-01 11:00:00');

-- Property 5: Cottage in Sakhareblo (active, sale)
INSERT INTO `properties` (`user_id`, `slug`, `type`, `deal_type`, `status`, `price`, `currency`, `area_m2`, `rooms`, `bedrooms`, `bathrooms`, `floors_total`, `has_garden`, `has_balcony`, `sea_distance_m`, `address`, `district`, `lat`, `lng`, `contact_name`, `contact_phone`, `views`, `created_at`) VALUES
(3, 'agaraki-sanaxareblo-95000', 'cottage', 'sale', 'active', 95000.00, 'USD', 120.00, 4, 2, 1, 2, 1, 1, 1000, 'სანახარებო, მთის ქუჩა', 'sakhareb', 41.8200, 41.7500, 'მარიამ ბ.', '+995 557 789 012', 67, '2026-03-28 16:00:00');

-- Property 6: Land in Eco-park (active, sale)
INSERT INTO `properties` (`user_id`, `slug`, `type`, `deal_type`, `status`, `price`, `currency`, `area_m2`, `sea_distance_m`, `address`, `district`, `lat`, `lng`, `contact_name`, `contact_phone`, `views`, `created_at`) VALUES
(4, 'mitsa-eko-parktan-25000', 'land', 'sale', 'active', 25000.00, 'USD', 500.00, 800, 'ეკო-პარკთან, ქობულეთი', 'ecopark', 41.8050, 41.7600, 'გიორგი ხ.', '+995 551 345 678', 45, '2026-03-25 08:30:00');

-- Property 7: Daily rent apartment (active)
INSERT INTO `properties` (`user_id`, `slug`, `type`, `deal_type`, `status`, `price`, `currency`, `area_m2`, `rooms`, `bedrooms`, `bathrooms`, `floors_total`, `floor_number`, `has_balcony`, `has_furniture`, `sea_distance_m`, `address`, `district`, `lat`, `lng`, `contact_name`, `contact_phone`, `contact_whatsapp`, `views`, `created_at`) VALUES
(2, 'dgiuri-bina-zghvastan-50', 'apartment', 'daily_rent', 'active', 50.00, 'USD', 45.00, 1, 1, 1, 5, 3, 1, 1, 50, 'ქობულეთი, სანაპირო 7', 'center', 41.8100, 41.7750, 'ნიკა მ.', '+995 555 123 456', '+995555123456', 198, '2026-04-12 12:00:00');

-- Property 8: Pending apartment
INSERT INTO `properties` (`user_id`, `slug`, `type`, `deal_type`, `status`, `price`, `currency`, `area_m2`, `rooms`, `bedrooms`, `bathrooms`, `floors_total`, `floor_number`, `has_balcony`, `sea_distance_m`, `address`, `district`, `lat`, `lng`, `contact_name`, `contact_phone`, `views`, `created_at`) VALUES
(3, 'axali-bina-chaqvshi-55000', 'apartment', 'sale', 'pending', 55000.00, 'USD', 72.00, 2, 1, 1, 10, 7, 1, 150, 'ჩაქვი, ზღვის ქუჩა 3', 'chakvi', 41.7210, 41.7440, 'მარიამ ბ.', '+995 557 789 012', 0, '2026-04-13 18:00:00');

-- ─── Property Translations ─────────────────────────────

-- Property 1 translations
INSERT INTO `property_translations` (`property_id`, `lang`, `title`, `description`) VALUES
(1, 'ka', '2-ოთახიანი ბინა ჩაქვში, ზღვიდან 200მ', 'გაყიდვაშია 2-ოთახიანი ბინა ჩაქვში, 9-სართულიანი სახლის მე-5 სართულზე. ბინა არის ახალი გარემონტებული, აქვს ბალკონი ზღვის ხედით. ზღვამდე მანძილი 200 მეტრი. ინფრასტრუქტურა განვითარებულია — მაღაზიები, აფთიაქი, ტრანსპორტი ახლოსაა.'),
(1, 'ru', '2-комнатная квартира в Чакви, 200м от моря', 'Продается 2-комнатная квартира в Чакви, на 5 этаже 9-этажного дома. Квартира с новым ремонтом, есть балкон с видом на море. Расстояние до моря 200 метров. Развитая инфраструктура — магазины, аптека, транспорт рядом.'),
(1, 'en', '2-Room Apartment in Chakvi, 200m from Sea', 'For sale: 2-room apartment in Chakvi, on the 5th floor of a 9-story building. Newly renovated with a balcony offering sea views. 200 meters to the beach. Developed infrastructure — shops, pharmacy, and transport nearby.');

-- Property 2 translations
INSERT INTO `property_translations` (`property_id`, `lang`, `title`, `description`) VALUES
(2, 'ka', '3-ოთახიანი ბინა ცენტრში, ავეჯით', 'გაყიდვაშია 3-ოთახიანი ბინა ქობულეთის ცენტრში, 12-სართულიანი სახლის მე-8 სართულზე. ბინა არის სრულად ავეჯითა და ტექნიკით აღჭურვილი. ბალკონიდან იხსნება ზღვის პანორამული ხედი. ზღვამდე — 100 მეტრი.'),
(2, 'ru', '3-комнатная квартира в центре, с мебелью', 'Продается 3-комнатная квартира в центре Кобулети, на 8 этаже 12-этажного дома. Квартира полностью меблирована и оснащена техникой. С балкона открывается панорамный вид на море. До моря — 100 метров.'),
(2, 'en', '3-Room Apartment in Center, Furnished', 'For sale: 3-room fully furnished apartment in Kobuleti center, on the 8th floor of a 12-story building. Panoramic sea view from the balcony. 100 meters to the beach.');

-- Property 3 translations
INSERT INTO `property_translations` (`property_id`, `lang`, `title`, `description`) VALUES
(3, 'ka', 'სახლი ცენტრში აუზით და ბაღით', 'გაყიდვაშია 2-სართულიანი სახლი ქობულეთის ცენტრში. სახლს აქვს აუზი, კეთილმოწყობილი ბაღი, გარაჟი 2 მანქანაზე. 5 ოთახი, 3 საძინებელი, 2 სველი წერტილი. ზღვამდე 500 მეტრი.'),
(3, 'ru', 'Дом в центре с бассейном и садом', 'Продается 2-этажный дом в центре Кобулети. Дом имеет бассейн, ухоженный сад, гараж на 2 машины. 5 комнат, 3 спальни, 2 ванные. До моря 500 метров.'),
(3, 'en', 'House in Center with Pool and Garden', 'For sale: 2-story house in Kobuleti center with swimming pool, landscaped garden, and 2-car garage. 5 rooms, 3 bedrooms, 2 bathrooms. 500 meters to the sea.');

-- Property 4 translations
INSERT INTO `property_translations` (`property_id`, `lang`, `title`, `description`) VALUES
(4, 'ka', 'დიდი სახლი ცენტრში, 250 კვ.მ', 'გაყიდვაშია 3-სართულიანი სახლი ქობულეთში. 7 ოთახი, 4 საძინებელი, 3 სველი წერტილი. დიდი ეზო ბაღით, გარაჟი. მშვიდ უბანში, ზღვამდე 300 მეტრი. იდეალურია საოჯახო საცხოვრებლად ან სასტუმროდ გადაკეთებისთვის.'),
(4, 'ru', 'Большой дом в центре, 250 кв.м', 'Продается 3-этажный дом в Кобулети. 7 комнат, 4 спальни, 3 ванные. Большой двор с садом, гараж. В тихом районе, 300 метров до моря. Идеально для семейного проживания или переоборудования в гостиницу.'),
(4, 'en', 'Large House in Center, 250 sqm', 'For sale: 3-story house in Kobuleti. 7 rooms, 4 bedrooms, 3 bathrooms. Large yard with garden, garage. Quiet neighborhood, 300 meters to the sea. Ideal for family living or hotel conversion.');

-- Property 5 translations
INSERT INTO `property_translations` (`property_id`, `lang`, `title`, `description`) VALUES
(5, 'ka', 'აგარაკი სანახარებოში, მთის ხედით', 'გაყიდვაშია 2-სართულიანი აგარაკი სანახარებოში. 4 ოთახი, 2 საძინებელი. ბაღი ხეხილით, ბალკონი მთის ხედით. მშვიდი ადგილი ბუნების მოყვარულთათვის. ზღვამდე 1 კმ.'),
(5, 'ru', 'Коттедж в Санахарэбло, вид на горы', 'Продается 2-этажный коттедж в Санахарэбло. 4 комнаты, 2 спальни. Сад с фруктовыми деревьями, балкон с видом на горы. Тихое место для любителей природы. До моря 1 км.'),
(5, 'en', 'Cottage in Sakhareblo with Mountain View', 'For sale: 2-story cottage in Sakhareblo. 4 rooms, 2 bedrooms. Garden with fruit trees, balcony with mountain views. Peaceful location for nature lovers. 1 km to the sea.');

-- Property 6 translations
INSERT INTO `property_translations` (`property_id`, `lang`, `title`, `description`) VALUES
(6, 'ka', 'მიწა ეკო-პარკთან, 500 კვ.მ', 'გაყიდვაშია 500 კვ.მ მიწის ნაკვეთი ეკო-პარკის მიმდებარე ტერიტორიაზე. სამშენებლო ნებართვა მოქმედია. კომუნიკაციები მიყვანილია. იდეალური ადგილი აგარაკის ან საინვესტიციო პროექტისთვის.'),
(6, 'ru', 'Земля у Эко-Парка, 500 кв.м', 'Продается земельный участок 500 кв.м рядом с Эко-Парком. Действующее разрешение на строительство. Коммуникации подведены. Идеальное место для коттеджа или инвестиционного проекта.'),
(6, 'en', 'Land near Eco-Park, 500 sqm', 'For sale: 500 sqm land plot near the Eco-Park area. Active building permit. Utilities connected. Ideal location for a cottage or investment project.');

-- Property 7 translations
INSERT INTO `property_translations` (`property_id`, `lang`, `title`, `description`) VALUES
(7, 'ka', 'დღიური ბინა ზღვასთან, 50$/ღამე', 'ქირავდება დღიურად 1-ოთახიანი ბინა ქობულეთის სანაპიროსთან. სრულად აღჭურვილი ავეჯით და ტექნიკით. ბალკონი ზღვის ხედით. ზღვამდე 50 მეტრი. იდეალური დასასვენებლად.'),
(7, 'ru', 'Посуточная квартира у моря, 50$/ночь', 'Сдается посуточно 1-комнатная квартира у набережной Кобулети. Полностью меблирована и оснащена техникой. Балкон с видом на море. 50 метров до моря. Идеально для отдыха.'),
(7, 'en', 'Daily Rent Apartment by the Sea, $50/night', 'Daily rental: 1-room apartment near Kobuleti waterfront. Fully furnished and equipped. Balcony with sea view. 50 meters to the beach. Perfect for vacation.');

-- Property 8 translations (pending)
INSERT INTO `property_translations` (`property_id`, `lang`, `title`, `description`) VALUES
(8, 'ka', 'ახალი ბინა ჩაქვში, ზღვიდან 150მ', 'გაყიდვაშია 2-ოთახიანი ახალი ბინა ჩაქვში, 10-სართულიანი ახალაშენებული სახლის მე-7 სართულზე. ბინა არის თეთრი კარკასი, მზადაა რემონტისთვის. ბალკონი ზღვის ხედით.'),
(8, 'ru', 'Новая квартира в Чакви, 150м от моря', 'Продается 2-комнатная новая квартира в Чакви, на 7 этаже 10-этажной новостройки. Квартира в белом каркасе, готова к ремонту. Балкон с видом на море.'),
(8, 'en', 'New Apartment in Chakvi, 150m from Sea', 'For sale: 2-room new apartment in Chakvi, on the 7th floor of a 10-story new building. White frame, ready for renovation. Balcony with sea view.');

-- ─── Property Images (placeholder filenames) ───────────
INSERT INTO `property_images` (`property_id`, `filename`, `is_main`, `sort_order`) VALUES
(1, 'prop1_main.jpg', 1, 0),
(1, 'prop1_2.jpg', 0, 1),
(1, 'prop1_3.jpg', 0, 2),
(2, 'prop2_main.jpg', 1, 0),
(2, 'prop2_2.jpg', 0, 1),
(3, 'prop3_main.jpg', 1, 0),
(3, 'prop3_2.jpg', 0, 1),
(3, 'prop3_3.jpg', 0, 2),
(3, 'prop3_4.jpg', 0, 3),
(4, 'prop4_main.jpg', 1, 0),
(4, 'prop4_2.jpg', 0, 1),
(5, 'prop5_main.jpg', 1, 0),
(5, 'prop5_2.jpg', 0, 1),
(6, 'prop6_main.jpg', 1, 0),
(7, 'prop7_main.jpg', 1, 0),
(7, 'prop7_2.jpg', 0, 1),
(7, 'prop7_3.jpg', 0, 2),
(8, 'prop8_main.jpg', 1, 0),
(8, 'prop8_2.jpg', 0, 1);

-- ─── Kobuleti Info Content ─────────────────────────────
INSERT INTO `kobuleti_info` (`section`, `lang`, `title`, `content`) VALUES
('general', 'ka', 'ქობულეთის შესახებ', '<p>ქობულეთი — აჭარის ავტონომიური რესპუბლიკის ქალაქი საქართველოში, შავი ზღვის სანაპიროზე. ქალაქი ცნობილია 12 კილომეტრიანი სანაპირო ზოლით, სუბტროპიკული კლიმატით და მშვიდი ატმოსფეროთი. ქობულეთი იდეალური ადგილია როგორც საცხოვრებლად, ასევე ინვესტიციისთვის.</p>'),
('general', 'ru', 'О Кобулети', '<p>Кобулети — город в Автономной Республике Аджария, Грузия, расположенный на побережье Черного моря. Город известен 12-километровой береговой линией, субтропическим климатом и спокойной атмосферой. Кобулети — идеальное место как для проживания, так и для инвестиций.</p>'),
('general', 'en', 'About Kobuleti', '<p>Kobuleti is a city in the Autonomous Republic of Adjara, Georgia, located on the Black Sea coast. The city is known for its 12-kilometer coastline, subtropical climate, and peaceful atmosphere. Kobuleti is an ideal place for both living and investment.</p>'),
('climate', 'ka', 'კლიმატი', '<p>ქობულეთში სუბტროპიკული ტენიანი კლიმატია. ზაფხულში ტემპერატურა 25-32°C-მდეა, ზამთარში იშვიათად ეცემა 5°C-ზე ქვემოთ. საუკეთესო დრო ვიზიტისთვის: მაისი-ოქტომბერი.</p>'),
('climate', 'ru', 'Климат', '<p>В Кобулети влажный субтропический климат. Летом температура достигает 25-32°C, зимой редко опускается ниже 5°C. Лучшее время для визита: май-октябрь.</p>'),
('climate', 'en', 'Climate', '<p>Kobuleti has a humid subtropical climate. Summer temperatures reach 25-32°C, while winter rarely drops below 5°C. Best time to visit: May-October.</p>'),
('transport', 'ka', 'ტრანსპორტი', '<p>ქობულეთი ადვილად მისაწვდომია: ბათუმის აეროპორტიდან — 25 წთ, ბათუმიდან — 20 წთ, თბილისიდან მატარებლით — 5 სთ. ქალაქში მოძრაობს ადგილობრივი ავტობუსები და ტაქსი.</p>'),
('transport', 'ru', 'Транспорт', '<p>Кобулети легко доступен: от аэропорта Батуми — 25 мин, от Батуми — 20 мин, от Тбилиси поездом — 5 часов. В городе курсируют местные автобусы и такси.</p>'),
('transport', 'en', 'Transport', '<p>Kobuleti is easily accessible: From Batumi airport — 25 min, from Batumi — 20 min, from Tbilisi by train — 5 hours. Local buses and taxis operate in the city.</p>');

-- ─── Default Settings ──────────────────────────────────
INSERT INTO `settings` (`key_name`, `value`) VALUES
('site_name_ka', 'ინფო ქობულეთი'),
('site_name_ru', 'Инфо Кобулети'),
('site_name_en', 'Info Kobuleti'),
('google_maps_key', 'AIzaSyBprMKl2_uowt9qp4x2_R4JICAHC4Yi810'),
('contact_phone', '+995 555 000 001'),
('contact_email', 'info@infokobuleti.com'),
('facebook_url', 'https://facebook.com/infokobuleti'),
('instagram_url', 'https://instagram.com/infokobuleti'),
('featured_price_gel', '25'),
('featured_duration_days', '30');
