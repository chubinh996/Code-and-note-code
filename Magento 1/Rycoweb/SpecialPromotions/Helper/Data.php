<?php
/**
* @category Rycoweb
* @package Rycoweb_SpecialPromotions
*/
class Rycoweb_SpecialPromotions_Helper_Data extends Mage_Core_Helper_Abstract
{

   public function getRuleInfoByRuleId($ruleIds,$prodCatIds){

       $discountAmountFlag = false;
       $ruleId = explode(',',$ruleIds);

       foreach ($ruleId as $ruleId_key=>$ruleId_val) {
           $rule = Mage::getModel('salesrule/rule')->load($ruleId_val);
           $rule_name=$rule->getName();
           $rule_action = $rule->getSimpleAction();
           if($rule_action == 'by_percent') {

                   $discountAmt = $rule->getDiscountAmount();
                   $ruleConditionsDataHelper = unserialize($rule->getConditionsSerialized());
                   $ruleAllConditions = array();

               foreach($ruleConditionsDataHelper as $ruleConditionsDataKeyHlp=>$ruleConditionsDataValHlp) {
                   foreach($ruleConditionsDataHelper['conditions'] as $rulesConditionsKeyHlp=>$rulesConditionsValHlp) {
                       if(in_array('salesrule/rule_condition_product_subselect',$rulesConditionsValHlp)){
                           $ruleAllConditions['qty_operator']=$rulesConditionsValHlp['operator'];
                           $ruleAllConditions['qty']=$rulesConditionsValHlp['value'];
                           foreach($rulesConditionsValHlp['conditions'] as $rulesKey=>$ruleVal){
                               $ruleAllConditions['category_ids'] = $ruleVal['value'];
                           }
                       } else {
                           continue;
                       }
                   }
               }
               $ruleAllConditions['discount_amount'] = round($discountAmt,2);
               $ruleAllConditions['simple_action'] = $rule_action;
               $ruleAllConditions['rule_name'] = $rule_name;

               if(count(array_intersect($prodCatIds, explode(',',$ruleAllConditions['category_ids']))) > 0) {
                   $ruleAllConditions['rule_id'] = $ruleId_val;
                   return $ruleAllConditions;

              } else {
                   return null;
               }


           } elseif($rule_action == 'use_bulk_price')
           {
               $discountAmt = $rule->getDiscountAmount();
               $ruleConditionsDataHelper = unserialize($rule->getConditionsSerialized());

               $ruleAllConditionsHlp = array();
               foreach($ruleConditionsDataHelper as $ruleConditionsDataKeyHlp=>$ruleConditionsDataValHlp) {

                   foreach($ruleConditionsDataHelper['conditions'] as $rulesConditionsKeyHlp=>$rulesConditionsValHlp) {

                       $ruleAllConditionsHlp['qty']=$rulesConditionsValHlp['value'];

                       foreach($rulesConditionsValHlp['conditions'] as $rulesKey=>$ruleVal){
                           if($ruleVal['attribute'] == 'category_ids'){
                               $ruleAllConditionsHlp['category_ids'] = $ruleVal['value'];
                           }
                           if($ruleVal['attribute'] == 'quote_item_qty'){
                               $ruleAllConditionsHlp['qty'] = $ruleVal['value'];
                               $ruleAllConditionsHlp['qty_operator']=$ruleVal['operator'];
                           }

                       }

                   }

               }
               $ruleAllConditionsHlp['discount_amount'] = round($discountAmt,2);
               $ruleAllConditionsHlp['simple_action'] = $rule_action;
               $ruleAllConditionsHlp['rule_name'] = $rule_name;

               if(count(array_intersect($prodCatIds, explode(',',$ruleAllConditionsHlp['category_ids']))) > 0) {
                   $ruleAllConditionsHlp['rule_id'] = $ruleId_val;
                   return $ruleAllConditionsHlp;
               } else {
                   return null;
               }

           } else {
               continue;
           }
       }//foreach

   }

}