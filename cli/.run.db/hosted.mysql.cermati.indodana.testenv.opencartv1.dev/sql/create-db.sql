CREATE DATABASE opencartv1;
USE opencartv1
CREATE USER opencartv1_root;
ALTER USER opencartv1_root IDENTIFIED BY '29bU2CMn9bb15hdRCWyDZh7FylW7s9Xh';
GRANT ALL ON opencartv1.* TO opencartv1_root;
GRANT ALL PRIVILEGES ON opencartv1.* TO opencartv1_root;
GRANT INSERT, UPDATE ON mysql.* TO opencartv1_root;
GRANT GRANT OPTION ON opencartv1.* TO opencartv1_root;

-- Provision Migration User
CREATE USER opencartv1_migrations;
GRANT ALL ON opencartv1.* TO opencartv1_migrations;
GRANT ALL PRIVILEGES ON opencartv1.* TO opencartv1_migrations;
