CREATE USER "{{name}}" IDENTIFIED BY '{{password}}';
GRANT INSERT, SELECT, DELETE, UPDATE ON magento2.* TO "{{name}}";
