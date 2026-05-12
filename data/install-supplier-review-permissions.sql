-- Insert permission for supplier-review list and update-status (fix: Role with identifier "admin-content-supplier-review-list" not found)
-- Run once on DB knowledge, e.g. mysql -u user -p knowledge < module/Content/data/install-supplier-review-permissions.sql

INSERT IGNORE INTO `permission_resource` (`title`, `key`, `section`, `module`, `type`) VALUES
('admin-content-supplier-review-list', 'admin-content-supplier-review-list', 'admin', 'content', 'system'),
('admin-content-supplier-review-update-status', 'admin-content-supplier-review-update-status', 'admin', 'content', 'system');

INSERT IGNORE INTO `permission_page` (`title`, `key`, `resource`, `section`, `module`, `package`, `handler`, `cache_type`, `cache_ttl`, `cache_level`) VALUES
('admin-content-supplier-review-list', 'admin-content-supplier-review-list', 'admin-content-supplier-review-list', 'admin', 'content', 'supplier-review', 'list', 'page', 0, ''),
('admin-content-supplier-review-update-status', 'admin-content-supplier-review-update-status', 'admin-content-supplier-review-update-status', 'admin', 'content', 'supplier-review', 'update-status', 'page', 0, '');

INSERT IGNORE INTO `permission_role` (`key`, `resource`, `section`, `module`, `role`) VALUES
('admin-admin-content-supplier-review-list', 'admin-content-supplier-review-list', 'admin', 'content', 'admin'),
('admin-admin-content-supplier-review-update-status', 'admin-content-supplier-review-update-status', 'admin', 'content', 'admin');
