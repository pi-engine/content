CREATE TABLE IF NOT EXISTS `content_item`
(
    `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `title`       VARCHAR(255)     NOT NULL DEFAULT '',
    `slug`        VARCHAR(255)     NOT NULL DEFAULT '',
    `type`        varchar(64)               DEFAULT NULL,
    `status`      INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `user_id`     INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `time_create` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `time_update` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `time_delete` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `information` JSON,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`)
);

CREATE TABLE IF NOT EXISTS `content_meta_key`
(
    `id`     INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `key`    VARCHAR(64)      NOT NULL DEFAULT '',
    `type`   VARCHAR(64)      NOT NULL DEFAULT 'string',
    `status` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `content_meta_value`
(
    `id`           INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `item_id`      INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `time_create`  INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `status`       INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `key`          VARCHAR(64)      NOT NULL DEFAULT '',
    `value_string` VARCHAR(255)     NOT NULL DEFAULT '',
    `value_number` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
);