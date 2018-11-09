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
                canadaPostExpeditedShippingRatesValidator,
                canadaPostExpeditedShippingRatesValidationRules
                ) {
            "use strict";
            defaultShippingRatesValidator.registerValidator('canadapostexpedited', canadaPostExpeditedShippingRatesValidator);
            defaultShippingRatesValidationRules.registerRules('canadapostexpedited', canadaPostExpeditedShippingRatesValidationRules);
            return Component;
        }
);
