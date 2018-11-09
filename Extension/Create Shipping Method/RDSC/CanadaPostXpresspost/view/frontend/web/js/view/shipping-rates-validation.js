/*browser:true*/
/*global define*/
define(
        [
            'uiComponent',
            'Magento_Checkout/js/model/shipping-rates-validator',
            'Magento_Checkout/js/model/shipping-rates-validation-rules',
            '../model/shipping-rates-validator',
            '../model/shipping-rates-validation-rules'
        ],
        function (
                Component,
                defaultShippingRatesValidator,
                defaultShippingRatesValidationRules,
                canadaPostXpresspostShippingRatesValidator,
                canadaPostXpresspostShippingRatesValidationRules
                ) {
            "use strict";
            defaultShippingRatesValidator.registerValidator('canadapostxpresspost', canadaPostXpresspostShippingRatesValidator);
            defaultShippingRatesValidationRules.registerRules('canadapostxpresspost', canadaPostXpresspostShippingRatesValidationRules);
            return Component;
        }
);
