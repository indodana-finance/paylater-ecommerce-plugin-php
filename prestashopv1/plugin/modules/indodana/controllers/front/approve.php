<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

/**
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
 */

class IndodanaApproveModuleFrontController extends ModuleFrontController
{
  /**
   * This class should be use by your Instant Payment
   * Notification system to validate the order remotely
   */
  public function postProcess()
  {
    $params = json_decode(file_get_contents('php://input'));
    $order = new Order($_GET['id_order']);
    $pending = Configuration::get('INDODANA_DEFAULT_ORDER_PENDING_STATUS');
    if ($params->transactionStatus == 'PAID' && $order->current_state == $pending) {
      $success = Configuration::get('INDODANA_DEFAULT_ORDER_SUCCESS_STATUS');
      $order->setCurrentState($success);
      $status = 'OK';
    } else {
      $status = 'REJECT';
    }

    die(Tools::jsonEncode([
      'status' => $status,
      'message' => ''
    ]));
  }
}
