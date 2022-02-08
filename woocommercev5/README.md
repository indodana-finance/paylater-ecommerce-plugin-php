# Indodana Paylater for Woocommerce V5

## Development Setup

1. Download & unzip Wordpress(>=5.4) and Woocommerce(>=5.0.0)

   > For development purposes, we will use [WordPress 5.6](https://wordpress.org/wordpress-5.4.zip) and [Woocommerce 5.0.0](https://downloads.wordpress.org/plugin/woocommerce.5.0.0.zip)

2. Copy all inside unzipped Wordpress folder to `woocommercev5/upload`

3. Copy `woocommerce` folder inside unzipped Woocommerce folder to `woocommercev5/upload/wp-content/plugins`

4. Create a database:

   ```sql
   mysql> CREATE DATABASE woocommercev5;
   
   mysql> CREATE USER 'woocommercev5' IDENTIFIED BY 'woocommercev5';
   
   mysql> GRANT ALL PRIVILEGES ON woocommercev5.* TO 'woocommercev5';
   ```

5. Install dependencies by running `make woocommercev5-install-dependencies`

6. Run the site using `make woocommercev5-serve`

7. Follow the WordPress installation. When prompted about database, use database information on step 4:

   - Database name: `woocommercev5`
   - Database username: `woocommercev5`
   - Database password: `woocommercev5`

8. Copy `wp-config.php` on the running site to `woocommerce/upload` by running:

   ```bash
   $ cp .build/woocommercev5/upload/wp-config.php woocommercev5/upload/wp-config.php
   ```
