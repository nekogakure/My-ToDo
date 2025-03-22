<?php
session_start();

// 管理者専用アクセス制御
if ($_SESSION['userName'] != 's24087') {
    die('アクセス権限がありません');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ユーザー情報を登録
    $useruserName = $_POST['userName'];  // 学生ID (userName)
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
        'userName' => $useruserName,
        'password' => $password,
        'directory' => $newDir
    ];

    // JSONファイルに書き込み
    file_put_contents($jsonFile, json_encode($jsonData, JSON_PRETTY_PRINT));

    echo "ユーザー $useruserName が登録され、ディレクトリ $newDir が作成され、./main の内容がコピーされました。";
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
<title>My ToDo - Admin</title>
<link rel="stylesheet" href="login.css" type="text/css">
</head>
<body>

<?php
include('header.php');
?>

<section class="main">
<div class="container">
<h1>My ToDo - Admin</h1>
<form method="POST">
    <label for="userName">userName</label>
    <input type="text" id="userName" name="userName" required>
    <label for="password">パスワード</label>
    <input type="password" id="password" name="password" required>
    <label for="directory">ディレクトリ名</label>
    <input type="text" id="directory" name="directory" required>
    <button type="submit">ユーザー登録</button>
</form>
</div>
</form>
</section>
</body>
</html>