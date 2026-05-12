-- انواع امتیاز برای هر تأمین‌کننده (هر کمپانی چند نوع امتیاز دارد)
-- Run once to create table and insert default types.

CREATE TABLE IF NOT EXISTS `content_score_type` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
    `key` varchar(64) NOT NULL COMMENT 'slug for API e.g. quality_product',
    `title_fa` varchar(128) NOT NULL COMMENT 'عنوان فارسی',
    `title_en` varchar(128) NOT NULL DEFAULT '' COMMENT 'عنوان انگلیسی',
    `sort_order` smallint UNSIGNED NOT NULL DEFAULT 0,
    `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '1=active',
    PRIMARY KEY (`id`),
    UNIQUE KEY `key` (`key`),
    KEY `status_sort` (`status`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `content_score_type` (`id`, `key`, `title_fa`, `title_en`, `sort_order`, `status`) VALUES
(1, 'quality_product',      'کیفیت محصول',                    'Product quality',               1, 1),
(2, 'price_fair',            'قیمت مناسب',                     'Fair pricing',                  2, 1),
(3, 'on_time_delivery',      'تحویل به موقع',                  'On-time delivery',              3, 1),
(4, 'communication',        'کیفیت ارتباط و پاسخگویی',        'Communication & responsiveness', 4, 1),
(5, 'packaging_delivery',    'بسته‌بندی و ارسال',              'Packaging & shipping',          5, 1),
(6, 'documentation',        'مستندات و مدارک',                 'Documentation & paperwork',    6, 1);
