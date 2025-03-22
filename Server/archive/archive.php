<?php
session_start();

// userNameがセッションにセットされていない場合、ログイン画面にリダイレクト
if (!isset($_SESSION['userName'])) {
    header("Location: ../login.php");
    exit();
}

// HTMLタグの入力を無効にし、文字コードをutf-8にする関数
function h($v){
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

$ARCHIVE_FILE = 'archive.txt'; // アーカイブ用のファイル
$ARCHIVE_BOARD = []; // アーカイブ内のタスクを保持する配列

// archive.txtファイルが存在する場合にデータを読み込む
if(file_exists($ARCHIVE_FILE)) {
    $ARCHIVE_BOARD = json_decode(file_get_contents($ARCHIVE_FILE), true);
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="content-type" charset="utf-8">
    <title>My ToDo - ArchiveList</title>
    <link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
<div style="text-align: center; margin: auto;">
    <h1>My ToDo</h1>
    
    <section class="main">
        <h2>過去のタスク</h2>
<a href="./index.php">←タスク一覧へ戻る</a>
        <!--アーカイブされたタスクのループ-->
        <?php if (!empty($ARCHIVE_BOARD)): ?>
        <table style="border-collapse: collapse">
            <?php foreach($ARCHIVE_BOARD as $DATA): ?>
            <tr>
                <td>
                    <!--テキスト-->
                    <span>
                        <?php echo h($DATA[2]); ?> <!-- タスク内容 -->
                    </span>
                </td>
                <td>
                    <!--日時-->
                    <?php echo h($DATA[1]); ?> <!-- 日時 -->
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
            <p>過去のタスクはありません</p>
        <?php endif; ?>
    </section>
</div>
</body>
</html>