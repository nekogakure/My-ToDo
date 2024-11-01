<?php
session_start();
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

// ログインしていない場合、login.php にリダイレクト
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
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
        // タグが存在し、配列であることを確認
        $tags = isset($DATA[5]) ? $DATA[5] : [];

        if (stripos($DATA[2], $query) !== false || (is_array($tags) && in_array($query, $tags))) {
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
                <td class="tag"><?php echo isset($DATA[5]) ? h(implode(', ', $DATA[5])) : 'タグなし'; ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">該当するタスクはありません。</td>
            </tr>
        <?php endif; ?>
    </table>
    <a href="index.php">戻る</a>
</div>
 </section>
</body>
</html>
