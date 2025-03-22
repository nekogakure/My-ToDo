<?php
session_start();

// セッションに 'dir' が設定されているか確認
if (isset($_SESSION['dir'])) {
    // セッションの 'dir' を取得
    $dir = $_SESSION['dir'];

    // セッションの dir に基づいたパスを作成
    $redirect_path = $dir . '/';

    // リダイレクト先のパスが存在する場合、リダイレクト
    if (is_dir($redirect_path)) {
        header("Location: " . $redirect_path);
        exit();
    } else {
        // ディレクトリが存在しない場合
        echo "指定されたディレクトリは存在しません:" . $redirect_path;
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $useruserName = $_POST['userName'];  // 学生ID (userName)
    $password = $_POST['password'];

    // JSONファイルからユーザー情報を取得
    $jsonFile = 'users.json';
    if (file_exists($jsonFile)) {
        $jsonData = json_decode(file_get_contents($jsonFile), true);
    } else {
        $jsonData = [];
    }

    // 入力されたuserNameとパスワードをチェック
    foreach ($jsonData as $user) {
        if ($user['userName'] == $useruserName && password_verify($password, $user['password'])) {
            // パスワードが正しい場合、セッションにユーザー情報を保存
            $_SESSION['userName'] = $useruserName;  // userNameをセッションに保存
            $_SESSION['dir'] = $user['directory'];  // ユーザーのディレクトリ
            header("Location:  ". $user['directory']);  // ユーザー専用ディレクトリにリダイレクト
            exit();
        }
    }

    // ユーザーが見つからない場合
    echo "userNameまたはパスワードが間違っています。";
}
?>
<head>
<title>My ToDo - Login</title>
<meta name="theme-color" content="#4CAF50">
<link rel="stylesheet" href="login.css" type="text/css">
<link rel="manifest" href="./manifest.json" />
<link rel="apple-touch-icon" href="./icon512.png">
<script>
        if ("serviceWorker" in navigator) {
            window.addEventListener("load", () => {
                navigator.serviceWorker.register("https://mytodo.f5.siservice_worker.js").then(
                    registration => {
                                console.log("Service Worker registered with scope:", registration.scope);
                    },
                    error => {
                        console.error("Service Worker registration failed:", error);
                    }
                );
            });
        }

    </script>
</head>
<body>

<?php
include('header.php');
?>
<section class="main">
<div class=".container">
<h1>Login</h1>
<form method="POST">
    <label for="userName">userName</label>
    <input type="text" id="userName" name="userName" required>
    <label for="password">パスワード</label>
    <input type="password" id="password" name="password" required>
    <button type="submit">ログイン</button>
</div>
</section>
</form>
</body>