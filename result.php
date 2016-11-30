<?php
/**
 * Скрипт, обрабатывающий форму form.php
 * 
 * Скрипт проверяет корректность CSRF токена, корректность HMAC. 
 * В случае их верности валидирует данные, пришедшие с формы 
 * Сбрасывает ключи, с помощью которых генерируются CSRF токен и HMAC
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

session_start();

/*
 * Проверка наличия всех необходимых данных для проверки безопасности операции
 */
if (!isset($_SESSION['keyCsrfToken']) || !isset($_POST['token'])) {
    echo "<p>Попытка отправки данных в обход официальной формы!</p>";
    exit();
}

if (!isset($_POST['HMAC']) || !isset($_SESSION['keyHmac'])) {
    echo "<p>Попытка отправки данных в обход официальной формы!</p>";
    exit();
}

/*
 * Проверка CSRF токена
 */
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

/*
 * Проверка прибытия с формы данных, необходимых для проведения операции
 */
if (!isset($_POST['OrderId']) || !isset($_POST['Amount'])) {
    echo "<p>Необходимые данные с формы не прибыли!</p>";
    exit();
}

/*
 * Проверка корректности HMAC
 */
$dataHMAC = $_POST['Amount'].$_POST['OrderId'].$_POST['token'];
$realHMAC = hash_hmac('sha256', $dataHMAC, $_SESSION['keyHmac']);
if ($realHMAC != $_POST['HMAC']) {
    echo "<p>Неверный ключ подтверждения!</p>";
    exit();
}

/*
 * Валидация данных, присланных пользователем
 */
if (!Validator::validateOrderId($_POST['OrderId'])
    || !Validator::validateAmount($_POST['Amount'])
) {
    echo "<p>Прибывшие данные не корректны!</p>";
    exit();
}

/*
 * Сброс ключей, необходимых для генерации CSRF токена и HMAC
 */
unset($_SESSION['keyCsrfToken']);
unset($_SESSION['keyHmac']);

/*
 * Операция прошла успешно
 */
echo "<p>Данные корректны!</p>";
?>