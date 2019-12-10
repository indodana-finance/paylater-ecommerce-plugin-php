CREATE USER "{{name}}"@'%' IDENTIFIED BY '{{password}}';
GRANT INSERT, SELECT, DELETE, UPDATE ON opencartv2.* TO "{{name}}"@'%';
CREATE USER "{{name}}"@'localhost' IDENTIFIED BY '{{password}}';
GRANT INSERT, SELECT, DELETE, UPDATE ON opencartv2.* TO "{{name}}"@'localhost';
