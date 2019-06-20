CREATE DATABASE opencartv2;
USE opencartv2
CREATE USER opencartv2_root;
ALTER USER opencartv2_root IDENTIFIED BY 'qIMVTr2JGRLNo549nC8AXmc22nnRBOTF';
GRANT ALL ON opencartv2.* TO opencartv2_root;
GRANT ALL PRIVILEGES ON opencartv2.* TO opencartv2_root;
GRANT INSERT, UPDATE ON mysql.* TO opencartv2_root;
GRANT GRANT OPTION ON opencartv2.* TO opencartv2_root;

-- Provision Migration User
CREATE USER opencartv2_migrations;
GRANT ALL ON opencartv2.* TO opencartv2_migrations;
GRANT ALL PRIVILEGES ON opencartv2.* TO opencartv2_migrations;
