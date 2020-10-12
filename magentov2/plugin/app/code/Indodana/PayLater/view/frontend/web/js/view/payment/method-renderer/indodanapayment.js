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
                window.checkoutConfig.payment.indodanapayment.paytype=item.id;
                window.checkoutConfig.payment.indodanapayment.transactionResults='Success';
                
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

                            window.checkoutConfig.payment.indodanapayment.installment=data.Installment;
                            window.checkoutConfig.payment.indodanapayment.OrderID=data.OrderID;
                            window.checkoutConfig.payment.indodanapayment.PassMinAmount=data.PassMinAmount;
                            window.checkoutConfig.payment.indodanapayment.PassMaxItemPrice=data.PassMaxItemPrice;  
                            window.checkoutConfig.payment.indodanapayment.ErrMsg=data.ErrMsg;
                            $('#dvmsgIndodana').hide();
                            if (data.IsError == true){
                                $('#dvmsgIndodana').show();
                                $("#indodanapayment").prop('disabled', true);
                                $("#lblErrMsgIndodana").html(data.ErrMsg);
                            } else{
                                window.checkoutConfig.payment.indodanapayment.installment.forEach(function (d){
                                    d.monthlyInstallment=data.CurCode +' '+  d.monthlyInstallment.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
                                });    
                                
                                if(window.checkoutConfig.payment.indodanapayment.PassMinAmount==false){
                                    $('#dvmsgIndodana').show();
                                    $("#indodanapayment").prop('disabled', true);
                                    $("#lblErrMsgIndodana").html(data.ErrMsg);
                                }
                                if(window.checkoutConfig.payment.indodanapayment.PassMaxItemPrice==false){
                                    $('#dvmsgIndodana').show();
                                    $("#indodanapayment").prop('disabled', true);
                                    $("#lblErrMsgIndodana").html(data.ErrMsg);
                                }                
    
                            }
                        },
                        //dataType: dataType
                    });
                }
                return window.checkoutConfig.payment.indodanapayment.installment;                
            },
            beforeselectPaymentMethod : function(){
                if(window.checkoutConfig.payment.indodanapayment.PassMinAmount==false){
                    $('#dvmsgIndodana').show();
                    $("#indodanapayment").prop('disabled', true);
                    $("#lblErrMsgIndodana").html(window.checkoutConfig.payment.indodanapayment.ErrMsg);
                    return false;
                }
                if(window.checkoutConfig.payment.indodanapayment.PassMaxItemPrice==false){
                    $('#dvmsgIndodana').show();
                    $("#indodanapayment").prop('disabled', true);
                    $("#lblErrMsgIndodana").html(window.checkoutConfig.payment.indodanapayment.ErrMsg);
                    return false;
                }                
                return this.selectPaymentMethod();
            },
            beforePlaceOrder:function(data, event){
                if(window.checkoutConfig.payment.indodanapayment.PassMinAmount==false){
                    $('#dvmsgIndodana').show();
                    $("#indodanapayment").prop('disabled', true);
                    return false;
                }
                if(window.checkoutConfig.payment.indodanapayment.PassMaxItemPrice==false){
                    $('#dvmsgIndodana').show();
                    $("#indodanapayment").prop('disabled', true);
                    return false;
                }                

                if(window.checkoutConfig.payment.indodanapayment.paytype==''){
                    alert('Silahkan pilih tenor cicilan');
                    return false;
                }

                  return this.placeOrder(data,event);
            }

            ,afterPlaceOrder:function(){
                
                var ptype=window.checkoutConfig.payment.indodanapayment.paytype;
                this.redirectAfterPlaceOrder = false;
                var strurl =url.build('indodanapayment/index/redirectto')

                $.ajax({
                    //async:true,
                    type: "POST",
                    url: strurl,
                    data: {paytype:ptype},
                    success: function(data){                        
                        window.checkoutConfig.payment.indodanapayment.redirecturl=data.Order;
                        window.location.replace(window.checkoutConfig.payment.indodanapayment.redirecturl);
                    },
                  });
                  return true;
            }
        });
    }
);