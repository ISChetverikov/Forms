<?php
/**
 * Скрипт, подменяющий значения полей формы form.php
 * 
 * PHP version 7
 * 
 * @category Form_Security
 * @package  Forms
 * @author   Ilya Chetverikov <ischetverikov@gmail.com>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link     https://github.com/ISChetverikov/Forms
 */
echo <<<HTML
<form action="../result.php" method="post">
  <p><input type="hidden" name="Amount" value=1000000 /></p>
  <p><input type="hidden" name="OrderId" value=10000 /></p>
  <p><input type="hidden" name="token" value=$_POST[token] /></p>
  <p><input type="hidden" name="HMAC" value=$_POST[HMAC] /></p>
  <p><input type="submit" value="Send next"/></p>
</form>
HTML;
?>