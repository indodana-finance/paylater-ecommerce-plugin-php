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

require_once(_PS_MODULE_DIR_ . 'indodana' . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'IndodanaTools.php');

class IndodanaApproveModuleFrontController extends ModuleFrontController
{
  /**
   * Handle payment notification from Indodana
   */
  public function postProcess()
  {
    // Log request headers
    $namespace = '[PrestashopV1-notify]';

    $requestHeaders = IndodanaCommon\IndodanaHelper::getRequestHeaders();

    IndodanaCommon\IndodanaLogger::info(
      sprintf(
        '%s Request headers: %s',
        $namespace,
        json_encode($requestHeaders)
      )
    );

    // Check whether request authorization is valid
    $authToken = IndodanaCommon\IndodanaHelper::getAuthToken($requestHeaders, $namespace);

    $indodanaTools = new IndodanaTools();
    $isValidAuthorization = $indodanaTools->getIndodanaCommon()->isValidAuthToken($authToken);

    if (!$isValidAuthorization) {
      IndodanaCommon\MerchantResponse::printInvalidRequestAuthResponse($namespace);

      die;;
    }

    // Log request body
    $requestBody = IndodanaCommon\IndodanaHelper::getRequestBody();

    IndodanaCommon\IndodanaLogger::info(
      sprintf(
        '%s Request body: %s',
        $namespace,
        json_encode($requestBody)
      )
    );

    // Check whether request body is valid
    if (!isset($requestBody['transactionStatus']) || !isset($requestBody['merchantOrderId'])) {
      IndodanaCommon\MerchantResponse::printInvalidRequestBodyResponse($namespace);

      die;
    }

    $transactionStatus = $requestBody['transactionStatus'];
    $orderId = $requestBody['merchantOrderId'];

    $order = new Order($_GET['id_order']);

    if (!$order) {
      IndodanaCommon\MerchantResponse::printNotFoundOrderResponse(
        $orderId,
        $namespace
      );

      die;
    }

    if (!in_array($transactionStatus, IndodanaCommon\IndodanaConstant::getSuccessTransactionStatuses())) {
      IndodanaCommon\MerchantResponse::printInvalidTransactionStatusResponse(
        $transactionStatus,
        $orderId,
        $namespace
      );

      die;
    }

    // Handle success order
    $order->setCurrentState(Configuration::get('INDODANA_DEFAULT_ORDER_SUCCESS_STATUS'));

    IndodanaCommon\MerchantResponse::printSuccessResponse($namespace);

    die;
  }
}
