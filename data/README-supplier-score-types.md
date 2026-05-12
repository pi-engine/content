# امتیازهای چندنوعی تأمین‌کننده (Supplier multi-type scores)

هر کمپانی (تأمین‌کننده) می‌تواند چند نوع امتیاز داشته باشد؛ کاربران برای هر معیار جداگانه از ۱ تا ۵ امتیاز می‌دهند.

## جداول

- **content_score_type:** تعریف انواع امتیاز (کیفیت محصول، قیمت مناسب، تحویل به موقع و غیره).
- **content_supplier_score:** امتیاز هر کاربر برای هر تأمین‌کننده و هر نوع امتیاز (یک رکورد به ازای هر ترکیب).

## نصب

از پوشه backend با متغیرهای محیطی دیتابیس:

```bash
php module/Content/bin/install-supplier-score-tables.php
```

یا اجرای دستی فایل‌های SQL به ترتیب:

1. `module/Content/data/content_score_type.sql`
2. `module/Content/data/content_supplier_score.sql`

## انواع امتیاز پیش‌فرض (در دیتابیس)

| key                | title_fa                        |
|--------------------|----------------------------------|
| quality_product    | کیفیت محصول                     |
| price_fair         | قیمت مناسب                      |
| on_time_delivery   | تحویل به موقع                   |
| communication      | کیفیت ارتباط و پاسخگویی        |
| packaging_delivery | بسته‌بندی و ارسال               |
| documentation      | مستندات و مدارک                 |

## APIهای کاربر (user)

- **POST** `user/content/supplier-review/score-types` — لیست انواع امتیاز (بدون پارامتر).
- **POST** `user/content/supplier-review/add-scores` — ثبت/بروزرسانی امتیازها. بدنه: `{ supplier_id یا supplier_slug, scores: { score_type_id: 1-5, ... } }`.
- **POST/GET** `user/content/supplier-review/get-my-scores` — امتیازهای فعلی کاربر برای یک تأمین‌کننده. پارامتر: supplier_id یا supplier_slug.
- **POST/GET** `user/content/supplier-review/get-supplier-score-averages` — میانگین امتیازها به تفکیک نوع برای یک تأمین‌کننده. پارامتر: supplier_id یا supplier_slug.
