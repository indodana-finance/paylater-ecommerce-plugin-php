CREATE DATABASE prestashop;
USE prestashop
CREATE USER prestashop_root;
ALTER USER prestashop_root IDENTIFIED BY 'qef3S5MPm9FHiNUthrhoKg8BSgUzRWew';
GRANT ALL ON prestashop.* TO prestashop_root;
GRANT ALL PRIVILEGES ON prestashop.* TO prestashop_root;
GRANT INSERT, UPDATE ON mysql.* TO prestashop_root;
GRANT GRANT OPTION ON prestashop.* TO prestashop_root;

-- Provision Migration User
CREATE USER prestashop_migrations;
GRANT ALL ON prestashop.* TO prestashop_migrations;
GRANT ALL PRIVILEGES ON prestashop.* TO prestashop_migrations;
