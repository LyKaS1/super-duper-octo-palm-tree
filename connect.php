<?php
define('DBSERVER', 'localhost'); // сервер с базой данных
define('DBUSERNAME', '***'); // имя пользователя
define('DBPASSWORD', '***'); // пароль
define('DBNAME', '***'); // название базы
 
$db = mysqli_connect(DBSERVER, DBUSERNAME, DBPASSWORD, DBNAME);
 
if($db === false){
    die("Ошибка соединения с базой. " . mysqli_connect_error());
}
?>
