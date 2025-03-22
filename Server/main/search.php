<?php
include('../../header.php');
?>

<?php
session_start();
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

// ログインしていない場合、login.php にリダイレクト
if (!isset($_SESSION['userName'])) {
    header('Location: ../../index.php');
    exit;
}

$current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$base_url = "https://mytodo.f5.si";

// ベースURLを取り除いた部分を取得
$access_dir = str_replace($base_url, "", $current_url);

// ファイル名部分を取り除く
$access_dir = dirname($access_dir);

// $ACCESS_DIR に格納
$ACCESS_DIR = rtrim($access_dir, '/'); // 最後のスラッシュを削除

//echo "SESSION DIR: " . $_SESSION['dir'] . "<br>";
//echo "ACCESS DIR: " . $ACCESS_DIR . "<br>";

// アクセス権の確認
if (!($_SESSION['dir'] == $ACCESS_DIR)) {
    die("アクセス権がありません");
}


function h($v) {
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

$FILE = 'todo.txt';
$BOARD = [];
if (file_exists($FILE)) {
    $BOARD = json_decode(file_get_contents($FILE), true);
}

$query = isset($_GET['q']) ? $_GET['q'] : '';
$results = [];

if ($query !== '') {
    foreach ($BOARD as $DATA) {
        // タグが存在し、型を確認
        $tags = isset($DATA[4]) && is_array($DATA[4]) ? $DATA[4] : [];
        $category = isset($DATA[5]) ? $DATA[5] : '';

        // タイトルまたはタグ、カテゴリに検索クエリが一致するか
        if (stripos($DATA[2], $query) !== false || in_array($query, $tags) || stripos($category, $query) !== false) {
            $results[] = $DATA;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="content-type" charset="utf-8">
    <title>MyToDo - 検索結果</title>
    <link rel="stylesheet" href="style.css" type="text/css">
    <script>
        function confirmDelete() {
            return confirm("本当に削除してもよろしいですか？");
        }
    </script>
</head>
<body>
<div style="text-align: center; margin: auto;">
    <h1>My ToDo</h1>

    <section class="main">
    <h3>検索</h3>
    <form method="get" action="search.php">
        <input type="text" name="q" placeholder="タスクを検索...">
        <input type="submit" value="検索">
    </form>
    </section>

    <section class="main">
    <h2>検索結果</h2>
    <table style="border-collapse: collapse">

        <?php if (count($results) > 0): ?>
            <?php foreach ($results as $DATA): ?>
            <tr>
                <td style="<?php echo $DATA[3] ? 'text-decoration: line-through;' : ''; ?>"><?php echo h($DATA[2]); ?></td>
                <td><?php echo h($DATA[1]); ?></td>
                <td class="tag">
                    <?php
                    // タグが配列なら結合して表示
                    $tags = isset($DATA[4]) && is_array($DATA[4]) ? implode(', ', $DATA[4]) : 'タグなし';
                    echo h($tags);
                    ?>
                </td>
                <td><?php echo h($DATA[5]); // カテゴリの表示 ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">該当するタスクはありません。</td>
            </tr>
        <?php endif; ?>
    </table>
    <a href="./">↩︎戻る</a>
    </div>
    </section>
</body>
</html>