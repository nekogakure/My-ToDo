<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // user.json からユーザー情報を読み込む
    if (file_exists('user.json')) {
        $json_data = file_get_contents('user.json');
        $user_data = json_decode($json_data, true);

        // ユーザー名とパスワードの確認
        if ($username === $user_data['username'] && password_verify($password, $user_data['password'])) {
            // ログイン成功
            $_SESSION['loggedin'] = true;
            header('Location: index.php');
            exit;
        } else {
            $error_message = "ユーザー名またはパスワードが間違っています。";
        }
    } else {
        $error_message = "ユーザーが登録されていません。";
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyToDo - ログインページ</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
<div class="container">
<form method="POST">
    <h1>MyToDo - Login</h1>
    <label for="username">ユーザー名:</label>
    <input type="text" name="username" id="username" required>
    <br>
    <label for="password">パスワード:</label>
    <input type="password" name="password" id="password" required>
    <br>
    <button type="submit">ログイン</button>
</form>
</div>
</body>
</html>



<?php
if (isset($error_message)) {
    echo "<p style='color:red;'>$error_message</p>";
}
