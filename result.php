<?php
require_once "validator.php";

session_start();

if (!isset($_SESSION['secret']) || !isset($_POST['token'])) {
    echo "<p>Попытка отправки данных в обход официальной формы1!</p>";
    exit();
}

$tokenArr = explode(":", $_POST['token']);
if (count($tokenArr) != 2) {
    echo "<p>Попытка отправки данных в обход официальной формы2!</p>";
    exit();
}
$salt = $tokenArr[0];
$token = $tokenArr[1];

$realToken = hash('sha256', $salt.$_SESSION['secret']);

if ($realToken != $token) {
    echo "<p>Попытка отправки данных в обход официальной формы3!</p>";
    exit();
}

if (!isset($_POST['OrderId']) || !isset($_POST['Amount'])) {
    echo "<p>Необходимые данные с формы не прибыли!</p>";
    exit();
}

if (!Validator::validateOrderId($_POST['OrderId'])
    || !Validator::validateAmount($_POST['Amount'])
) {
    echo "<p>Прибывшие данные не корректны!</p>";
    exit();
}

unset($_SESSION['secret']);
echo $token;
echo "<br/>";
echo $realToken;
echo "<p>Данные корректны!</p>";
?>