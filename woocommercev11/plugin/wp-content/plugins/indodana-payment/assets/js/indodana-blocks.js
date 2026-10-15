/**
 * Registers Indodana as a payment method for the WooCommerce Cart & Checkout blocks.
 *
 * Plain script, no build step: relies on the `wc-blocks-registry`, `wc-settings`,
 * `wp-element`, `wp-html-entities` and `wp-i18n` handles registered in
 * `WC_Indodana_Blocks::get_payment_method_script_handles()`.
 */
( function( wp, wc ) {
  var el = wp.element.createElement;
  var useState = wp.element.useState;
  var decodeEntities = wp.htmlEntities.decodeEntities;
  var __ = wp.i18n.__;

  var settings = wc.wcSettings.getSetting( 'indodana_data', {} );

  var label = decodeEntities( settings.title || __( 'Indodana PayLater', 'indodana-blocks' ) );

  var Content = function( props ) {
    var emitResponse = props.emitResponse;
    var eventRegistration = props.eventRegistration;
    var installmentOptions = settings.installmentOptions || [];
    var stateSelection = useState( '' );
    var selected = stateSelection[ 0 ];
    var setSelected = stateSelection[ 1 ];

    wp.element.useEffect( function() {
      var unsubscribe = eventRegistration.onPaymentSetup( function() {
        if ( ! selected ) {
          return {
            type: emitResponse.responseTypes.ERROR,
            message: __( 'Please select an Indodana installment option before continuing.', 'indodana-blocks' ),
          };
        }

        return {
          type: emitResponse.responseTypes.SUCCESS,
          meta: {
            paymentMethodData: {
              payment_selection: selected,
            },
          },
        };
      } );

      return unsubscribe;
    }, [ selected, emitResponse, eventRegistration ] );

    if ( installmentOptions.length === 0 ) {
      return el(
        'div',
        { className: 'wc-indodana-blocks-content' },
        __( 'Indodana installment options are currently unavailable. Please choose another payment method.', 'indodana-blocks' )
      );
    }

    return el(
      'div',
      { className: 'wc-indodana-blocks-content' },
      settings.description ? el( 'p', {}, decodeEntities( settings.description ) ) : null,
      installmentOptions.map( function( option ) {
        return el(
          'label',
          { key: option.id, className: 'wc-indodana-blocks-option' },
          el( 'input', {
            type: 'radio',
            name: 'wc-indodana-payment-selection',
            value: option.id,
            checked: selected === option.id,
            onChange: function() {
              setSelected( option.id );
            },
          } ),
          el( 'span', {}, option.paymentType ),
          el( 'span', {}, option.monthlyInstallment + '/bulan' )
        );
      } )
    );
  };

  var Edit = function() {
    return el( 'div', {}, __( 'Indodana PayLater', 'indodana-blocks' ) );
  };

  wc.wcBlocksRegistry.registerPaymentMethod( {
    name: 'indodana',
    label: label,
    ariaLabel: label,
    canMakePayment: function() {
      return ( settings.installmentOptions || [] ).length > 0;
    },
    content: el( Content ),
    edit: el( Edit ),
    supports: {
      features: settings.supports || [ 'products' ],
    },
  } );
} )( window.wp, window.wc );
