/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'Magento_Checkout/js/view/payment/default',
        'mage/url',
        'Magento_Checkout/js/model/url-builder',
        'jquery'
    ],
    function (Component, url,urlBuilder,$) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Indodana_PayLater/payment/form',
                transactionResult: '',
                paytype:''
            },

            initObservable: function () {

                this._super()
                    .observe([
                        'transactionResult',
                        'paytype'
                    ]);
                return this;
            },

            getCode: function() {
                return 'indodanapayment';
            },

            getData: function() {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'transaction_result': this.transactionResult()
                    }
                };
            },

            getTransactionResults: function() {
                return _.map(window.checkoutConfig.payment.sample_gateway.transactionResults, function(value, key) {
                    return {
                        'value': key,
                        'transaction_result': value
                    }
                });
            },
            getPaymentOptions:function(){
                $.ajax({
                    type: "POST",
                    url: url.build('IndodanaPayment/index/paymentoptions'),
                    //data: data,
                    success: function(data){
                        return data.Installment;        
                    },
                    //dataType: dataType
                  });
            },
            onInstallmentClick:function(paytype){
                    alert(paytype);
                    this.paytype=paytype;
            },
            afterPlaceOrder:function(){
                alert(this.paytype);
                this.redirectAfterPlaceOrder = false;
                var strurl =url.build('IndodanaPayment/index/redirectto')
                $.ajax({
                    type: "POST",
                    url: strurl,
                    data: {paytype:'30_days'},
                    success: function(data){
                        alert (data.redirectUrl);
                        window.location.replace(data.redirectUrl);
                        //return data.Installment;        
                    },
                    //dataType: dataType
                  });


                
                //window.location=strurl;
            }

        });
    }
);