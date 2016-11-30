
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

$_SESSION['secret'] = md5(rand(0,1000).rand());//
$salt = rand(0,1000);
$token = hash('sha256', $salt.$_SESSION['secret']);

echo <<<HTML
    <script type="text/javascript" src="hmac-sha256.js"></script>
    <script type="text/javascript">
    function send(){  
        var HMAC = CryptoJS.HmacSHA256("Message", "Secret Passphrase");
        alert(HMAC);
    }
   
 </script>

<form action="result.php" method="post" onsubmit="return send()">
    <p>Amount: <input type="text" name="Amount" required/></p>
    <p><input type="hidden" name="HMAC" /></p>
    <p><input type="hidden" name="OrderId" value=$_GET[OrderId] />
    <p><input type="hidden" name="token" value=$salt:$token></p>
    <p><input type="submit" /></p>
</form>
HTML;
?>



