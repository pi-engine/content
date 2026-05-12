# دسترسی نقش supplier به اندپوینت‌های ادمین (پنل تأمین‌کننده)

برای اینکه کاربران با نقش **supplier** بتوانند از پنل تأمین‌کننده به همه سرویس‌های لازم دسترسی داشته باشند، دسترسی‌های زیر باید در `permission_role` وجود داشته باشند.

## سرویس‌های پنل ساپلیر که اتوریزیشن دارند

| ماژول   | اندپوینت‌ها | Resourceها |
|--------|--------------|------------|
| Content | `admin/content/supplier/*`, `material/*`, `material-offer/*`, `industry/*` | admin-content-item-get, list, add, edit, delete, **update** |
| Support | `admin/support/item/*` (درخواست‌ها، تیکت‌ها) | admin-support-item-get, list, edit, update |
| User    | `admin/user/profile/list`, add, edit, password | user-profile-list, add, edit, password |

سرویس‌های `api/*` (مثل لاگین، پروفایل، نوتیفیکیشن) و `public/*` جدا هستند و به این دسترسی‌ها وابسته نیستند.

## تغییرات انجام‌شده در کد

- **User\\Service\\RoleService::getAdminRoleList():** نقش `supplier` همیشه به لیست نقش‌های مجاز ادمین اضافه می‌شود (حتی در صورت استفاده از کش).

## نصب دسترسی (یکی از دو روش)

### روش ۱: اسکریپت PHP (توصیه می‌شود)

از پوشه backend، فقط دسترسی نقش supplier را اضافه می‌کند (برای Content، Support و User):

```bash
MYSQL_DB_HOST=127.0.0.1 MYSQL_DB_USER=... MYSQL_DB_PASSWORD=... MYSQL_DB_NAME=knowledge php module/Content/bin/install-supplier-role-permissions.php
```

### روش ۲: اسکریپت Content (resource + صفحه + دسترسی ادمین و ساپلیر فقط برای Content)

اگر resourceهای Content (مثل admin-content-item-*) در دیتابیس نیستند:

```bash
php module/Content/bin/install-content-permissions.php
```

این اسکریپت برای ماژول Content هم `permission_resource` و `permission_page` و هم دسترسی admin و supplier را می‌سازد. برای دسترسی ساپلیر به **Support** و **User** حتماً روش ۱ یا SQL زیر را اجرا کنید.

### روش ۳: اجرای مستقیم SQL

```bash
mysql -u user -p knowledge < module/Content/data/install-supplier-role-admin-permissions.sql
```

توجه: ردیف‌های مربوط به Support و User فقط در صورت وجود داشتن resourceهای مربوط در `permission_resource` / `permission_page` (توسط نصب ماژول‌های Support و User) درست کار می‌کنند.

## اگر هنوز ۴۰۳ می‌گیرید (You dont have access to this area ! 2)

۱. **دسترسی ساپلیر در دیتابیس:** حتماً اسکریپت نصب را با همان دیتابیسی که بک‌اند استفاده می‌کند اجرا کنید:
   ```bash
   cd /path/to/backend
   MYSQL_DB_HOST=127.0.0.1 MYSQL_DB_USER=USER MYSQL_DB_PASSWORD=PASS MYSQL_DB_NAME=knowledge php module/Content/bin/install-supplier-role-permissions.php
   ```
۲. **بررسی سریع:** برای چک کردن و در صورت نبود، درج فقط دسترسی material/list:
   ```bash
   MYSQL_DB_HOST=... MYSQL_DB_USER=... MYSQL_DB_PASSWORD=... MYSQL_DB_NAME=knowledge php module/Content/bin/verify-supplier-material-access.php
   ```
۳. **نقش کاربر:** کاربر باید در جدول `role_account` نقش `supplier` داشته باشد (برای همان `user_id` که در توکن است). اگر با ادمین نقش را تازه اضافه کرده‌اید، کاربر باید یک بار **خروج و ورود مجدد** کند تا کش نقش‌ها به‌روز شود.

## بعد از نصب

بک‌اند را یک بار ریستارت کنید. با تغییر قبلی در `RoleService` نیازی به خالی کردن کش `roles-admin` نیست.
