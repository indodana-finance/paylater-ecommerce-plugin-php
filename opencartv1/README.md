# OpenCart Payment Plugin

This documentation will document the process of developing a plugins for OpenCart. By doing this we hope future developers won't get stuck on a mistake that previous developers make and development time become shorter.

## Installing Dependencies and Running
Assuming you've setup the db (mysql) for opencartv1.
Go to the root project of this repo, you'll find a [makefile](https://github.com/cermati/paylater-indodana-online-shop/blob/master/makefile) there,

```
# Install dependencies
make opencart-v1-install-dependencies

# Now you can run the server to start opencartv1
make opencart-v1-serve

# For debugging and see log, open another terminal and run
make opencart-v1-log-tail

# If you want to simulate payment then open opencartv1/plugin/catalog/controller/payment/indodana_checkout.php
# then follow instruction in getBackToStoreUrl(), getCancellationRedirectUrl(), and getNotificationUrl()
# -------------------------------
# If you want to simulate notify merchant request (after installment request is approved) just do this
curl -X POST http://localhost:8001?route=payment/indodana_checkout/notify --data '{"transactionStatus": "PAID", "merchantOrderId": <fill merchant order id here>}' -v
```

## Admin and Catalog

When you open the upload folder, you will notice two separete folders, admin and catalog.

* Admin folder dictates how the module interacts with the webmaster, including setting up the initial configuration. The rests are MVC, each plugins have its own controllers and view. The view will dictate what to show when the merchants press the edit button, and the controller controls the passage of data.

* Catalog folder dictates how the module interacts with customers. For payment plugin, the module will show up when customers checkout on an order.

## How to get Additional Information in OpenCart

Most of OpenCart information can be used after calling the `load` method. For example, when calling `$this->language->load` we need to supply the language's relative directory. After doing this, each consecusive `$this->language->get` call will get the value defined in language file.

For other informations like orderInfo, OpenCart gave us an API for each model. A model in OpenCart is a helper to SQL calls. A model can be loaded by using `$this->load->model`. This method will populate `$this` with a model object. For example by loading `$this->load->model('setting/setting')` we will have reference to `$this->model_setting_setting` object. Every functions or objects included in the model can be found in `admin/model` or `catalog/model`. You might notice that the arguments supplied when we load module is just the relative path to the model file from the model directory.

## Supplying Data to View

OpenCart handles how controllers interact with view. In short, every variable pushed to `$this->data` will be spawned in the view instance. So if we add `$this->data['count'] = 2;` we will be able to get the value of this variable by using `<?php $count; ?>` from view.

## How the Plugin Works

### Admin

For merchant's admin, we will render a form for plugin's configuration. This form will ask the merchant's API Key, Secrets, Sandbox/Porduction mode, and so on.

### Catalog

Due to Indodana Installment View not showing installment options when we first open the page. We decided to fetch installment options before customers finalize their purchase. There are two ways to do this, either:

* We use curl from PHP to get installment options and renders it to the view
* We use javascript

Currently we go for the first option, the main consideration being if we use Javascript, the view will be rendered blank for a short time, before installment options can be fetched. We determine that this is not a great User Experience. Also by going for the first options our javascript codes still looks nice and not bloated.

After fetching installment options, we need to post current transaction details to get the redirect's url. This url will be used to redirect our customers to Indodana checkout page. This can only be done using Javascript as we need the customer's payment options (by using radiobutton).

## TODO

* Handle incoming notification for installment approval
