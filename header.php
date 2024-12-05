<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // セッションが開始されていない場合に開始
}

// セッションからSIDを取得し、ユーザー名を設定
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : '<a>NotLogin</a>';

// config.json ファイルのパス
$config_file = './config.json';

// ファイルが存在するか確認
if (file_exists($config_file)) {
    // config.jsonを読み込む
    $json_data = file_get_contents($config_file);

    // JSONデータを配列にデコード
    $config = json_decode($json_data, true);

    // versionキーの値を取得
    if (isset($config['version'])) {
        $version = $config['version'];
    } else {
 //       echo "versionキーが存在しません。";
    }
} else {
  //  echo "config.jsonファイルが存在しません。";
}

function GetPageURL() {
    // サーバー名（ホスト名）を取得
    $host = $_SERVER['HTTP_HOST'];
    
    // HTTPSかどうかを確認
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    
    // URLを構築して返す
    return $protocol . '://' . $host;
}
//echo GetPageURL();
$HOSTNAME = GetPageURL();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Header styling */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        header {
            background-color: #4CAF50;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            position: top;
            position-area: top;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .menu-icon {
            cursor: pointer;
            font-size: 24px;
        }

        .title {
            flex-grow: 1;
            text-align: center;
        }

        .user-name {
            margin-left: auto;
        }

        /* Sliding menu styling */
        #menu {
            position: fixed;
            top: 0;
            left: -100%;
            width: 75%;
            height: 100%;
            background-color: #333 !important;
            color: white;
            transition: left 0.3s ease-in-out;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
        }

        #menu .close-btn {
            font-size: 24px;
            color: white;
            text-align: left;
            cursor: pointer;
            margin-bottom: 20px;
        }

        #menu ul {
            list-style: none;
            padding: 0;
        }

        #menu ul li {
            margin: 15px 0;
        }

        #menu ul li a {
            color: white;
            text-decoration: none;
            background-color: transparent !important;
        }
    </style>
</head>
<body>
    <header>
        <section class="menu-icon" onclick="toggleMenu()">☰</section>
        <section class="title">My ToDo</section>
        <section class="user-name"><?php echo $username; ?></section>
    </header>

    <section id="menu">
        <section class="close-btn" onclick="toggleMenu()">×</section>
        <ul>
            <li><a href="<?php echo $HOSTNAME; ?>">Home</a></li>
            <li><a href="https://github.com/nekogakure/My-ToDo/blob/main/README.md">About</a></li>
            <li><a href="https://docs.google.com/forms/d/e/1FAIpQLSdXjcNaBR200vG-NCnUanc6vlOGOrvbGLAlICrkgAroyHpGQA/viewform">Contact</a></li>
            <li><a href="<?php echo $HOSTNAME; ?>/logout.php">Logout</a></li>
        </ul>
        <p style="color: #c0c0c0">……………………</p>
        <img src="https://mytodo.f5.si/apple-touch-icon-120x120.png" alt="My ToDo icon" />
        <p style="font-size: x-small; color: #c0c0c0;">MyToDo ver.<?php echo $version ?></p>
        <small>© 2024 nekogakure.</small>
    </section>

    <script>
        function toggleMenu() {
            const menu = document.getElementById('menu');
            const menuStyle = window.getComputedStyle(menu);
            const currentLeft = menuStyle.left;

            if (currentLeft === '0px') {
                menu.style.left = '-100%'; // メニューを閉じる
            } else {
                menu.style.left = '0'; // メニューを開く
            }
        }
    </script>
</body>
</html>
