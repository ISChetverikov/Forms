
<?php

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

$_SESSION['keyCsrfToken'] = md5(rand(0, 1000).rand());
$_SESSION['keyHmac'] = md5(rand(0, 1000).rand());
$salt = rand(0, 1000);
$token = hash('sha256', $salt.$_SESSION['keyCsrfToken']);

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

<p>Вы получили по телефону SMS с кодом $_SESSION[keyHmac]</p>
<form name="payment" action="result.php" method="post" onsubmit="return send()">
    <p>Amount: <input type="text" name="Amount" required/></p>
    <p>Key: <input type="password" name="Key" required></p>
    <p><input type="hidden" name="HMAC" /></p>
    <p><input type="hidden" name="OrderId" value=$_GET[OrderId] />
    <p><input type="hidden" name="token" value=$salt:$token></p>
    <p><input type="submit" /></p>
</form>
HTML;
?>



