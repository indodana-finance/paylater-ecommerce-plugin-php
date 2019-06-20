CREATE USER "{{name}}" IDENTIFIED BY '{{password}}';
GRANT INSERT, SELECT, DELETE, UPDATE ON opencartv3.* TO "{{name}}";
