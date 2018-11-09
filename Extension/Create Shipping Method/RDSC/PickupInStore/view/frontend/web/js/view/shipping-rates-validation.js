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
                pickupInStoreShippingRatesValidator,
                pickupInStoreShippingRatesValidationRules
                ) {
            "use strict";
            defaultShippingRatesValidator.registerValidator('storepickup', pickupInStoreShippingRatesValidator);
            defaultShippingRatesValidationRules.registerRules('storepickup', pickupInStoreShippingRatesValidationRules);
            return Component;
        }
);
