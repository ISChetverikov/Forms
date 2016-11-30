<?php
require_once "validator.php";

session_start();

if (!isset($_SESSION['keyCsrfToken']) || !isset($_POST['token']) || !isset($_SESSION['keyHmac'])) {
    echo "<p>Попытка отправки данных в обход официальной формы!</p>";
    exit();
}

$tokenArr = explode(":", $_POST['token']);
if (count($tokenArr) != 2) {
    echo "<p>Попытка отправки данных в обход официальной формы!</p>";
    exit();
}
$salt = $tokenArr[0];
$token = $tokenArr[1];

$realToken = hash('sha256', $salt.$_SESSION['keyCsrfToken']);

if ($realToken != $token) {
    echo "<p>Попытка отправки данных в обход официальной формы!</p>";
    exit();
}

if (!isset($_POST['HMAC'])) {
    echo "<p>Данные прибыли без подтверждения подлинности!</p>";
    exit();
}

if (!isset($_POST['OrderId']) || !isset($_POST['Amount'])) {
    echo "<p>Необходимые данные с формы не прибыли!</p>";
    exit();
}

$dataHMAC = $_POST['Amount'].$_POST['OrderId'].$_POST['token'];
$realHMAC = hash_hmac('sha256', $dataHMAC, $_SESSION['keyHmac']);
if ($realHMAC != $_POST['HMAC']) {
    echo "<p>Неверный ключ подтверждения!</p>";
    exit();
}

if (!Validator::validateOrderId($_POST['OrderId'])
    || !Validator::validateAmount($_POST['Amount'])
) {
    echo "<p>Прибывшие данные не корректны!</p>";
    exit();
}

unset($_SESSION['keyCsrfToken']);
unset($_SESSION['keyHmac']);

echo "<p>Данные корректны!</p>";
?>