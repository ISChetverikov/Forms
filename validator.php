<?php
class Validator
{
    public static function validateOrderId($orderId)
    {
        $options = array(
            'options' => array(
                                'min_range' => 1,
                              )
        );
        $isValid = (bool)filter_var($orderId, FILTER_VALIDATE_INT, $options);
        
        return $isValid;
    }
    
    public static function validateAmount($amount)
    {
        
        $options = array(
            'options' => array(
                                'regexp' => "/^\d{1,8}(?:\.\d{1,2})?$/",
                              )
        );
        $isValid = (bool)filter_var($amount, FILTER_VALIDATE_REGEXP, $options);
        
        return $isValid;
    }
    
}
?>