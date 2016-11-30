<?php
/**
 * Форма, необходимая для ввода необходимой суммы, а также ключ подтверждения
 * 
 * При обращении к скипту формы необходим GET-параметр OrderId, являющийся 
 * положительным целым числом.
 * Эта форма защищена от CSRF атаки с помощью токена CSRF, генерируемого сервером.
 * Форма защищена от подмены отправляемых данных добавкой HMAC. 
 * Ключ, используемый для вычисления HMAC, также генерируется сервером.
 * Ключи сохраняются в сессию.
 * Ключ подтверждения выведен на страницу как имитация получения его по SMS,
 * его необходимо ввести в поле Key.
 *
 * PHP version 7
 * 
 * @category Form_Security
 * @package  Forms
 * @author   Ilya Chetverikov <ischetverikov@gmail.com>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link     https://github.com/ISChetverikov/Forms
 */
require_once "validator.php";

if (!isset($_GET['OrderId'])) {
    echo "<p>Необходимый GET параметр не передан!</p>";
    exit();
}

if (!Validator::validateOrderId($_GET['OrderId'])) {
    echo "<p>В Get запросе передан неподходящий тип параметра</p>";
    exit();
}

session_start();

/*
 * Генерация CSRF токена
 */
$_SESSION['keyCsrfToken'] = md5(uniqid(rand(), true));
$salt = substr(md5(uniqid(rand(), true)), 0, 8);
$token = hash('sha256', $salt.$_SESSION['keyCsrfToken']);

/*
 * Генерация ключа HMAC
 */
$_SESSION['keyHmac'] = substr(md5(uniqid(rand(), true)), 0, 8);

/*
 * Страница с формой и JavaScript кодом для формирования HMAC на стороне клиента
 */
echo <<<HTML
<script type="text/javascript" src="hmac-sha256.js"></script>
<script type="text/javascript">
function send(){
    var amount = document.payment.Amount.value; 
    var orderId = document.payment.OrderId.value;
    var token = document.payment.token.value;
    var data = amount.toString()+orderId.toString()+token;
    
    var key = document.payment.Key.value;
    document.payment.Key.value = "";
    
    var HMAC = CryptoJS.HmacSHA256(data , key);
    document.payment.HMAC.value = HMAC;
}
</script>

<p>Вы получили по телефону SMS с ключом $_SESSION[keyHmac]</p>
<p>Введите его в поле Key</p>
<form name="payment" action="result.php" method="post" onsubmit="return send()">
    <p>Amount: <input type="text" name="Amount" required/></p>
    <p>Ключ подтверждения: <input type="password" name="Key" required></p>
    <p><input type="hidden" name="HMAC" /></p>
    <p><input type="hidden" name="OrderId" value=$_GET[OrderId] />
    <p><input type="hidden" name="token" value=$salt:$token></p>
    <p><input type="submit" /></p>
</form>
HTML;
?>



