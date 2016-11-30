<?php
/**
 * ‘айл, определ€ющий статический класс-валидатор полей формы
 * 
 * PHP version 7
 * 
 * @category Form_Security
 * @package  Forms
 * @author   Ilya Chetverikov <ischetverikov@gmail.com>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link     https://github.com/ISChetverikov/Forms
 */
 
 /**
 *  ласс-валидатор полей формы
 * 
 * PHP version 7
 * 
 * @category Form_Security
 * @package  Forms
 * @author   Ilya Chetverikov <ischetverikov@gmail.com>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link     https://github.com/ISChetverikov/Forms
 */
class Validator
{
    /**
     * ѕроверка того, что данные в строке €вл€ютс€ натуральным числом
     *
     * @param string $orderId ID операции, введенный пользователем
     *
     * @return bool данные в строке €вл€ютс€ натуральным числом?
     */
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
    
    /**
     * ѕроверка того, что данные в строке €вл€ютс€ положительной дес€тичной дробью,
     * с не более чем двум€ дробными разр€дами
     *
     * @param string $amount сумма, введенна€ пользователем 
     *
     * @return bool данные в строке €вл€ютс€ числом указанногго выше вида?
     */
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