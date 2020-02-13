# Paylater Indodana Online Shop (PIOS)

This repository contains most used ecommerce platform based on PHP, such as:

* Woocommerce
* Opencart (v1, v2, v3)
* Magento (1, 2)
* Prestashop

Each platform was modified to fit into Cermati's DBCTL workflow. To simplify, due to DBCTL way of having dynamic credentials, each platform needs to synchronize the generated credentials to the coresponding PHP config file.

This is done by using NodeJS worker that will get a new credential 10 minutes before expiry time.

To simplify the process, we hardcode the deployment port, so make sure it doesn't clash with another application. Here is the mapping that we used, don't worry we tried to make the port as unique as possible.

| Service     | Version | PHP Version | Port |
| ----------- | ------- | ----------- | ---- |
| OpenCart v1 | 1.5.6.4 | 5.6         | 6011 |
| OpenCart v2 | 2.3.0.2 | 5.6         | 6012 |
| OpenCart v3 | 3.0.3.2 | 5.6         | 6013 |
| Woocommerce | 3.6.4   | 7.2         | 6021 |
| Magento 1   | 1.9.2.4 | 5.6         | 6031 |
| Prestashop  | 1.7.5.2 | 7.2         | 6041 |

# System Requirements

Due to framework dependence on PHP and its modules, you will need to install all of this dependency to continue. This can be done using this command. Also because each framework supports different PHP version we'll need to install two version of PHP (5.6 and 7.2) and install all the modules for each PHP versions.

1. Add repository to correctly install PHP requirements
```
$ sudo add-apt-repository universe
$ sudo add-apt-repository ppa:ondrej/php
$ sudo apt-get update
```

2. Installing PHP 7.2 & required PHP modules
```
$ sudo apt-get install \
    php7.2 \
    php7.2-bcmath \
    php7.2-common \
    php7.2-curl \
    php7.2-xml \
    php7.2-gd \
    php7.2-intl \
    php7.2-mbstring \
    php7.2-mysql \
    php7.2-soap \
    php7.2-xsl \
    php7.2-zip \
    php7.2-fpm
```
3. Installing PHP 5.6 & required PHP modules
```
$ sudo apt-get install \
    php5.6 \
    php5.6-bcmath \
    php5.6-common \
    php5.6-curl \
    php5.6-xml \
    php5.6-gd \
    php5.6-intl \
    php5.6-mbstring \
    php5.6-mysql \
    php5.6-soap \
    php5.6-xsl \
    php5.6-mcrypt \
    php5.6-zip \
    php5.6-fpm
```
4. Installing NGINX as our main application server. 
```
$ sudo apt-get install nginx
```

NGINX will run its php-file as another user, the default will be www-data. Currently there are no way to change this user for this framework. But we'll be developing it soon. Also we assume that the location of nginx configuration directory is here `/etc/nginx/conf.d`.

NGINX will also use the `php5.6-fpm` & `php7.2-fpm` to execute the PHP source code required by this services.

We will also need to setup MySQL server in our laptop. To install MySQL please type the following command:
```
$ sudo apt-get install mysql-server-5.7 mysql-client-5.7
```

# Development Requirements
## 1. Preparing PKICTL requirement

To prepare your laptop for PKICTL environment, you will need to set up Public Key Infrastructure (PKI) in your computer first. The guide to do this can be seen in this [document](https://github.com/cermati/getting-started/blob/master/docs/tutorials/setting-up-pki-certificates-for-development.md).

Once you have setup the environment, you will need to setup the PKICTL environment for the project. To do this, you will need to execute the following command:

Assuming `PROJ_DIR` as your `/path/to/online-shop/project`
```
$ cd $PROJ_DIR
$ cd cli
$ ./pkictl setup
$ ./pkictl vault unseal local local-vault
$ ./pkictl vault context login root-local
$ ./pkictl vault policy setup local local-vault
$ ./pkictl vault auth cert setup local local-vault
$ ./pkictl vault context configure
```

After that we will need to prepare a development certificate. To do this we need to turn on the PKI signing server. You will need to open 2 terminal console and type the following command respectively.

Assuming `PROJ_DIR` as your `/path/to/online-shop/project`
```
$ cd $PROJ_DIR
$ ./pkictl pki server setup
```

In the 1st terminal, please type:
```
$ cd $PROJ_DIR
$ sudo su --> change to ROOT
$ ./pkictl pki server serve private
```

In the 2nd terminal, please type:
```
$ cd $PROJ_DIR
$ sudo su
$ ./pkictl pki server serve public
```

Finally, we will need to generate the service certificate `pios-dev`.
```
$ cd $PROJ_DIR
$ ./pkictl service certs generate local pios-dev
```

## 2. Preparing DB requirement

We have leveraged DBCTL workflow to setup our database. The database setup would be different according to each service.

To prepare the project for DBCTL runtime, please type the following command:

Assuming `PROJ_DIR` as your `/path/to/online-shop/project`
```
$ cd $PROJ_DIR
$ ./dbctl setup
```

### opencartv1
To setup & prepare the database for `opencartv1` service (OpenCart v1.X), please type the following command:

Assuming `PROJ_DIR` as your `/path/to/online-shop/project`
```
$ cd $PROJ_DIR
$ ./dbctl mysql deploy opencartv1 dev
$ ./dbctl mysql role configure opencartv1 dev app
$ ./dbctl mysql migrate opencartv1 dev public
```

## 3. Running the service
Finally, after we have setup our PKI & DB requirement, we can start to run the service locally.

Assuming `PROJ_DIR` as your `/path/to/online-shop/project`

### opencartv1
To build `opencartv1` service in your local machine, from the `PROJ_DIR`, run this following command
```
$ cd $PROJ_DIR
$ cd cli
$ ./svctl build opencartv1 dev
$ ./svctl run opencartv1 dev
```
Some notes:
- The `./svctl build` will generate the required file located in: 
`$PROJ_DIR/.build/opencartv1/dev/`. 
- The `./svctl run` will preparing the runtime config for your local NGINX. Currently we are relying on convention, where the generated config will be put in `/etc/nginx/conf.d/<servicename>.conf`. For our `opencartv1` service, it will create : `/etc/nginx/conf.d/opencartv1.conf`.

To view logs of the service, you can `tail` the nginx log.
```
$ tail -200f /var/log/nginx.log
```
To view logs of the service worker (to synchronize credential from Vault), you can view the logs using the following command:

Assuming `PROJ_DIR` as your `/path/to/online-shop/project`
```
$ cd $PROJ_DIR
$ tail -200f ./.build/opencartv1/dev/logs/out.log
$ tail -200f ./.build/opencartv1/dev/logs/err.log
```

## 4. Deploying the service

# Operational Aspects

## Administrative Account
Congratulation you've reach this part, now to make life easier, we've set up some administrative accounts for you to try. Here it is:

| Service               | Username             | Password        |
| --------------------- |:--------------------:|:---------------:|
| Opencart, Woocommerce | admin                | admin           |
| Prestashop            | admin@prestashop.com | adminprestashop | 

## Plugin Development
Plugin Development will be covered on each services, see you there!

## Troubleshooting & FAQ

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
