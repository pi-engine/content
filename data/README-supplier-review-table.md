# جدول نظرات تأمین‌کننده (content_supplier_review)

اگر جدول را در دیتابیس نمی‌بینید، یکی از این دو روش را انجام دهید.

## روش ۱: اجرای اسکریپت PHP (با متغیر محیطی دیتابیس)

از پوشه backend، با تنظیم متغیرهای دیتابیس:

```bash
MYSQL_DB_HOST=نام_هاست MYSQL_DB_USER=کاربر MYSQL_DB_PASSWORD=رمز MYSQL_DB_NAME=knowledge php module/Content/bin/install-supplier-review-table.php
```

مثال (داخل Docker، اگر سرویس وب شما به mysql وصل است):

```bash
docker compose exec web env MYSQL_DB_HOST=mysql MYSQL_DB_USER=root MYSQL_DB_PASSWORD=yourpass MYSQL_DB_NAME=knowledge php module/Content/bin/install-supplier-review-table.php
```

## روش ۲: اجرای مستقیم SQL

در phpMyAdmin، MySQL Workbench یا هر کلاینت MySQL، دیتابیس `knowledge` را انتخاب کنید و این دستور را اجرا کنید (یا از فایل `content_supplier_review.sql` استفاده کنید):

```sql
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
```

بعد از ایجاد جدول، ثبت نظر از پنل کاربر باید بدون خطا کار کند.

---

## به‌روزرسانی: امتیاز جدا و نظرهای متعدد

برای قابلیت «یک امتیاز per کاربر (قابل بروزرسانی) + چندین نظر»، پس از ایجاد جدول، این دستور را اجرا کنید:

```bash
# در کلاینت MySQL فایل زیر را اجرا کنید:
# data/alter_supplier_review_rating_comment.sql
```

یا مستقیم:

```sql
ALTER TABLE `content_supplier_review`
    ADD COLUMN `review_type` VARCHAR(20) NOT NULL DEFAULT 'rating' COMMENT 'rating or comment' AFTER `user_id`,
    MODIFY COLUMN `rating` tinyint UNSIGNED NULL DEFAULT NULL COMMENT '1-5, only for review_type=rating';
```
