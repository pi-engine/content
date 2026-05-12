-- امتیازهای چندنوعی کاربران برای هر تأمین‌کننده (یک رکورد per user per supplier per score_type)
-- Run after content_score_type exists.

CREATE TABLE IF NOT EXISTS `content_supplier_score` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
    `supplier_id` int UNSIGNED NOT NULL COMMENT 'content_item.id (type=supplier)',
    `user_id` int UNSIGNED NOT NULL COMMENT 'user_account.id',
    `score_type_id` int UNSIGNED NOT NULL COMMENT 'content_score_type.id',
    `score` tinyint UNSIGNED NOT NULL COMMENT '1-5',
    `status` varchar(32) NOT NULL DEFAULT 'pending' COMMENT 'pending, approved, rejected',
    `time_create` int UNSIGNED NOT NULL DEFAULT 0,
    `time_update` int UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `supplier_user_type` (`supplier_id`, `user_id`, `score_type_id`),
    KEY `supplier_id` (`supplier_id`),
    KEY `user_id` (`user_id`),
    KEY `score_type_id` (`score_type_id`),
    KEY `supplier_status` (`supplier_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
