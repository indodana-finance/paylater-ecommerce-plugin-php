{*
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2020 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<style>
  .display-none {
    display: none !important;
  }
</style>

<script>
$(function () {
  var selectEnv = $('select[name="INDODANA_ENVIRONMENT"]');
  toggleEnv(selectEnv.val());

  selectEnv.change(function () {
    toggleEnv($(this).val());
  });

  // show/hide API key and API secret input fields according to environment
  function toggleEnv(value) {
    var sandboxApiKey = $('input[name="INDODANA_API_KEY"]');
    var sandboxApiSecret = $('input[name="INDODANA_API_SECRET"]');

    var productionApiKey = $('input[name="INDODANA_API_KEY_PRODUCTION"]');
    var productionApiSecret = $('input[name="INDODANA_API_SECRET_PRODUCTION"]');

    var displayNone = 'display-none';

    if (value === 'PRODUCTION') {
      // hide sandbox
      sandboxApiKey.parents('.form-group').addClass(displayNone);
      sandboxApiSecret.parents('.form-group').addClass(displayNone);

      // show production
      productionApiKey.parents('.form-group').removeClass(displayNone);
      productionApiSecret.parents('.form-group').removeClass(displayNone);
    } else {
      // show sandbox
      sandboxApiKey.parents('.form-group').removeClass(displayNone);
      sandboxApiSecret.parents('.form-group').removeClass(displayNone);

      // hide production
      productionApiKey.parents('.form-group').addClass(displayNone);
      productionApiSecret.parents('.form-group').addClass(displayNone);
    }
  }
});
</script>
