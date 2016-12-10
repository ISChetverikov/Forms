<?php
require_once "../validator.php";

$result = array();

if(isset($_POST['Amount']) && Validator::validateAmount($_POST['Amount'])){
    $result['isValid'] = True;
    $result['result'] = round($_POST['Amount'] * 0.01, 2);
} else {
    $result['isValid'] = False;
    $result['result'] = "Некорректные данные. Сумма должна быть от 10.00 до 15000.00";
}

    echo json_encode($result);
?>