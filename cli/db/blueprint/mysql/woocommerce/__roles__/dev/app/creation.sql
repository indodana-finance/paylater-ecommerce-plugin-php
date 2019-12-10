CREATE USER "{{name}}"@'%' IDENTIFIED BY '{{password}}';
GRANT INSERT, SELECT, DELETE, UPDATE ON woocommerce.* TO "{{name}}"@'%';
CREATE USER "{{name}}"@'localhost' IDENTIFIED BY '{{password}}';
GRANT INSERT, SELECT, DELETE, UPDATE ON woocommerce.* TO "{{name}}"@'localhost';
