-- Grant role "supplier" access to all admin endpoints used by the supplier panel.
-- Run once: mysql -u user -p knowledge < module/Content/data/install-supplier-role-admin-permissions.sql
-- Resources must already exist (Support/User modules installers create their own; Content: run install-content-permissions.php if needed).
-- After running: restart backend or ensure RoleService includes supplier in getAdminRoleList().

-- Content: admin/content/supplier/*, material/*, material-offer/*, industry/*
INSERT IGNORE INTO `permission_role` (`key`, `resource`, `section`, `module`, `role`) VALUES
('supplier-admin-content-item-get',    'admin-content-item-get',    'admin', 'content', 'supplier'),
('supplier-admin-content-item-list',   'admin-content-item-list',   'admin', 'content', 'supplier'),
('supplier-admin-content-item-add',    'admin-content-item-add',    'admin', 'content', 'supplier'),
('supplier-admin-content-item-edit',   'admin-content-item-edit',   'admin', 'content', 'supplier'),
('supplier-admin-content-item-delete', 'admin-content-item-delete', 'admin', 'content', 'supplier'),
('supplier-admin-content-item-update', 'admin-content-item-update', 'admin', 'content', 'supplier');

-- Support: admin/support/item/* (tickets, requests)
INSERT IGNORE INTO `permission_role` (`key`, `resource`, `section`, `module`, `role`) VALUES
('supplier-admin-support-item-get',    'admin-support-item-get',    'admin', 'support', 'supplier'),
('supplier-admin-support-item-list',   'admin-support-item-list',   'admin', 'support', 'supplier'),
('supplier-admin-support-item-edit',   'admin-support-item-edit',   'admin', 'support', 'supplier'),
('supplier-admin-support-item-update', 'admin-support-item-update', 'admin', 'support', 'supplier');

-- User: admin/user/profile/* (list, add, edit, password - used in request dialogs)
INSERT IGNORE INTO `permission_role` (`key`, `resource`, `section`, `module`, `role`) VALUES
('supplier-user-profile-list',   'user-profile-list',   'admin', 'user', 'supplier'),
('supplier-user-profile-add',    'user-profile-add',    'admin', 'user', 'supplier'),
('supplier-user-profile-edit',   'user-profile-edit',   'admin', 'user', 'supplier'),
('supplier-user-profile-password','user-profile-password','admin', 'user', 'supplier');
