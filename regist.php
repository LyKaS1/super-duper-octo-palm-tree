<?php
require_once "connect.php";
require_once "session.php";
$error ='';



if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {

    $error ='Ошибка';
    $full_name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST["confirm_password"]);
    $password_hash = password_hash($password, PASSWORD_BCRYPT);


    if($query = $db->prepare("SELECT * FROM users WHERE email = ?")) {
        $error = '';
    $query->bind_param('s', $email);
    $query->execute();
    $query->store_result();
        if ($query->num_rows > 0) {
            $error .= '<p class="error">Пользователь с такой почтой уже зарегистрирован!</p>';
        } else {
            if (strlen($password ) < 6) {
                $error .= '<p class="error">Пароль не должен быть короче 6 символов.</p>';
            }

            if (empty($confirm_password)) {
                $error .= '<p class="error">Пожалуйста, подтвердите пароль.</p>';
            } else {
                if (empty($error) && ($password != $confirm_password)) {
                    $error .= '<p class="error">Введённые пароли не совпадают.</p>';
                }
            }
            if (empty($error) ) {
                $insertQuery = $db->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?);");
                $insertQuery->bind_param("sss", $full_name, $email, $password_hash);
                $result = $insertQuery->execute();
                if ($result) {
                    $error .= '<p class="success">Вы успешно зарегистрировались!</p>';
                } else {
                    $error .= '<p class="error">Ошибка регистрации, что-то пошло не так.</p>';
                }
            }
        }
    }
    mysqli_close($db);
}
?>
<!DOCTYPE html>
<html lang="form-group">
    <head>
        <meta charset="UTF-8">
        <title>Регистрация</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2>Регистрация</h2>
                    <p>Заполните все поля, чтобы создать новый аккаунт.</p>
                    
                </div>
            </div>
        </div>
        <?php echo $error; ?>
        <form action="" method="post">
            <div class="form-group">
                <label>Имя</label>
                <input type="text" name="name" class="form-control" required>
            </div>    
            <div class="form-group">
                <label>Электронная почта</label>
                <input type="email" name="email" class="form-control" required />
            </div>    
            <div class="form-group">
                <label>Пароль</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Повторите пароль</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <div class="form-group">
                <input type="submit" name="submit" class="btn btn-primary" value="Зарегистрироваться">
            </div>
            <p>Уже зарегистрированы? <a href="login.html">Войдите в систему</a>.</p>
        </form>    
    </body>
</html>
