CREATE DATABASE opencartv3;
USE opencartv3
CREATE USER opencartv3_root;
ALTER USER opencartv3_root IDENTIFIED BY '0bSXwNsh3C5KVon8bXkQDDQEDXN8lfuB';
GRANT ALL ON opencartv3.* TO opencartv3_root;
GRANT ALL PRIVILEGES ON opencartv3.* TO opencartv3_root;
GRANT INSERT, UPDATE ON mysql.* TO opencartv3_root;
GRANT GRANT OPTION ON opencartv3.* TO opencartv3_root;

-- Provision Migration User
CREATE USER opencartv3_migrations;
GRANT ALL ON opencartv3.* TO opencartv3_migrations;
GRANT ALL PRIVILEGES ON opencartv3.* TO opencartv3_migrations;
