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
                                      `id` int(10) UNSIGNED NOT NULL,
                                      `item_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
                                      `meta_key` varchar(64) NOT NULL DEFAULT '''''',
                                      `value_string` varchar(255) NOT NULL DEFAULT '''''',
                                      `value_id` varchar(255) NOT NULL DEFAULT '',
                                      `value_number` int(10) UNSIGNED NOT NULL DEFAULT 0,
                                      `status` int(10) UNSIGNED NOT NULL DEFAULT 0,
                                      `logo` varchar(255) NOT NULL DEFAULT 'no-image.png',
                                      `time_create` int(10) UNSIGNED NOT NULL DEFAULT 0,
                                      `time_update` int(11) NOT NULL DEFAULT 0,
                                      `time_delete` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `content_meta_key` (`id`, `key`, `value`, `type`, `suffix`, `logo`, `status`) VALUES
                                                                                              (1, 'like', 'Like', 'int', '', 'no-image.png', 1),
                                                                                              (2, 'dislike', 'Dislike', 'int', '', 'no-image.png', 1),
                                                                                              (3, 'comment_count', 'Number of comments', 'int', '', 'no-image.png', 1),
                                                                                              (4, 'category', 'Category', 'string', '', 'no-image.png', 1),
                                                                                              (5, 'tag', 'Tag', 'string', '', 'no-image.png', 1);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


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