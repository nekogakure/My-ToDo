<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // GitHubリポジトリ情報
    $owner = 'nekogakure';
    $repo = 'My-ToDo';
    $releasesUrl = "https://github.com/$owner/$repo/releases/latest";

    // インストール先のパス（このスクリプトがある階層）
    $installTargetDir = __DIR__ . '/';
    $arcTodoDir = $installTargetDir . 'arc_todo';

    // 最新リリースのページからリリース情報を取得
    $options = [
        'http' => [
            'header' => 'User-Agent: PHP'
        ]
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($releasesUrl, false, $context);

    // リリースページからバージョンを抽出
    preg_match('/\/' . $owner . '\/' . $repo . '\/releases\/tag\/v?([\d.]+)/', $response, $matches);
    if (!isset($matches[1])) {
        echo "最新バージョンの取得に失敗しました。\n";
        exit;
    }

    $latestVersion = $matches[1];
    echo "最新バージョン: " . htmlspecialchars($latestVersion) . "<br>";

    // ZIPアーカイブのURLを組み立てる
    $zipUrl = "https://github.com/$owner/$repo/archive/refs/tags/v$latestVersion.zip";
    $zipFile = $installTargetDir . 'update.zip';

    // ZIPファイルをダウンロード
    $zipContent = file_get_contents($zipUrl);
    if ($zipContent === false) {
        echo "ZIPファイルのダウンロードに失敗しました。\n";
        exit;
    }
    file_put_contents($zipFile, $zipContent);

    // ZIPファイルを解凍
    $zip = new ZipArchive;
    if ($zip->open($zipFile) === TRUE) {
        $extractPath = $installTargetDir . 'update';
        $zip->extractTo($extractPath);
        $zip->close();
        echo "アップデートファイルを解凍しました。\n";
    } else {
        echo "ZIPファイルの解凍に失敗しました。\n";
        exit;
    }

    // arc_todoディレクトリを作成
    if (!file_exists($arcTodoDir)) {
        mkdir($arcTodoDir, 0777, true);
    }

    // アップデートするファイルを除外するリスト
    $excludedFiles = ['todo.txt', 'archive.txt', '.htaccess', '.user.ini'];

    // 解凍したディレクトリの中身をarc_todoに移動し、新しいファイルをコピー
    $extractedDir = glob($extractPath . '/*', GLOB_ONLYDIR)[0];
    moveAndReplaceFiles($extractedDir, $installTargetDir, $arcTodoDir, $excludedFiles);

    // バージョン情報をconfig.jsonに更新
    $configFile = $installTargetDir . 'config.json';
    $config = [
        'version' => $latestVersion,
    ];
    file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "config.jsonを更新しました。\n";

    // 一時ファイルの削除
    if (file_exists($zipFile)) {
        unlink($zipFile);
    }
    deleteDirectory($extractPath);
    
    // インストーラーを無効にするために内容を消去
    file_put_contents(__FILE__, '');
    
    // Updater/index.phpにリダイレクト
    header('Location: ./Updater/index.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Install.css">
    <title>MyToDo Installer</title>
</head>
<body>
    <div class="installer-container">
        <h1>MyToDo インストーラー</h1>
        <p>このプログラムをインストールする準備が整いました。以下のボタンを押してインストールを開始してください。</p>
        <form method="POST">
            <button type="submit" class="install-button">インストールする</button>
        </form>
    </div>
</body>
</html>

<?php
/**
 * ディレクトリを再帰的に移動し、ファイルを置き換える関数
 */
function moveAndReplaceFiles($source, $destination, $backupDir, $excludedFiles) {
    $dir = opendir($source);
    @mkdir($destination);

    while (($file = readdir($dir)) !== false) {
        if (($file != '.') && ($file != '..')) {
            $sourcePath = $source . '/' . $file;
            $destPath = $destination . '/' . $file;
            $backupPath = $backupDir . '/' . $file;

            // 除外リストに含まれているファイルはスキップ
            if (in_array($file, $excludedFiles)) {
                continue;
            }

            // 既存のファイルをarc_todoに移動
            if (file_exists($destPath)) {
                rename($destPath, $backupPath);
            }

            if (is_dir($sourcePath)) {
                moveAndReplaceFiles($sourcePath, $destPath, $backupDir . '/' . $file, $excludedFiles);
            } else {
                copy($sourcePath, $destPath);
            }
        }
    }
    closedir($dir);
}

/**
 * ディレクトリを再帰的に削除する関数
 */
function deleteDirectory($dir) {
    if (!is_dir($dir)) {
        return;
    }
    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        $filePath = "$dir/$file";
        if (is_dir($filePath)) {
            deleteDirectory($filePath);
        } else {
            unlink($filePath);
        }
    }
    rmdir($dir);
}
