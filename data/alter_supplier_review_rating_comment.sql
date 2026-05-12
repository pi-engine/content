-- Add review_type: 'rating' = one per user/supplier (updatable), 'comment' = multiple per user/supplier.
-- Run after content_supplier_review exists.

ALTER TABLE `content_supplier_review`
    ADD COLUMN `review_type` VARCHAR(20) NOT NULL DEFAULT 'rating' COMMENT 'rating or comment' AFTER `user_id`,
    MODIFY COLUMN `rating` tinyint UNSIGNED NULL DEFAULT NULL COMMENT '1-5, only for review_type=rating';

-- Optional: unique so one rating row per (supplier_id, user_id)
-- ALTER TABLE `content_supplier_review` ADD UNIQUE KEY `supplier_user_rating` (`supplier_id`, `user_id`, `review_type`);
