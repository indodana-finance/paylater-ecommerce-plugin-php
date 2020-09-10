/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'ko',
        'Magento_Checkout/js/view/payment/default',
        'mage/url',
        'Magento_Checkout/js/model/url-builder',
        'jquery'
    ],
    function (ko,Component, url,urlBuilder,$) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Indodana_PayLater/payment/form',
                transactionResult: '',
                paytype:'',
                installment:'',
                redirecturl:''
            },

            initObservable: function () {

                this._super()
                    .observe([
                        'transactionResult',
                        'paytype',
                        'installment',
                        'redirecturl'
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
                        'transaction_result': this.transactionResult(),                        
                        'paytype': this.paytype(),
                        'installment':this.installment(),
                        'redirecturl':this.redirecturl()
                    }
                };
            },
            getLogoUrl: function() {
                return window.checkoutConfig.payment.indodanapayment.logo;
            },

            getTransactionResults: function() {
                return _.map(window.checkoutConfig.payment.indodanapayment.transactionResults, function(value, key) {
                    return {
                        'value': key,
                        'transaction_result': value
                    }
                });
            },
            
            onInstallmentClick: function(item){
                //alert(item.id);
                window.checkoutConfig.payment.indodanapayment.paytype=item.id;
                window.checkoutConfig.payment.indodanapayment.transactionResults='Success';
                //self.isInstallmentSelected(true);
                return true;
            },            
            
    

            getPaymentOptions:function(){
                if (window.checkoutConfig.payment.indodanapayment.installment==''){                
                    $.ajax({
                        async:false,
                        type: "POST",
                        url: url.build('indodanapayment/index/paymentoptions'),
                        //data: data,
                        success: function(data){                        

                            //alert(data.Installment);
                            window.checkoutConfig.payment.indodanapayment.installment=data.Installment;
                            window.checkoutConfig.payment.indodanapayment.OrderID=data.OrderID;
                            //window.checkoutConfig.payment.indodanapayment.installment='1';
                            //return data.Installment;        
                        },
                        //dataType: dataType
                    });
                }
                return window.checkoutConfig.payment.indodanapayment.installment;                
            },
            beforePlaceOrder:function(data, event){
                //alert(window.checkoutConfig.payment.indodanapayment.paytype);
                //alert(window.checkoutConfig.payment.indodanapayment.OrderID);
                if(window.checkoutConfig.payment.indodanapayment.paytype==''){
                    alert('Silahkan pilih ternor cicilan');
                    return false;
                }
                  return this.placeOrder(data,event);
            }

            ,afterPlaceOrder:function(){
                this.redirectAfterPlaceOrder = false;
                var ptype=window.checkoutConfig.payment.indodanapayment.paytype;
                this.redirectAfterPlaceOrder = false;
                var strurl =url.build('indodanapayment/index/redirectto')
                $.ajax({
                    type: "POST",
                    url: strurl,
                    data: {paytype:ptype},
                    success: function(data){
                        //alert (JSON.stringify(data) );
                        window.checkoutConfig.payment.indodanapayment.redirecturl=data.Order;
                        window.location.replace(window.checkoutConfig.payment.indodanapayment.redirecturl);
                    },
                    //dataType: dataType
                  });

                
                //window.location=strurl;

            }

        });
    }
);