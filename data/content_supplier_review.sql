-- Supplier reviews: user can rate (1-5) and comment; admin must approve before display.
-- Run once to create the table.

CREATE TABLE IF NOT EXISTS `content_supplier_review` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
    `supplier_id` int UNSIGNED NOT NULL COMMENT 'content_item.id (type=supplier)',
    `user_id` int UNSIGNED NOT NULL COMMENT 'user_account.id',
    `rating` tinyint UNSIGNED NOT NULL DEFAULT 5 COMMENT '1-5',
    `comment` text DEFAULT NULL,
    `status` varchar(32) NOT NULL DEFAULT 'pending' COMMENT 'pending, approved, rejected',
    `time_create` int UNSIGNED NOT NULL DEFAULT 0,
    `time_update` int UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `supplier_id` (`supplier_id`),
    KEY `user_id` (`user_id`),
    KEY `status` (`status`),
    KEY `supplier_status` (`supplier_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
