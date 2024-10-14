<?php

// HTMLタグの入力を無効にし、文字コードをutf-8にする
function h($v){
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

include('./WEBHOOK/index.html');

// 変数の準備
$FILE = 'todo.txt'; // 保存ファイル名
$ARCHIVE_FILE = 'archive.txt'; // アーカイブ用のファイル

$id = uniqid(); // ユニークなIDを自動生成

// タイムゾーン設定
date_default_timezone_set('Japan');
$date = date('Y年m月d日H時i分'); // 日時（年/月/日/ 時:分）

$text = ''; // 入力テキスト
$comment = ''; // コメント
$commentIndex = ''; // 削除するコメントのインデックス

$DATA = []; // 一回分の投稿の情報を入れる

$BOARD = []; // 全ての投稿の情報を入れる

// $FILEというファイルが存在しているとき
if(file_exists($FILE)) {
    // ファイルを読み込む
    $BOARD = json_decode(file_get_contents($FILE), true); // trueで配列として取得
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // 新規タスク追加
    if(!empty($_POST['txt'])){
        $text = $_POST['txt'];
        // 新規データ
        $DATA = [$id, $date, $text, false, []];
        $BOARD[] = $DATA;
        // todo.txtに保存
        file_put_contents($FILE, json_encode($BOARD));
        // archive.txtにも保存
        $archiveData = $DATA; // 同じデータを使う
        $archiveBoard = [];
        if(file_exists($ARCHIVE_FILE)) {
            $archiveBoard = json_decode(file_get_contents($ARCHIVE_FILE), true);
        }
        $archiveBoard[] = $archiveData;
        file_put_contents($ARCHIVE_FILE, json_encode($archiveBoard));
        sendLine("新規タスクが追加されました。", $text); // タスクの内容も一緒に送信
    }

    
    if(isset($_POST['del'])){
        $NEWBOARD = [];
        foreach($BOARD as $DATA){
            if($DATA[0] !== $_POST['del']){
                $NEWBOARD[] = $DATA;
            }
        }
        file_put_contents($FILE, json_encode($NEWBOARD));
        sendLine("タスクが削除されました。", $DATA[2]); // 削除されたタスクの内容を送信
    }

    if(isset($_POST['done'])){
        foreach($BOARD as &$DATA){ // $DATAは参照で扱う
            if($DATA[0] === $_POST['done']){
                $DATA[3] = !$DATA[3]; // 完了状態を反転させる

                // 完了または未完了のメッセージを送信
                if($DATA[3]){
                    sendLine("タスクが完了しました。", $DATA[2]); // 完了メッセージ
                } else {
                    sendLine("タスクが未完了に戻されました。", $DATA[2]); // 未完了に戻されたメッセージ
                }
            }
        }
        file_put_contents($FILE, json_encode($BOARD));
    }

    // コメントの追加処理
    if(isset($_POST['comment_text']) && isset($_POST['comment_id'])){
        $comment = $_POST['comment_text'];
        $comment_id = $_POST['comment_id'];
        foreach($BOARD as &$DATA){
            if($DATA[0] === $comment_id){
                if(!isset($DATA[4])) {
                    $DATA[4] = []; // コメント配列が存在しない場合は空の配列を初期化
                }
                $DATA[4][] = $comment; // コメントを追加
            }
        }
        file_put_contents($FILE, json_encode($BOARD));
    }

    // コメントの削除処理
    if(isset($_POST['comment_delete_id']) && isset($_POST['comment_index'])){
        $comment_delete_id = $_POST['comment_delete_id'];
        $comment_index = $_POST['comment_index'];
        foreach($BOARD as &$DATA){
            if($DATA[0] === $comment_delete_id){
                if(isset($DATA[4][$comment_index])) {
                    unset($DATA[4][$comment_index]); // コメントを削除
                    $DATA[4] = array_values($DATA[4]); // インデックスをリセット
                }
            }
        }
        file_put_contents($FILE, json_encode($BOARD));
    }

    // ページをリダイレクトしてリロード
    header('Location: '.$_SERVER['SCRIPT_NAME']);
    exit;
}
?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="content-type" charset="utf-8">
    <title>My ToDo</title>
    <link rel="stylesheet" href="style.css" type="text/css">
    <script>
        // 削除の確認ダイアログ
        function confirmDelete() {
            return confirm("本当に削除してもよろしいですか？");
        }

        // 完了の確認ダイアログ
        function confirmComplete() {
            return confirm("");
        }

        // コメントの展開・折りたたみ
        function toggleComments(id) {
            var comments = document.getElementById('comments-' + id);
            var button = document.getElementById('toggle-button-' + id);
            if (comments.style.display === 'none') {
                comments.style.display = 'block';
                button.innerHTML = '▽ コメントを隠す';
            } else {
                comments.style.display = 'none';
                button.innerHTML = '▶ コメントを表示';
            }
        }
    </script>
</head>
<body>
<div style="text-align: center; margin: auto;">
    <h1>My ToDo</h1>
    
    <section class="main">
        <h2>ToDoにデータを追加する</h2>
        <!--投稿-->
        <form method="post">
            <input type="text" name="txt">
            <input type="submit" value="投稿">
        </form>    
<p>過去のタスクは<a href="./archive.php">ココ</a>からみることができます。</p>
    </section>
    <section class="main">
<!--        <table style="border-collapse: collapse">  -->
        <!--投稿のループ-->
        <?php if (!empty($BOARD)): ?>
        <table style="border-collapse: collapse">
            <?php foreach($BOARD as $DATA): ?>
            <tr>
                <td>
<h1>　</h1>
<section class="main">
                    <!--テキスト（完了したタスクには線を引く）-->
                    <span style="<?php echo $DATA[3] ? 'text-decoration: line-through;' : ''; ?>">
                        <?php echo h($DATA[2]); ?>
                    </span>
                </td>
                <td>
                    <!--日時-->
                    <?php echo $DATA[1]; ?>
                </td>
                <td>
                    <!--削除ボタン-->
                    <form method="post" style="display:inline;" onsubmit="return confirmDelete();">
                        <input type="hidden" name="del" value="<?php echo $DATA[0]; ?>">
                        <input type="submit" value="削除" class="delete">
                    </form>
                </td>
                <td>
                    <!--完了ボタン-->
                    <form method="post" style="display:inline;" onsubmit="return confirmComplete();">
                        <input type="hidden" name="done" value="<?php echo $DATA[0]; ?>">
                        <input type="submit" value="<?php echo $DATA[3] ? '完了しました' : '未完了です'; ?>" class="complete">
                    </form>
                </td>
                <td>
                    <!--コメントフォーム-->
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="comment_id" value="<?php echo $DATA[0]; ?>">
                        <input type="text" name="comment_text" placeholder="コメントを追加">
                        <input type="submit" value="コメント追加">
                    </form>
                </td>
            </tr>
            <!-- コメント表示のトグルボタン -->
            <tr>
                <td colspan="5">
                    <button id="toggle-button-<?php echo $DATA[0]; ?>" onclick="toggleComments('<?php echo $DATA[0]; ?>')">▶ コメントを表示</button>
                </td>
            </tr>
<?php echo "　"; ?>
            <!-- コメントリスト -->
            <tr id="comments-<?php echo $DATA[0]; ?>" style="display: none;">
                <td colspan="5">
                    <ul>
                        <?php if (isset($DATA[4]) && is_array($DATA[4])): ?>
                            <?php foreach($DATA[4] as $index => $comment): ?>
                                <li>
                                    <?php echo h($comment); ?>
                                    <!-- コメント削除フォーム -->
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="comment_delete_id" value="<?php echo $DATA[0]; ?>">
                                        <input type="hidden" name="comment_index" value="<?php echo $index; ?>">
                                        <input type="submit" value="削除" onclick="return confirm('このコメントを削除しますか？');">
                                    </form>
                                </li>
    <h1>   </h1>
                            <?php endforeach; ?>
                        <?php else: ?>
                            
                        <?php endif; ?>
                    </ul>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <p>タスクがありません</p>
        <?php endif; ?>
        </table>
    </section>
</div>
</body>
</html>
