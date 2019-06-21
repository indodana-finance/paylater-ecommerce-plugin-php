# Paylater Indodana Online Shop (PIOS)

This repository contains sevent most used ecommerce platform based on PHP, such as:

* Woocommerce
* Opencart (v1, v2, v3)
* Magento (1, 2)
* Prestashop

Each platform was modified to fit into Cermati's DBCTL workflow. To simplify, due to DBCTL way of having dynamic credentials, each platform needs to synchronize the generated credentials to the coresponding PHP config file.

This is done by using NodeJS worker that will get a new credential 10 minutes before expiry time.

To simplify the process, we hardcode the deployment port, so make sure it doesn't clash with another application. Here is the mapping that we used, don't worry we tried to make the port as unique as possible.

| Service     | Version | PHP Version | Port |
| ----------  |:-------:|:-----------:| ---- |
| OpenCart    | 1.5.6.4 | 5.6         | 6101 |
| OpenCart    | 2.3.0.2 | 5.6         | 6102 |
| OpenCart    | 3.0.3.2 | 5.6         | 6103 |
| Prestashop  | 1.7.5.2 | 7.2         | 6104 |
| Woocommerce | 3.6.4   | 7.2         | 6105 |

## Requirements

Due to framework dependence on PHP and its modules, you will need to install all of this dependency to continue. This can be done using this command. Also because each framework supports different PHP version we'll need to install two version of PHP (5.6 and 7.2) and install all the modules for each PHP versions.

To install PHP and its modules we need to add the repository first
```
sudo add-apt-repository universe
```

For PHP 7.2
```
sudo apt-get install php7.2 php7.2-bcmath php7.2-common php7.2-curl php7.2-xml php7.2-gd php7.2-intl php7.2-mbstring php7.2-mysql php7.2-soap php7.2-xsl php7.2-zip
```

For PHP 5.6
```
sudo apt-get install php5.6 php5.6-bcmath php5.6-common php5.6-curl php5.6-xml php5.6-gd php5.6-intl php5.6-mbstring php5.6-mysql php5.6-soap php5.6-xsl php5.6-zip
```

To serve PHP file, we'll use NGINX. So make sure to install it by using this command. 
```
sudo apt-get install nginx
```

NGINX run its php-file as another user, the default will be www-data. Currently there are no way to change this user for this framework. But we'll be developing it soon. Also we assume that the location of nginx configuration directory is here
```
/etc/nginx/conf.d
```

We'll also need PHP-FPM as the Fast PHP CGI to NGINX.
```
sudo apt install php5.6-fpm php7.1-fpm
```

## Installation and Migration
If you haven't use any ctl program, run this command to download the necessary files
```
./dbctl setup
```

To deploy and migrate database, you will need to use DBCTL. To use DBCTL in your local environment, you will need to set up Public Key Infrastructure (PKI) in your computer first. The guide to do this can be seen in this [document](https://github.com/cermati/getting-started/blob/master/docs/tutorials/setting-up-pki-certificates-for-development.md).

After completing the above commands, we'll be deploying our database using DBCTL. To do this run this command
```
./dbctl mysql deploy db_name dev
```
We set up db_name to be the same as service_name, so if you want to deploy database for opencartv3 you need to subtitute db_name with opencartv3.

The deployment process will only create database instance and root and migrations user. To migrate the data in public we will need to run this command
```
./dbctl mysql migrate db_name dev public
```

For security, we'll separate db user that do migrations, root and db user that is used by the apps. The default configuration (app) exist already, we just need to use it by using this command
```
./dbctl mysql role configure db_name dev app
```
Done!! Now our database is ready and we can move forward to the real stuff.

## Build and Running

To build a service to your local machine, from the root project, run this following command
```
cd cli && ./svctl build service_name dev
```

Actually, after each build the PHP file will already be serve by NGINX, but trying to access one will lead to an error. This is caused by the current database credentials is wrong and need to be synchronized first. To do this run the following command from your root project
```
cd cli && ./svctl run service_name dev
```

## Administrative Account
Congratulation you've reach this part, now to make life easier, we've set up some administrative accounts for you to try. Here it is:
| Service               | Username             | Password        |
| --------------------- |:--------------------:|:---------------:|
| Opencart, Woocommerce | admin                | admin           |
| Prestashop            | admin@prestashop.com | adminprestashop | 

## Plugin Development
Plugin Development will be covered on each services, see you there!

## Troubleshoots
### Vault Login Process Related
```
[ERROR] Vault login process using certificates failed. No PKICTL_CONTEXT_TOKEN for context cermati-indodana-testenv-dev retrieved. Please make sure you have configured the context properly.
```
The error was caused by vault still at sealed state when PKICTL ran. If this happens in production, then contact Infrateam as the vault mas managed remotely. If this happens in your development environemnt, we need to unseal your vault. To fix this error, we just need to run this simple command:
```
./pkictl vault unseal local local-vault
```

### Command Not Found
```
line 74: $'\E[0;31m[ERROR]\E[0m': command not found
```
This error is very uninformative, my bad :(
We'll improve the error on the next release.
 
    
It was caused by your environment not having GITHUB_USERNAME as key. To fix this, just add your GITHUB_USERNAME to current shell process by using this command:
```
export GITHUB_USERNAME=your_github_username
```
### Permission Denied
```
==> v11-read - for Vault > 1.1.0
Error reading v1.1/cermati/indodana/db/hosted/mysql/testenv/testenv/dev/creds/app: Error making API request.

URL: GET https://localhost:8200/v1/v1.1/cermati/indodana/db/hosted/mysql/testenv/testenv/dev/creds/app
Code: 403. Errors:

* permission denied
```
This error is documented, but we are still not sure of the cause. The usual way to fix this problem is to run DBCTL manually or just wait for some time.
```
./dbctl mysql role get-credential service_name dev app
```

### Error Related to Ecommerce Platform
Error related to ecommerce platform will be documented on each service. To see what the error is, you can access the log file of NGINX located here
```
/var/log/nginx/error.log
```
