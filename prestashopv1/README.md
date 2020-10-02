# Prestashop V1

## Table of Contents

[Getting Started](#getting-started)

- [Aplication Setup](#application-setup)

[Concepts](#concepts)

- [Module Structure](#module-structure)
- [Hooks](#hooks)
- [ObjectModel class](#objectmodel-class)
- [Template engine: Smarty](#template-engine-smarty)
- [Passing data to Smarty view](#passing-data-to-smarty-view)
- [Backward compatibility with PrestaShop 1.6](#backward-compatibility-with-prestashop-16)

[How the Module Works](#how-the-module-works)

- [Back Office](#back-office)
- [Front Office](#front-office)
- [Handling payment notification from Indodana](#handling-payment-notification-from-indodana)

---

## Getting Started

### Application Setup

1. Setup Database.

   ```sql
   mysql> CREATE DATABASE prestashopv1;

   mysql> CREATE USER 'prestashopv1' IDENTIFIED BY 'prestashopv1';

   mysql> GRANT ALL PRIVILEGES ON prestashopv1.* TO 'prestashopv1';
   ```

   > If you found this error:<br><br> > `ERROR 1819 (HY000): Your password does not satisfy the current policy requirements`<br><br>
   > See the solution [here](https://sysfiddler.com/mysql-error-1819-hy000-your-password-does-not-satisfy-the-current-policy-requirements/)

2. Run your Prestashop application.

   ```shell
   make prestashopv1-install-dependencies

   make prestashopv1-serve
   ```

   > Make sure `composer` is already installed on your computer

3. Go to `http://localhost:6041`

   > When opening url above for the first time, Prestashop will take a few moments to set up the Prestashop Installation Assistant

4. Follow the Prestashop Installation Assistant until "Store Information" page, see next step.

5. On 'Store Information' page set following values:

   - Country = `Indonesia`
   - Shop timezone = `Asia/Jakarta`
   - Your admin account

6. On 'System Configuration' page set following values:

   - Database name = `prestashopv1`
   - Database login = `prestashopv1`
   - Database password = `prestashopv1`

7. Plese wait for the installation process to complete. It will take a few minutes.

8. After the installation process is complete, delete `/install` folder.

9. Open Back Office, login with admin account that you entered earlier.

10. Make sure to enable debug mode and disable cache during development on `Advance Parameters` page.

11. Install Indodana payment module on `Modules > Module Catalog` and configure it.

---

## Concepts

A PrestaShop plugin (or in Prestahop, plugin is called **module**) consists of a main PHP file with as many other PHP files as needed, as well as the necessary template (.tpl) files and assets (images, JavaScript, CSS, etc.) to display the module’s interface, whether to the customer (on the front office) or to the merchant (on the back office).

For more information you can visit [Prestashop official documentation site](https://devdocs.prestashop.com/1.7/basics/introduction/).

### Module Structure

```
📦indodana
 ┣ 📂controllers
 ┣ 📂log
 ┣ 📂tools
 ┣ 📂translations
 ┣ 📂upgrade
 ┣ 📂vendor
 ┣ 📂views
 ┣ 📜autoload.php
 ┣ 📜config.xml
 ┣ 📜index.php
 ┣ 📜indodana.php
 ┗ 📜logo.png
```

- Main file: `indodana.php`

  The main PHP file should have the same name as the module’s root folder. For instance, for the indodana module:

  - Folder name: `/modules/indodana`
  - Main file name: `/modules/indodana/indodana.php`

- Icon file: `logo.png`

- Templating: the `/views` folder

  This folder contains your module’s template files (.tpl or .html.twig files).

  Depending on your needs, your files are located in differents subfolders:

  - `/views/templates/admin`: For template files used by the module’s administration legacy controllers.
  - `/views/templates/front`: For template files used by the module’s front office controllers.
  - `/views/templates/hook`: For template files used by the module’s hooks.

- Make actions and pages: the `/controllers` folder

  This folder contains the Controller files. You can use the same sub-folder paths as for the View files.

- Manage the upgrade: the `/upgrade` folder

  When releasing a new version of the module, the older might need an upgrade of its data or files. This can be done using this folder.

- Cache file: `config.xml`

  It contains some properties on the main module class and optimizes the loading of the module list in the back office.

- Log: the `/log` folder

  This folder contains log files used by Indodana ecommerce plugin.

- Helper: the `/tools` folder

  This folder contains helper file used to get required parameter data for calling indodana API.

- Dependencies: the `/vendor` folder

  This folder contains the dependencies used by this module.

- Autoload file: `autoload.php`

  It contains composer autoload and constants that used by indodana.

### Hooks

Hooks are a way to associate your code to some specific PrestaShop events.

Most of the time, they are used to insert content in a page. The place it will be added (header, footer, left or right column …) will depend on the hook you choose.

Hooks can also be used to perform specific actions under certain circumstances (i.e. sending an e-mail to the client on an order creation).

In this module, we use multiple hooks to handle payment:

- `hookPaymentOptions()` : for displaying payment method on v1.7.x
- `hookPaymentReturn()` : for handle payment return
- `hookDisplayPayment()` : for displaying payment method on v1.6.x
- `hookDisplayPaymentReturn()` : for displaying order confirmation page

### ObjectModel class

When you need to fetch data from database, you have to use the ObjectModel class. This is the main object of PrestaShop’s object model. For example:

```
new Order((int) $orderId);
```

Code above is used to fetch order details data.

### Template engine: Smarty

PrestaShop uses Smarty to handle page view.

You can learn more by going to [Smarty official website](http://www.smarty.net/)

### Passing data to Smarty view

Smarty handles of how the controller interacts with view. In short, any variables assigned to smarty, it will be accessible on view. For example:

```
$this->smarty->assign([
  'moduleName' => 'indodana'
]);
```

We will be able to access the value of `moduleName` variable by using smarty syntax
`{$moduleName}`

### Backward compatibility with PrestaShop 1.6

PrestaShop 1.6 does not fully support namespaces. They throw some issues when used in specific places.

- In the main class of your module, the keyword `use [...];` will trigger syntax errors when PrestaShop will try to parse the file.
- ObjectModels can’t be defined in a namespace. The hook generated while managing this entity will be considered as invalid by the Validate class and will trigger fatal errors.

---

## How the Module Works

### Back Office

For merchant's admin, we will render a form for module's configuration. This form will ask the merchant's API Key, Secrets, Sandbox/Porduction mode, and so on.

### Front Office

On the checkout page, when user will chooses a payment method. We call Indodana API to fetch installment options data then displaying it as radio form.

Once user chooses Indodana payment method and submits the order, we need to post curent transaction details to fetch redirect URL to Indodana payment page.

After user has successfully made a payment, user will be redirected back to the Prestashop order detail page.

### Handling payment notification from Indodana

Indodana will send a notification once the user has successfully made a payment, we will handle the notification to update the order status to 'Payment Accepted' (or according to module configuration `DEFAULT_ORDER_SUCCESS_STATUS`) and display JSON response:

```
{
  "status":"OK",
  "message":"OK"
}
```
