-- Add parent_id to content_item if missing (safe for existing data: NOT NULL DEFAULT 0).
-- Idempotent: safe to run multiple times.
-- Run from backend: mysql -u ... -p knowledge < module/Content/data/add-parent_id-to-content_item.sql

SET @dbname = DATABASE();
SET @stmt = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'content_item' AND COLUMN_NAME = 'parent_id') > 0,
    'SELECT 1',
    'ALTER TABLE `content_item` ADD COLUMN `parent_id` int UNSIGNED NOT NULL DEFAULT ''0'' AFTER `id`'
));
PREPARE stmt FROM @stmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
