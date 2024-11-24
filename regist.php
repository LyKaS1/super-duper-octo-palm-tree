<?php

// подключаем служебные файлы, которые создали раньше
require_once "connect.php";
require_once "session.php";
// сообщение об ошибке, на старте — пустое
$error ='';


// если на странице нажали кнопку регистрации
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {

    $error ='Ошибка';
    // берём данные из формы
    $full_name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST["confirm_password"]);
    $password_hash = password_hash($password, PASSWORD_BCRYPT);


    if($query = $db->prepare("SELECT * FROM users WHERE email = ?")) {
        $error = '';
    // указываем, что почта — это строка
    $query->bind_param('s', $email);
    $query->execute();
    // сначала проверяем, есть ли такой аккаунт в базе
    $query->store_result();
        if ($query->num_rows > 0) {
            $error .= '<p class="error">Пользователь с такой почтой уже зарегистрирован!</p>';
        } else {
            // проверяем требование к паролю
            if (strlen($password ) < 6) {
                $error .= '<p class="error">Пароль не должен быть короче 6 символов.</p>';
            }

            // проверяем, ввели ли пароль второй раз
            if (empty($confirm_password)) {
                $error .= '<p class="error">Пожалуйста, подтвердите пароль.</p>';
            } else {
                // если пароли не совпадают
                if (empty($error) && ($password != $confirm_password)) {
                    $error .= '<p class="error">Введённые пароли не совпадают.</p>';
                }
            }
            // если ошибок нет
            if (empty($error) ) {
                // добавляем запись в базу данных
                $insertQuery = $db->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?);");
                $insertQuery->bind_param("sss", $full_name, $email, $password_hash);
                $result = $insertQuery->execute();
                // если всё прошло успешно
                if ($result) {
                    $error .= '<p class="success">Вы успешно зарегистрировались!</p>';
                // если случилась ошибка
                } else {
                    $error .= '<p class="error">Ошибка регистрации, что-то пошло не так.</p>';
                }
            }
        }
    }
    // закрываем соединение с базой данных
    mysqli_close($db);
}
?>
<!DOCTYPE html>
<html lang="form-group">
    <head>
        <meta charset="UTF-8">
        <title>Регистрация</title>
        <!-- подключаем бутстрап -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    </head>
    <body>
        <!-- вся страница будет в одном контейнере -->
        <div class="container">
            <div class="row">
                <!-- делаем самую простую вёрстку -->
                <div class="col-md-12">
                    <h2>Регистрация</h2>
                    <p>Заполните все поля, чтобы создать новый аккаунт.</p>
                    
                </div>
            </div>
        </div>
        <?php echo $error; ?>
        <!-- форма регистрации -->
        <form action="" method="post">
            <!-- поле ввода имени -->
            <div class="form-group">
                <label>Имя</label>
                <input type="text" name="name" class="form-control" required>
            </div>    
            <!-- поле ввода электронной почты -->
            <div class="form-group">
                <label>Электронная почта</label>
                <input type="email" name="email" class="form-control" required />
            </div>    
            <!-- поле ввода пароля -->
            <div class="form-group">
                <label>Пароль</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <!-- поле повторного ввода пароля -->
            <div class="form-group">
                <label>Повторите пароль</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <!-- кнопка отправки данных на сервер -->
            <div class="form-group">
                <input type="submit" name="submit" class="btn btn-primary" value="Зарегистрироваться">
            </div>
            <!-- вход для тех, у кого уже есть аккаунт -->
            <p>Уже зарегистрированы? <a href="login.html">Войдите в систему</a>.</p>
        </form>    
    </body>
</html>
