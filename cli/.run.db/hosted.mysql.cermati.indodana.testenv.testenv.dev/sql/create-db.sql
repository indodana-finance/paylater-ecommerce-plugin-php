CREATE DATABASE testenv;
USE testenv
CREATE USER testenv_root;
ALTER USER testenv_root IDENTIFIED BY 'ANf9p6hudSKbixaAbzBRdFwKb8fxp5w3';
GRANT ALL ON testenv.* TO testenv_root;
GRANT ALL PRIVILEGES ON testenv.* TO testenv_root;
GRANT INSERT, UPDATE ON mysql.* TO testenv_root;
GRANT GRANT OPTION ON testenv.* TO testenv_root;

-- Provision Migration User
CREATE USER testenv_migrations;
GRANT ALL ON testenv.* TO testenv_migrations;
GRANT ALL PRIVILEGES ON testenv.* TO testenv_migrations;
