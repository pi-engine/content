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



CREATE TABLE `content_meta_value` (
                                      `id` int UNSIGNED NOT NULL,
                                      `item_id` int UNSIGNED NOT NULL DEFAULT '0',
                                      `item_slug` varchar(255) DEFAULT NULL,
                                      `meta_key` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '''''',
                                      `value_string` varchar(255) NOT NULL DEFAULT '''''',
                                      `value_number` int UNSIGNED NOT NULL DEFAULT '0',
                                      `value_id` varchar(255) NOT NULL DEFAULT '''''',
                                      `value_slug` varchar(255) DEFAULT NULL,
                                      `status` int UNSIGNED NOT NULL DEFAULT '0',
                                      `logo` varchar(255) NOT NULL DEFAULT 'no-image.png',
                                      `time_create` int UNSIGNED NOT NULL DEFAULT '0',
                                      `time_update` int NOT NULL DEFAULT '0',
                                      `time_delete` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `content_meta_key` (
                                    `id` int UNSIGNED NOT NULL,
                                    `key` varchar(64) NOT NULL DEFAULT '',
                                    `value` varchar(255) DEFAULT NULL,
                                    `type` varchar(64) NOT NULL DEFAULT 'string',
                                    -- store data of required , entity type , ....
                                    `option` json DEFAULT NULL,
                                    `suffix` varchar(255) NOT NULL DEFAULT '',
                                    `logo` varchar(255) NOT NULL DEFAULT 'no-image.png',
                                    `status` int UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


ALTER TABLE `content_meta_key`
    ADD PRIMARY KEY (`id`);

ALTER TABLE `content_meta_key`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;


CREATE TABLE `content_meta_value` (
                                      `id` int(10) UNSIGNED NOT NULL,
                                      `item_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
                                      `key` varchar(64) NOT NULL DEFAULT '''''',
                                      `value_string` varchar(255) NOT NULL DEFAULT '''''',
                                      `value_number` int(10) UNSIGNED NOT NULL DEFAULT 0,
                                      `value_id` varchar(255) NOT NULL DEFAULT '''''',
                                      `status` int(10) UNSIGNED NOT NULL DEFAULT 0,
                                      `logo` varchar(255) NOT NULL DEFAULT 'no-image.png',
                                      `time_create` int(10) UNSIGNED NOT NULL DEFAULT 0,
                                      `time_update` int(11) NOT NULL DEFAULT 0,
                                      `time_delete` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4_general_ci;


CREATE TABLE `content_log` (
                               `id` int(11) NOT NULL,
                               `user_id` int(11) NOT NULL DEFAULT 0,
                               `item_id` int(11) NOT NULL DEFAULT 0,
                               `action` varchar(255) NOT NULL DEFAULT '',
                               `type` varchar(255) NOT NULL DEFAULT '',
                               `event` varchar(255) NOT NULL DEFAULT '',
                               `date` varchar(255) NOT NULL DEFAULT '',
                               `time_create` int(11) NOT NULL DEFAULT 0,
                               `time_update` int(11) NOT NULL DEFAULT 0,
                               `time_delete` int(11) NOT NULL DEFAULT 0
);