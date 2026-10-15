# Indodana Paylater for Woocommerce V11

## Development Setup

1. Download & unzip Wordpress(>=6.8) and Woocommerce(>=11.2.0)

   > For development purposes, we will use [WordPress 6.8](https://wordpress.org/wordpress-6.8.zip) and [Woocommerce 11.2.0](https://downloads.wordpress.org/plugin/woocommerce.11.2.0.zip)

2. Copy all inside unzipped Wordpress folder to `woocommercev11/upload`

3. Copy `woocommerce` folder inside unzipped Woocommerce folder to `woocommercev11/upload/wp-content/plugins`

4. Create a database:

   ```sql
   mysql> CREATE DATABASE woocommercev11;
   
   mysql> CREATE USER 'woocommercev11' IDENTIFIED BY 'woocommercev11';
   
   mysql> GRANT ALL PRIVILEGES ON woocommercev11.* TO 'woocommercev11';
   ```

5. Install dependencies by running `make woocommercev11-install-dependencies`

6. Run the site using `make woocommercev11-serve`

7. Follow the WordPress installation. When prompted about database, use database information on step 4:

   - Database name: `woocommercev11`
   - Database username: `woocommercev11`
   - Database password: `woocommercev11`

8. Copy `wp-config.php` on the running site to `woocommercev11/upload` by running:

   ```bash
   $ cp .build/woocommercev11/upload/wp-config.php woocommercev11/upload/wp-config.php
   ```
