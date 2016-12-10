<?php
require_once "../validator.php";
session_start();

function getResult(){
    if(!isset($_SESSION['passwordHash']) && !isset($_SESSION['keyCsrfToken'])) {
        $result['isValid'] = False;
        $result['result'] = "Ошибка доступа. Вы не авторизованы.";
        return $result;
    }
    
    if(!isset($_POST['HMAC']) && !isset($_POST['Amount'])) {
        $result['isValid'] = False;
        $result['result'] = "Ошибка доступа. Неполный AJAX запрос.";
        return $result;
    }
    
    $tokenArr = explode(":", $_POST['Token']);
    if (count($tokenArr) != 2) {
        $result['isValid'] = False;
        $result['result'] = "Попытка подмены данных.";
        return $result;
    }
    $salt = $tokenArr[0];
    $token = $tokenArr[1];
    
    $dataHMAC = $_POST['Amount'].$salt.":".hash('sha256', $salt.$_SESSION['keyCsrfToken']);
    $HMAC = hash_hmac('sha256', $dataHMAC, $_SESSION['passwordHash']);
    
    if($HMAC != $_POST['HMAC']) {
        $result['isValid'] = False;
        $result['result'] = "Попытка подмены данных.";
        return $result;
    }

    if(Validator::validateAmount($_POST['Amount'])){
        $result['isValid'] = True;
        $result['result'] = round($_POST['Amount'] * 0.01, 2);
        return $result;
    } else {
        $result['isValid'] = False;
        $result['result'] = "Некорректные данные. Сумма должна быть от 10.00 до 15000.00.";
        return $result;
    }
}

echo json_encode(getResult());


?>