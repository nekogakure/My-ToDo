<?php
ini_set('display_errors', 0);
error_reporting(0);
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
    $username = $_POST['username'];
    $password = $_POST['password'];

    // JSONファイルからユーザー情報を取得
    $jsonFile = 'users.json';
    if (file_exists($jsonFile)) {
        $jsonData = json_decode(file_get_contents($jsonFile), true);
    } else {
        $jsonData = [];
    }

    foreach ($jsonData as $user) {
        if ($user['username'] == $username && password_verify($password, $user['password'])) {
            // パスワードが正しい場合、セッションにユーザー情報を保存
            $_SESSION['username'] = $username;
            $_SESSION['dir'] = $user['directory'];  // ユーザーのディレクトリ
            header("Location:  ". $user['directory']);  // ユーザー専用ディレクトリにリダイレクト
            exit();
        }
    }

    // ユーザーが見つからない場合
    echo "ユーザー名またはパスワードが間違っています。";
}
?>
<head>
<title>My ToDo - Login</title>
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#4CAF50">
<link rel="stylesheet" href="login.css" type="text/css">
<script>
if ("serviceWorker" in navigator) {
  window.addEventListener("load", () => {
    navigator.serviceWorker.register("/service_worker.js").then(
      registration => {
        console.log("Service Worker registered with scope:", registration.scope);
      },
      error => {
        console.error("Service Worker registration failed:", error);
      }
    );
  });
}

if ('serviceWorker' in navigator && 'PushManager' in window) {
  navigator.serviceWorker.register('/service_worker.js').then(reg => {
    console.log('Service Worker Registered:', reg);

    // プッシュ通知の購読を開始
    reg.pushManager
      .subscribe({
        userVisibleOnly: true, // 常にユーザーに見える通知を要求
        applicationServerKey: '<公開鍵（BASE64 URL形式）>',
      })
      .then(subscription => {
        console.log('Subscription:', subscription);

        // サーバーに購読情報を送信
        fetch('/save-subscription.php', {
          method: 'POST',
          body: JSON.stringify(subscription),
          headers: {
            'Content-Type': 'application/json',
          },
        });
      });
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
    <label for="username">username</label>
    <input type="text" id="username" name="username" required>
    <label for="password">パスワード</label>
    <input type="password" id="password" name="password" required>
    <button type="submit">ログイン</button>
</div>
</section>
</form>
</body>