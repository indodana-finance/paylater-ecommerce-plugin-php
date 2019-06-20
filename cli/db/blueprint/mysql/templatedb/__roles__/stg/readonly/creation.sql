CREATE USER "{{name}}" IDENTIFIED BY '{{password}}';
GRANT SELECT ON templatedb.* TO "{{name}}";