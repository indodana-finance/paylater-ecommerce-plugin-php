# indodana paylater payment method module for magento 2.4.0 >= 2.3.x 
# Application Instalation
    1. Setup Database, create user and database for magento 
        ```sql
        mysql> CREATE DATABASE magento;
        mysql> CREATE USER 'magento' IDENTIFIED BY 'magento';
        mysql> GRANT ALL PRIVILEGES ON magento.* TO 'magento';
        ```
    2. Download magento from https://magento.com/tech-resources/download and extract 
    3. Set permission 
    3. Install magento, step by step installation can follow https://devdocs.magento.com/guides/v2.4/install-gde/install-quick-ref.html

# Application Instalation
# Magento Structure
    Magento Root -
                 |-app
                 |  |-code
                 |      |-Module Indodana
                 |-var
                    |-log
                        |-Indodana
# Plugin Installation 
    1. Download release version Plugin MagentoV2
        https://github.com/indodana/paylater-ecommerce-plugin-php/releases
    2. Extract 
    3. Copy plugin/app/ to <magento root>/app/
    4. Set folder Acces Permission 
        cd <magento_root>
        find var generated vendor pub/static pub/media app/etc -type f -exec chmod u+w {} +
        find var generated vendor pub/static pub/media app/etc -type d -exec chmod u+w {} +
        chmod u+x bin/magento
    5. Run <magento_root>$sudo bin/magento setup:upgrade
    6. Run <magento_root>$sudo bin/magento module:enable Indodana_PayLater
    7. Run <magento_root>$sudo bin/magento setup:upgrade
    8. Jika mode developer skip bagian ini ( coba skip dulu ) 
            Run <magento_root>$sudo bin/magento setup:di:compile
    9. Run <magento_root>$sudo bin/magento setup:static-content:deploy –f
    10. Pastikan apache/ngix punya hak write
        Atau jalankan 
        sudo chown -R ubuntu:www-data <magento_root>
    11. Set Currency = IDR
   

# Plugin Structure
    |-code
        |-Indodana
            |-Paylater
                |-Api
                |-Block
                |-Controller
                |-etc
                |-Gateway
                |-Helper
                |-i18n
                |-Model
                |-Observer
                |-view
for detail guidelines can see [magento-module-file-structure](https://devdocs.magento.com/guides/v2.4/extension-dev-guide/build/module-file-structure.html)



# Plugin workflow 
![alt Plugin workflow](docs/flow.PNG)


|-Controller/Index
    |-paymentoptions.php    => digunakan untuk proses mengambil data installment
    |-redirectto.php        => digunakan untuk proses checkout ke indodana payment
    |-cancel.php            => digunakan untk proses cancel cart

|-Api
    |-NotifyInterface.php   => Interface api untuk handle notification dari indodana
|-Model
    |-Api
        |-Notify.php        => Implementasi dari Notify Interface untuk handle notification dari indodana

more Information check [payment-gateway-intro](https://devdocs.magento.com/guides/v2.3/payments-integrations/payment-gateway/payment-gateway-intro.html) 
and [payment-gateway-structure](https://devdocs.magento.com/guides/v2.4/payments-integrations/payment-gateway/payment-gateway-structure.html)

