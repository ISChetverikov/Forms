<?php
/**
 * Файл, определ€ющий статический класс-валидатор полей формы
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
 * Класс-валидатор полей формы
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
     * Проверка того, что данные в строке €вл€ютс€ натуральным числом
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
     * Проверка того, что данные в строке являются положительной десятичной дробью,
     * с не более чем двумя дробными разрядами, 
     * не превышающей значения 15000.00 и не меньшей 10.00.
     *
     * @param string $amount сумма, введенна€ пользователем 
     *
     * @return bool данные в строке €вл€ютс€ числом указанногго выше вида?
     */
    public static function validateAmount($amount)
    {
        
        $options = array(
            'options' => array(
                                'regexp' => "/^\d{2,5}(?:\.\d{1,2})?$/",
                              )
        );
        $isValid = (bool)filter_var($amount, FILTER_VALIDATE_REGEXP, $options);
        
        if (!$isValid) {
            return false;
        }
        
        if ($amount < 10.00 || $amount > 15000.00) {
            return false;
        }
                
        return true;
    }
    
}
?>