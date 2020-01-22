
### Create database user
```sql
CREATE USER 'woocommerce' IDENTIFIED BY 'woocommerce';
CREATE DATABASE woocommerce;
GRANT ALL PRIVILEGES ON *.* TO woocommerce;
```
