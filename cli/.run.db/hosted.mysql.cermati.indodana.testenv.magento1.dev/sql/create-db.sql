CREATE DATABASE magento1;
USE magento1
CREATE USER magento1_root;
ALTER USER magento1_root IDENTIFIED BY 'iORTrwh3Ifd7gs30dB8Zryos1zgDe934';
GRANT ALL ON magento1.* TO magento1_root;
GRANT ALL PRIVILEGES ON magento1.* TO magento1_root;
GRANT INSERT, UPDATE ON mysql.* TO magento1_root;
GRANT GRANT OPTION ON magento1.* TO magento1_root;

-- Provision Migration User
CREATE USER magento1_migrations;
GRANT ALL ON magento1.* TO magento1_migrations;
GRANT ALL PRIVILEGES ON magento1.* TO magento1_migrations;
