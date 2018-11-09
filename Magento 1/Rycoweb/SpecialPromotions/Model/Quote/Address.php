<?php
/**
 * Sales Quote Address Model
 *
 * Allows dispatching before and after events for each controller action
 *
 * @category    Rycoweb
 * @package     Rycoweb_SpecialPromotions
 */
class Rycoweb_SpecialPromotions_Model_Quote_Address extends Mage_Sales_Model_Quote_Address
{
    /**
     * Set total amount value
     *
     * @param   string $code
     * @param   float $amount
     * @return  Mage_Sales_Model_Quote_Address
     */
    public function setTotalAmount($code, $amount)
    {
        //below code written to set any discount values to 0 on cart page
        if (Mage::app()->getFrontController()->getAction()->getFullActionName() == 'checkout_cart_index') {
            if($code == 'discount'){
                $amount = 0;
            }
        }
        $this->_totalAmounts[$code] = $amount;
        if ($code != 'subtotal') {
            $code = $code.'_amount';
        }
        $this->setData($code, $amount);
        return $this;
    }

}