<?php
/**
 * Observer Model
 *
 * @category    Rycoweb
 * @package     Rycoweb_SpecialPromotions
 */
class Rycoweb_SpecialPromotions_Model_Observer extends Varien_Object
{
    public function salesQuoteItemSet($observer)
    {
        /** @var Mage_Sales_Model_Quote_Item $quoteItem */
        $quoteItem = $observer->getQuoteItem();
        $product = $observer->getProduct();
        $quoteItem->setBulkPrice($product->getBulkPrice());
    }

    public function updateCartForRules($observer)
    {

        $quote = $observer->getCart()->getQuote();
        $quoteTotalAmount = 0;
        $appliedRuleIds = $quote->getAppliedRuleIds();
        $ruleInfoById = array();
        foreach ($quote->getAllItems() as $quote_item) {
            $ruleDiscountedPrice = 0;
            $product = Mage::getModel('catalog/product')->load($quote_item->getProductId());
            $now = Mage::getModel('core/date')->date('Y-m-d');
            $productData = new Varien_Object();
            $data = array(
                'product' => $product,
                'qty' => $quote_item->getQty(),
                'price' => $product->getFinalPrice(),
                'base_row_total' => $product->getFinalPrice()
            );
            $productData->setData($data);
            $allItems = new Varien_Object();
            $allItems->setAllItems(array($productData));
            $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
            $catIds = $product->getCategoryIds();
            $ruleCollection = Mage::getResourceModel('salesrule/rule_collection')
                ->addWebsiteGroupDateFilter(Mage::app()->getStore()->getWebsiteId(), $customerGroupId);
            foreach ($ruleCollection->getData() as $rule) {

                $shoppingCartRules[] = $rule;
            }
            foreach ($ruleCollection->getData() as $rulek => $rulev) {
                $item_rule_id = $rulev['rule_id'];
                $ruleInfoById[] = $this->getRuleInfoById($item_rule_id);

            }

            $productBulkPrice = round($product->getBulkPrice(), 2);
            $productPrice = round($product->getPrice(), 2);
            $netProductPrice = $productPrice;
            $itemCartQty = $quote_item->getQty();

            foreach ($ruleInfoById as $ruleInfoByIdKey=>$ruleInfoByIdVal) {

                $ruleDiscountedPrice = 0;
                $rulePct = $ruleInfoByIdVal['rule_discount_amount']/100;
                $cartRuleName = $ruleInfoByIdVal['rule_name'];
                $cartRuleAction = $ruleInfoByIdVal['rule_action'];
                if($cartRuleAction=='by_percent' || $cartRuleAction=='use_bulk_price'){
                    if ($itemCartQty >= $ruleInfoByIdVal['ruleAllConditions_qty']) {
                        if (count(array_intersect($catIds, explode(',',$ruleInfoByIdVal['ruleAllConditions_category_ids']))) > 0) {
                            if (isset($productBulkPrice) && !empty($productBulkPrice) && ($ruleInfoByIdVal['rule_action'] == 'use_bulk_price')) {
                                $netProductPrice = $productBulkPrice;
                                break;
                            } else {
                                $netProductPrice = $netProductPrice - ($netProductPrice * $rulePct);
                                break;
                            }
                        }//catids
                    }//qty

                } else {
                    break;
                }
            }//rules foreach
            $quote_item->setCustomPrice($netProductPrice);
            $quote_item->setOriginalCustomPrice($netProductPrice);
            $quote_item->getProduct()->setIsSuperMode(true);
            $quote_item->setProduct($product);
            //$quote_item->save();
            $quote = Mage::getModel('checkout/session')->getQuote();
            $quote->setTotalsCollectedFlag(false)->collectTotals();
        }//product foreach

    }//function

    public function getRuleInfoById($item_rule_id)
    {
          $itemRuleInfo = array();
          $ruleInfo = Mage::getModel('salesrule/rule')->load($item_rule_id);
          $rule_name=$ruleInfo->getName();
          $rule_action = $ruleInfo->getSimpleAction();
          $rule_discount_amount = round($ruleInfo->getDiscountAmount(),2);
          if($rule_action == 'by_percent') {

              $ruleConditionsDataHelper = unserialize($ruleInfo->getConditionsSerialized());
              foreach($ruleConditionsDataHelper as $ruleConditionsDataKeyHlp=>$ruleConditionsDataValHlp) {
                  foreach($ruleConditionsDataHelper['conditions'] as $rulesConditionsKeyHlp=>$rulesConditionsValHlp) {
                      if(in_array('salesrule/rule_condition_product_subselect',$rulesConditionsValHlp)){
                          $ruleAllConditions_qty_operator = $rulesConditionsValHlp['operator'];
                          $ruleAllConditions_qty = $rulesConditionsValHlp['value'];
                          foreach($rulesConditionsValHlp['conditions'] as $rulesKey=>$ruleVal){
                              $ruleAllConditions_category_ids = $ruleVal['value'];
                          }
                      } else {
                          continue;
                      }
                  }
              }


          } elseif($rule_action == 'use_bulk_price') {

              $ruleConditionsDataHelper = unserialize($ruleInfo->getConditionsSerialized());
              foreach($ruleConditionsDataHelper as $ruleConditionsDataKeyHlp=>$ruleConditionsDataValHlp) {

                  foreach($ruleConditionsDataHelper['conditions'] as $rulesConditionsKeyHlp=>$rulesConditionsValHlp) {

                      foreach($rulesConditionsValHlp['conditions'] as $rulesKey=>$ruleVal){
                          if($ruleVal['attribute'] == 'category_ids'){
                              $ruleAllConditions_category_ids = $ruleVal['value'];
                          }
                          if($ruleVal['attribute'] == 'quote_item_qty'){
                              $ruleAllConditions_qty = $ruleVal['value'];
                              $ruleAllConditions_qty_operator=$ruleVal['operator'];
                          }

                      }

                  }

              }

          }

          $itemRuleInfo = compact('rule_name', 'rule_action', 'rule_discount_amount','ruleAllConditions_category_ids','ruleAllConditions_qty','ruleAllConditions_qty_operator');
          return $itemRuleInfo;

    }
}