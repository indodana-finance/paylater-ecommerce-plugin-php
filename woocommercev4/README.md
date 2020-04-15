# Indodana Paylater for Woocommerce V4

## Development Setup

1. Download & unzip Wordpress(>=5.3.0) and Woocommerce(>=4.0.0)

   > For development purposes, we will use [WordPress 5.3.1](https://github.com/WordPress/WordPress/archive/5.3.1.zip) and [Woocommerce 4.0.0](https://github.com/woocommerce/woocommerce/releases/download/4.0.0/woocommerce.4.0.0.zip)

2. Copy all inside unzipped Wordpress folder to `woocommercev4/upload`

3. Copy `woocommerce` folder inside unzipped Woocommerce folder to `woocommercev4/upload/wp-content/plugins`

4. Create a database:

   ```sql
   mysql> CREATE DATABASE woocommercev4;
   
   mysql> CREATE USER 'woocommercev4' IDENTIFIED BY 'woocommercev4';
   
   mysql> GRANT ALL PRIVILEGES ON woocommercev4.* TO 'woocommercev4';
   ```

5. Run the site using `make woocommerce-serve`

6. Follow the WordPress installation. When prompted about database, use database information on step 4:

   - Database name: `woocommercev4`
   - Database username: `woocommercev4`
   - Database password: `woocommercev4`

7. Copy `wp-config.php` on the running site to `woocommerce/upload` by running:

   ```bash
   $ cp .build/woocommercev4/upload/wp-config.php woocommercev4/upload/wp-config.php
   ```
