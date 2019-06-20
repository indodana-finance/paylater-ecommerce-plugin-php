CREATE USER "{{name}}" IDENTIFIED BY '{{password}}';
GRANT ALL PRIVILEGES ON templatedb.* TO "{{name}}";