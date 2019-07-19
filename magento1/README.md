# Introduction to Magento's Plugin Development

Magento realies heavily on Convention over Configuration. Due to this, we might feel most of the stuff here is overwhelming to us. But once we understand their flow, it will be easier.

## Basic Plugin Structure

Magento determines available plugins by iterating through xmls in `app/etc/modules`.
Here is `Indodana_Payment.xml`:
```
<?xml version="1.0"?>
<config>
    <modules>
        <Indodana_Payment>
            <active>true</active>
            <codePool>community</codePool>
            <depends>
                <Mage_Payment />
            </depends>
        </Indodana_Payment>
    </modules>
</config>
```
Reading the configuration above, we can guess that this files define a plugin called `Indodana_Payment` that depends on `Mage_Payment` and is currenctly active.

Now if we navigate to `app/code`. Notice that the folder `community` match the value specified in `codePool`. Basically, `codePool` define in which folder does a module reside, although the options itself is limited to:
* community (for 3rd party modules)
* local (for local private modules)
* core (core magento modules)

Inside `community` folder we can find our plugin residing in the folder `Indodana`. This is the namespace for our modules. Digging deeper down we'll find the folder `Payment`, this is our module name. Now if you notice, the name of our module configuration file is `Indodana_Payment.xml` while inside it we define the modules as `<Indodana_Payment>`. It is not coincidence that this value match the directory of our modules `Indodana/Payment`. This is one of convention that magento use and it will need a bit of time to get used to this.

Inside `Payment` folder we'll get five more folders
* Block
* controllers
* etc
* Helper
* Model

We'll asume that you know about `controllers`, `Helper`, and `Model` already. But the most important part for now is `etc`. Here we'll find more configuration for our plugins. There will be two xmls, `config.xml` and `system.xml`.

`config.xml` defines additional configuration for our modules, like Helper or Block file location, etc. Most of this should be self-explanatory but let's focus on this part
```
<routers>
    <indodanapayment>
        <use>standard</use>
        <args>
            <module>Indodana_Payment</module>
            <frontName>indodanapayment</frontName>
        </args>
    </indodanapayment>
</routers>
```
`frontName` define the endpoint of our modules. For example, if we put this modules in an ecommerce website that resides in `magento.indodana.com` and our `frontName` value is `indodanapayment` then our controllers can be accessed from this url `magento.indodana.com/indodanapayment`.

## File Naming

File naming in Magento is very strict, miss one letter and hell will break lose. But fear not there's just one rule that we need to remember. Always put your namespace and module name first, and use the combination of Pascal Case and Snake Case for the class name, okay that's two rules but we hope you got the point. So here an example from our `CheckoutController.php`

`Indodana_Payment_CheckoutController`

Tada!! Simple, right? Just follow this pattern

`{NAMESPACE}_{MODULE_NAME}_{PATH_TO_FILE_WITH_UNDERSCORE}`

And make sure `PATH_TO_FILE_WITH_UNDERSCORE` ends with `Controller` for your module's controller.

What about the rest of the modules? It still follows the above rules. Here we can see the class name for Helper/Data.php `Indodana_Payment_Helper_Data`. Here is for Model/Standard.php `Indodana_Payment_Model_Standard`. One last thing, the name of the model Standard.php is also a convention. So changing it will break the code, in the scope of our current project we will not need to add another model so it is fine.

## Controller's Workflow

Let's recall `frontName` from the previous section. Every request that comes to `baseUrl/frontName` will be handled by appropriate controllers for modules assosiated with the `frontName`. Okay that part might be confusing but here is an example.

Let's say our current module have `indodanapayment` as frontName. We also have a controllers called `CheckoutController.php`. Now this controller will catch all request that comes to `indodanapayment/checkout`. So the question is, what function will handle the request? If the request comes through `indodanapayment/checkout` then it will be directed to `indexAction` function in `CheckoutController`. Why index? because `indodanapayment/checkout` is the same as `indodanapayment/checkout/index`. The Action keyword appended on the back is just one of many Magento's convention. 

So what happens if a request comes to `indodanapayment/checkout/redirect`? Then the function that will handle it will be named redirectAction in CheckoutController.php.

## Plugin's Workflow

![alt Plugin workflow](docs/workflow.png)