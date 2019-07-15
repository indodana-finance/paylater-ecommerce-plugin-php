CREATE USER "{{name}}" IDENTIFIED BY '{{password}}';
GRANT INSERT, SELECT, DELETE, UPDATE ON magento1.* TO "{{name}}";
