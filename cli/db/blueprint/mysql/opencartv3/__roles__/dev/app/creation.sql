CREATE USER "{{name}}"@'%' IDENTIFIED BY '{{password}}';
GRANT INSERT, SELECT, DELETE, UPDATE ON opencartv3.* TO "{{name}}"@'%';
CREATE USER "{{name}}"@'localhost' IDENTIFIED BY '{{password}}';
GRANT INSERT, SELECT, DELETE, UPDATE ON opencartv3.* TO "{{name}}"@'localhost';
