<?php
session_start();
include('log.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ユーザー情報を登録
    $userName = $_POST['userName'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // パスワードをハッシュ化
    $dirName = $_POST['directory'];
    
    // 新しいディレクトリを作成
    $newDir = 'user_data/' . $dirName;
    if (!is_dir($newDir)) {
        mkdir($newDir, 0777, true); // 新しいディレクトリを作成
    }

    // ./main の内容をコピー
    $sourceDir = './main';
    copy_dir($sourceDir, $newDir);

    // ユーザー情報をJSONファイルに保存
    $jsonFile = 'users.json';
    if (file_exists($jsonFile)) {
        $jsonData = json_decode(file_get_contents($jsonFile), true);
    } else {
        $jsonData = [];
    }

    // 新しいユーザー情報を追加
    $jsonData[] = [
        'userName' => $userName,
        'password' => $password,
        'directory' => $newDir
    ];

    log_write("ユーザー $userName が登録され、ディレクトリ $newDir が作成され、./main の内容がコピーされました。");

    // JSONファイルに書き込み
    file_put_contents($jsonFile, json_encode($jsonData, JSON_PRETTY_PRINT));

    //echo "ユーザー $userName が登録され、ディレクトリ $newDir が作成され、./main の内容がコピーされました。";
}

// ディレクトリを再帰的にコピーする関数
function copy_dir($source, $destination) {
    // ソースディレクトリが存在しない場合
    if (!is_dir($source)) {
        die("ソースディレクトリが存在しません: $source");
    }

    // ディレクトリを作成
    if (!is_dir($destination)) {
        mkdir($destination, 0777, true);
    }

    // ソースディレクトリ内のファイルとディレクトリをループ
    $items = scandir($source);
    foreach ($items as $item) {
        // . と .. はスキップ
        if ($item == '.' || $item == '..') {
            continue;
        }

        $sourceItem = $source . DIRECTORY_SEPARATOR . $item;
        $destinationItem = $destination . DIRECTORY_SEPARATOR . $item;

        // アイテムがディレクトリの場合は再帰的にコピー
        if (is_dir($sourceItem)) {
            copy_dir($sourceItem, $destinationItem);
        } else {
            // ファイルの場合はコピー
            copy($sourceItem, $destinationItem);
        }
    }
}
?>
<html>
<head>
<title>My ToDo - Register</title>
<link rel="stylesheet" href="login.css" type="text/css">
</head>
<body>

<?php
include('header.php');
?>

<section class="main">
<div class="container">
<h1>My ToDo - Register</h1>
<form method="POST">
    <label for="userName">ユーザー名</label>
    <input type="text" id="userName" name="userName" required>
    <label for="password">パスワード</label>
    <input type="password" id="password" name="password" required>
    <label for="directory">ディレクトリ名</label>
    <input type="text" id="directory" name="directory" required>
    <button type="submit">登録</button>
</form>
</div>
</form>
</section>
</body>
</html>