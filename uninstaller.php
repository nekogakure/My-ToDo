<?php
// 削除対象のファイルを定義
$excludedFiles = [
    'todo.txt',
    'archive.txt',
    '.htaccess',
    'favicon.ico'
];

// インストール先のパス（このスクリプトがある階層）
$installDir = __DIR__ . '/';

// アンインストール処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'このソフトウェアを削除します') {
        // フォルダを削除する関数
        function deleteFiles($dir, $excludedFiles) {
            $files = array_diff(scandir($dir), ['.', '..']);

            foreach ($files as $file) {
                $filePath = $dir . $file;

                // 除外リストに含まれているファイルはスキップ
                if (in_array($file, $excludedFiles)) {
                    continue;
                }

                if (is_dir($filePath)) {
                    // ディレクトリの場合は再帰的に削除
                    deleteFiles($filePath . '/', $excludedFiles);
                    rmdir($filePath); // 空になったディレクトリを削除
                } else {
                    unlink($filePath); // ファイルを削除
                }
            }
        }

        // アプリケーションのデータを削除
        deleteFiles($installDir, $excludedFiles);

        // 完了メッセージを表示
        echo "<!DOCTYPE html>
        <html lang='ja'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>アンインストール完了</title>
        </head>
        <body>
            <h1>アンインストールが完了しました！</h1>
            <p>すべてのデータが削除されました。</p>
            <a href='./'>戻る</a>
        </body>
        </html>";
        exit();
    } else {
        // 入力が間違っている場合のエラーメッセージ
        $errorMessage = "正しいテキストを入力してください。";
    }
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>アンインストール</title>
</head>
<body>
    <h1>アンインストール</h1>
    <p>このソフトウェアを削除するには、以下のテキストを入力してください：</p>
    <p><strong>このソフトウェアを削除します</strong></p>

    <?php if (isset($errorMessage)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($errorMessage); ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="confirm" required>
        <button type="submit">アンインストール</button>
    </form>
</body>
</html>
