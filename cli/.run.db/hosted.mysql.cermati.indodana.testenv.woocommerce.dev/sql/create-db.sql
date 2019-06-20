CREATE DATABASE woocommerce;
USE woocommerce
CREATE USER woocommerce_root;
ALTER USER woocommerce_root IDENTIFIED BY 'SHqG2k0l9dB0yw1o4VsHUE2TW4DBQzuB';
GRANT ALL ON woocommerce.* TO woocommerce_root;
GRANT ALL PRIVILEGES ON woocommerce.* TO woocommerce_root;
GRANT INSERT, UPDATE ON mysql.* TO woocommerce_root;
GRANT GRANT OPTION ON woocommerce.* TO woocommerce_root;

-- Provision Migration User
CREATE USER woocommerce_migrations;
GRANT ALL ON woocommerce.* TO woocommerce_migrations;
GRANT ALL PRIVILEGES ON woocommerce.* TO woocommerce_migrations;
