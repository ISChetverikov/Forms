<?php
/**
 * ����, ������������ ����������� �����-��������� ����� �����
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
 * �����-��������� ����� �����
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
     * �������� ����, ��� ������ � ������ �������� ����������� ������
     *
     * @param string $orderId ID ��������, ��������� �������������
     *
     * @return bool ������ � ������ �������� ����������� ������?
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
     * �������� ����, ��� ������ � ������ �������� ������������� ���������� ������,
     * � �� ����� ��� ����� �������� ���������
     *
     * @param string $amount �����, ��������� ������������� 
     *
     * @return bool ������ � ������ �������� ������ ����������� ���� ����?
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