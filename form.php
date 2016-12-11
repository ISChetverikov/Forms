<?php
/**
 * Форма, необходимая для ввода необходимой суммы
 * 
 * При обращении к скипту формы необходим GET-параметр OrderId, являющийся 
 * положительным целым числом.
 * Эта форма защищена от CSRF атаки с помощью токена CSRF, генерируемого сервером.
 * Форма защищена от подмены отправляемых данных добавкой HMAC. 
 * Ключ, используемый для вычисления HMAC, также генерируется сервером.
 * Ключи сохраняются в сессию.
 * Ключ подтверждения выведен на страницу как имитация получения его по SMS,
 * он введен пользователем заранее, например при авторизации.
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
$_SESSION['keyHmac'] = substr(md5(uniqid(rand(), true)), 0, 16);

/*
 * Страница с формой и JavaScript кодом для формирования HMAC на стороне клиента.
 * Введенный ключ подтверждения не передается срерверу
 */
echo <<<HTML
<script type="text/javascript" src="js\hmac-sha256.js"></script>
<script type="text/javascript" src="js\jquery-3.0.0.min.js"></script>
<script type="text/javascript">
function send(){
    var amount = $('input[name=Amount]').val(); 
    var orderId = $('input[name=OrderId]').val();
    var token = $('input[name=token]').val();
    var data = amount.toString()+orderId.toString()+token;
    
    var key = $('#keyHmac').html();
    
    var HMAC = CryptoJS.HmacSHA256(data , key);
    $('input[name=HMAC]').val(HMAC);
}

function getFeeValue(){
    var amount = $('input[name=Amount]').val();
    var token = $('input[name=token]').val();
    var data = amount.toString()+token;
    
    var key = $('#keyHmac').html();
    var HMAC = CryptoJS.HmacSHA256(data, key);
    
    $.ajax({
        type: "POST",
        url: "testing/intruderAJAX.php",
        data: "Amount="+amount+"&HMAC="+HMAC+"&Token="+token,
    }).done(function( result )
        {
            var resultArr = $.parseJSON(result);
            $("#feeMsg").html(resultArr.result);
        });
}
</script>

<p>Вы получили при авторизации по телефону SMS с ключом 
<span id="keyHmac">$_SESSION[keyHmac]</span></p>
<form name="payment" action="result.php" method="post" onsubmit="send()">
    <p>Amount: <input type="text" name="Amount" onblur="getFeeValue()" required/></p>
    <div>Коммисия: <span id="feeMsg"></span></div>
    <p><input type="hidden" name="HMAC" /></p>
    <p><input type="hidden" name="OrderId" value=$_GET[OrderId] />
    <p><input type="hidden" name="token" value=$salt:$token></p>
    <p><input type="submit" value="Send" /></p>
</form>
HTML;
?>



