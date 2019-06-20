CREATE USER "{{name}}" IDENTIFIED BY '{{password}}';
GRANT INSERT, SELECT, DELETE, UPDATE ON opencartv2.* TO "{{name}}";
