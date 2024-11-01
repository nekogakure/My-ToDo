<?php
// config.jsonファイルのパス
$configFile = __DIR__ . '/../config.json';

// config.jsonを読み込んで現在のバージョンを取得
if (!file_exists($configFile)) {
    echo "config.jsonが見つかりません。\n";
    exit;
}

$config = json_decode(file_get_contents($configFile), true);
if ($config === null || !isset($config['version'])) {
    echo "config.jsonの内容が不正です。\n";
    exit;
}

$currentVersion = $config['version'];

// GitHubリポジトリ情報
$owner = 'nekogakure';
$repo = 'My-ToDo';
$releasesUrl = "https://github.com/$owner/$repo/releases/latest";

// 最新リリースのページからリリース情報を取得
$options = [
    'http' => [
        'header' => 'User-Agent: PHP'
    ]
];
$context = stream_context_create($options);
$response = @file_get_contents($releasesUrl, false, $context);

if ($response === false) {
    echo "リリースページの取得に失敗しました。\n";
    exit;
}

// リリースページからバージョンを抽出
preg_match('/\/' . $owner . '\/' . $repo . '\/releases\/tag\/v?([\d.]+)/', $response, $matches);
if (!isset($matches[1])) {
    echo "最新バージョンの取得に失敗しました。\n";
    exit;
}

$latestVersion = $matches[1];

if (version_compare($latestVersion, $currentVersion, '>')) {
    echo "新しいバージョンが見つかりました: " . $latestVersion . "\n";

    // ZIPアーカイブのURLを組み立てる
    $zipUrl = "https://github.com/$owner/$repo/archive/refs/tags/v$latestVersion.zip";
    $zipFile = __DIR__ . '/update.zip';

    // ZIPファイルをダウンロード
    $zipContent = @file_get_contents($zipUrl);
    if ($zipContent === false) {
        echo "ZIPファイルのダウンロードに失敗しました。\n";
        exit;
    }
    file_put_contents($zipFile, $zipContent);

    // ZIPファイルを解凍
    $zip = new ZipArchive;
    if ($zip->open($zipFile) === TRUE) {
        $extractPath = __DIR__ . '/update';
        $zip->extractTo($extractPath);
        $zip->close();
        echo "アップデートファイルを解凍しました。\n";
    } else {
        echo "ZIPファイルの解凍に失敗しました。\n";
        exit;
    }

    // 解凍したディレクトリの確認
    if (!is_dir($extractPath)) {
        echo "解凍先のディレクトリが存在しません。\n";
        exit;
    }

    // 更新先のディレクトリを設定（`updater`の一つ上のディレクトリ）
    $updateTargetDir = realpath(__DIR__ . '/..');
    $arcTodoDir = $updateTargetDir . '/arc_todo';

    // 古いファイルをarc_todoに移動
    if (!file_exists($arcTodoDir)) {
        mkdir($arcTodoDir, 0777, true);
    }

    // アップデートするファイルを除外するリスト
    $excludedFiles = ['todo.txt', 'archive.txt', '.htaccess', '.user.ini'];

    // 解凍したディレクトリの中身をarc_todoに移動し、新しいファイルをコピー
    $extractedDir = glob($extractPath . '/*', GLOB_ONLYDIR);
    if (empty($extractedDir)) {
        echo "解凍したディレクトリが見つかりません。\n";
        exit;
    }
    moveAndReplaceFiles($extractedDir[0], $updateTargetDir, $arcTodoDir, $excludedFiles);

    // バージョン情報をconfig.jsonに更新
    $config['version'] = $latestVersion;
    file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "config.jsonを更新しました。\n";

    // 一時ファイルの削除
    if (file_exists($zipFile)) {
        unlink($zipFile);
    }
    deleteDirectory($extractPath);
    echo "アップデートが完了しました。\n";
} else {
    echo "最新バージョンです。\n";
}

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
                if (!rename($destPath, $backupPath)) {
                    echo "ファイルの移動に失敗しました: $destPath\n";
                }
            }

            if (is_dir($sourcePath)) {
                moveAndReplaceFiles($sourcePath, $destPath, $backupDir . '/' . $file, $excludedFiles);
            } else {
                if (!copy($sourcePath, $destPath)) {
                    echo "ファイルのコピーに失敗しました: $sourcePath\n";
                }
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
