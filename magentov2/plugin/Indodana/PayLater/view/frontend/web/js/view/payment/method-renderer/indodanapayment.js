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
                selectedInstallment:''
            },

            initObservable: function () {

                this._super()
                    .observe([
                        'transactionResult',
                        'paytype',
                        'installment'
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
                        'installment': this.installment()
                    }
                };
            },

            // getTransactionResults: function() {
            //     return _.map(window.checkoutConfig.payment.indodanapayment.transactionResults, function(value, key) {
            //         return {
            //             'value': key,
            //             'transaction_result': value
            //         }
            //     });
            // },
            
            onInstallmentClick: function(item){
                //alert(item.id);
                
                window.checkoutConfig.payment.indodanapayment.paytype=item.id;
                //self.isInstallmentSelected(true);
                return true;
            },            
            // getIsInstallmentSelected:function(){
            //       return this.isInstallmentSelected();  
            // },
            
    

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
                            //return data.Installment;        
                        },
                        //dataType: dataType
                    });
                }
                return window.checkoutConfig.payment.indodanapayment.installment;
            },
            afterPlaceOrder:function(){
                //alert(window.checkoutConfig.payment.indodanapayment.paytype);
                var ptype=window.checkoutConfig.payment.indodanapayment.paytype;
                this.redirectAfterPlaceOrder = false;
                var strurl =url.build('indodanapayment/index/redirectto')
                $.ajax({
                    type: "POST",
                    url: strurl,
                    data: {paytype:ptype},
                    success: function(data){
                        //alert (JSON.stringify(data) );
                        //alert (data.Order);
                        window.location.replace(data.Order);
                        //return data.Installment;        
                    },
                    //dataType: dataType
                  });
                  return true;

                
                //window.location=strurl;
            }

        });
    }
);