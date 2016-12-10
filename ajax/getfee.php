<?php
/**
 * Файл с обработчиком AJAX запроса на расчет суммы комиссии
 * 
 * Форма обращается к данному скрипту для вычисления суммы комиссии.
 * Для защиты от подмены данных по сумме, введенной пользователем и
 * CSRF токену на клиенте вычисляется HMAC. Если он совпадает с вычисленным
 * на сервере, то данные считаются не подменнеными.
 * В качестве ключа HMAC выступает имитация хэша пароля, введенным пользователем
 * при авторизации.
 * Скрпит возвращает данные в формате JSON.
 *
 * PHP version 7
 * 
 * @category Form_Security
 * @package  Forms
 * @author   Ilya Chetverikov <ischetverikov@gmail.com>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link     https://github.com/ISChetverikov/Forms
 */
require_once "../validator.php";
session_start();

/**
 * Функция, вычисляющая сумму комисси 
 *
 * @return array словарь: 'isValid' bool удалось ли вычислить значение?
 *                        'result' mixed результат вычисления или сообщение об ошибке
 */
function getResult()
{
    /*
     * Проверка наличия переменных сессии
     */
    if (!isset($_SESSION['passwordHash']) || !isset($_SESSION['keyCsrfToken'])) {
        $result['isValid'] = false;
        $result['result'] = "Ошибка доступа. Вы не авторизованы.";
        return $result;
    }
    
    /*
     * Проверка наличия ожидаемых данных
     */
    if (!isset($_POST['HMAC']) 
        || !isset($_POST['Amount']) 
        || !isset($_POST['Token'])
    ) {
        $result['isValid'] = false;
        $result['result'] = "Неполный AJAX запрос.";
        return $result;
    }
    
    /*
     * Вычисление HMAC кода для проверка целостности данных
     */
    $tokenArr = explode(":", $_POST['Token']);
    if (count($tokenArr) != 2) {
        $result['isValid'] = false;
        $result['result'] = "Попытка подмены данных.";
        return $result;
    }
    $salt = $tokenArr[0];
    
    $dataHMAC = $_POST['Amount'];
    $dataHMAC.= $salt.":".hash('sha256', $salt.$_SESSION['keyCsrfToken']);
    $HMAC = hash_hmac('sha256', $dataHMAC, $_SESSION['passwordHash']);
    
    if ($HMAC != $_POST['HMAC']) {
        $result['isValid'] = false;
        $result['result'] = "Попытка подмены данных.";
        return $result;
    }

    /*
     * Проверка корректности данных.
     * В случае успеха нахождение величины комиссии.
     */
    if (Validator::validateAmount($_POST['Amount'])) {
        $result['isValid'] = true;
        $result['result'] = round($_POST['Amount'] * 0.01, 2);
        return $result;
    } else {
        $result['isValid'] = false;
        $result['result'] = "Некорректные данные. "
            ."Сумма должна быть от 10.00 до 15000.00.";
        return $result;
    }
}

/*
 * Представляем резултат в виде JSON.
 */
 echo json_encode(getResult());

?>