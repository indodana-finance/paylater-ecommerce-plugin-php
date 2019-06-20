CREATE DATABASE magento2;
USE magento2
CREATE USER magento2_root;
ALTER USER magento2_root IDENTIFIED BY 'BPsKchAWk2Nyw009sHsXBCNVIH4IgJde';
GRANT ALL ON magento2.* TO magento2_root;
GRANT ALL PRIVILEGES ON magento2.* TO magento2_root;
GRANT INSERT, UPDATE ON mysql.* TO magento2_root;
GRANT GRANT OPTION ON magento2.* TO magento2_root;

-- Provision Migration User
CREATE USER magento2_migrations;
GRANT ALL ON magento2.* TO magento2_migrations;
GRANT ALL PRIVILEGES ON magento2.* TO magento2_migrations;
