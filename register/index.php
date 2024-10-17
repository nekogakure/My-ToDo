<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        // パスワードをハッシュ化
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // ユーザー情報を保存
        $user_data = [
            'username' => $username,
            'password' => $hashed_password,
        ];

        $json_data = json_encode($user_data);

        // user.json に保存
        file_put_contents('../user.json', $json_data);

        echo "ユーザー登録が完了しました。";
    } else {
        echo "ユーザー名とパスワードを入力してください。";
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyToDo - 登録</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<form method="POST">
    <label for="username">ユーザー名:</label>
    <input type="text" name="username" id="username" required>
    <br>
    <label for="password">パスワード:</label>
    <input type="password" name="password" id="password" required>
    <br>
    <button type="submit">登録</button>
</form>
</body>
</html>
