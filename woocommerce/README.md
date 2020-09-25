# Woocommerce

## Getting Started

### Application Setup

1. Setup Database

    ```sql
    mysql> CREATE DATABASE woocommerce;

    mysql> CREATE USER 'woocommerce' IDENTIFIED BY 'woocommerce';

    mysql> GRANT ALL PRIVILEGES ON woocommerce.* TO 'woocommerce';
    ```

2. Run your Woocommerce application

    ```shell
    $ make magento1-install-dependencies

    $ make magento1-serve
    ```

3. Go to `http://localhost:6021/`

4. Setup WordPress. Just follow the installation process, it's self-explanatory

    > After this, it will automatically migrates SQL schema to `woocommerce` database that you just made. Your Woocommerce admin account is also being setup based on your input

5. Enable Woocommerce and Indodana Payment plugin on WordPress on `Plugin > Installed Plugins > Activate`

6. `Run the Setup Wizard` for Woocommerce. These are the things that are required for minimal setup:
- Store information: address and accepted currency
- Shipping information: weight, dimension and rate
- Products to be sold

7. Setup Indodana Payment plugin:
- Go to `Woocommerce > Settings > Payments`
- `Enable` Indodana Payment. Then `setup` to configure the properties
