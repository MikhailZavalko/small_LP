<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){

    // Файлы phpmailer
require 'class.phpmailer.php';
require 'class.smtp.php';

$form_subject = $_POST['form_subject'];
$name = $_POST['Имя'];
$number = $_POST['Телефон'];
$email = $_POST['email'];
$ip = $_POST['ip'];

$message = "";

$myCurl = curl_init();
//curl_setopt( $myCurl, CURLOPT_HTTPHEADER, array("REMOTE_ADDR: $ip", "HTTP_X_FORWARDED_FOR: $ip"));
$isCurlSuccess = curl_setopt_array($myCurl, array(
    CURLOPT_URL => 'https://app.getresponse.com/add_subscriber.html',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    //CURLOPT_INTERFACE => $ip,
    //CURLOPT_HTTPHEADER =>  array("REMOTE_ADDR: $ip", "HTTP_X_FORWARDED_FOR: $ip"),
    CURLOPT_POSTFIELDS => http_build_query(array(
    	"email" => $email,
    	"name" => $name,
    	"start_day" => "0",
    	"campaign_token" => "nAwze"
    ))
));
$response = curl_exec($myCurl);
curl_close($myCurl);

//echo $name;
//echo $email;
// echo 'Ответ от сервера'. $response;

// Настройки
$mail = new PHPMailer;

$mail->isSMTP();
$mail->Host = 'smtp.yandex.ru';
$mail->SMTPAuth = true;
$mail->Username = 'amazing.cash';
$mail->Password = 'qweASD123';
$mail->SMTPSecure = 'ssl';
$mail->Port = 465;
$mail->setFrom('amazing.cash@yandex.ru');
//$mail->addAddress('amazing.cash@mail.ru');
$mail->addAddress('info@amazing-cash.ru');


$method = $_SERVER['REQUEST_METHOD'];
//Script Foreach
$c = true;
if ( $method === 'POST' ) {

    $project_name = trim($_POST["project_name"]);
    $admin_email  = trim($_POST["admin_email"] || '');
    $form_subject = trim($_POST["form_subject"]);

    foreach ( $_POST as $key => $value ) {
        if ( $value != "" && $key != "project_name" && $key != "admin_email" && $key != "form_subject" ) {
            $message .= "
            " . ( ($c = !$c) ? '<tr>':'<tr style="background-color: #f8f8f8;">' ) . "
            <td style='padding: 10px; border: #e9e9e9 1px solid;'><b>$key</b></td>
            <td style='padding: 10px; border: #e9e9e9 1px solid;'>$value</td>
        </tr>
        ";
    }
}
} else if ( $method === 'GET' ) {

    $project_name = trim($_GET["project_name"]);
    $admin_email  = trim($_GET["admin_email"] || '');
    $form_subject = trim($_GET["form_subject"]);

    foreach ( $_GET as $key => $value ) {
        if ( $value != "" && $key != "project_name" && $key != "admin_email" && $key != "form_subject" ) {
            $message .= "
            " . ( ($c = !$c) ? '<tr>':'<tr style="background-color: #f8f8f8;">' ) . "
            <td style='padding: 10px; border: #e9e9e9 1px solid;'><b>$key</b></td>
            <td style='padding: 10px; border: #e9e9e9 1px solid;'>$value</td>
        </tr>
        ";
    }
}
}

$message = "<table style='width: 100%;'>$message 
    <td style='padding: 10px; border: #e9e9e9 1px solid;'><b>Откуда</b></td>
    <td style='padding: 10px; border: #e9e9e9 1px solid;'><a href='http://ipgeobase.ru/?address=".$_SERVER['REMOTE_ADDR']."' target='_blank'>посмотреть</a></td></table>";

                                 
// Письмо
$mail->isHTML(true); 
$mail->Subject = "$form_subject"; // Заголовок письма
$mail->Body    = "$message"; // Текст письма


include "bitrix24.php";

// Результат
if(!$mail->send()) {
    echo 'Письмо не может быть отправлено.';
    echo 'Ошибка отправки письма: ' . $mail->ErrorInfo;
} else {
    echo 'Письмо успешно отправлено';
}
}
