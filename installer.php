<?php
// GitHubリポジトリ情報
$user = 'nekogakure';
$repo = 'My-ToDo';

// GitHub API URL for the latest release
$apiUrl = "https://api.github.com/repos/$user/$repo/releases/latest";

// cURLを使ってAPIから最新リリース情報を取得
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'PHP'); // User-Agentの設定が必要
$response = curl_exec($ch);
if ($response === false) {
    die('最新バージョン情報の取得に失敗しました: ' . curl_error($ch));
}
curl_close($ch);

// 最新リリース情報をデコード
$releaseData = json_decode($response, true);
if (!isset($releaseData['zipball_url'])) {
    die('最新バージョンの取得に失敗しました。');
}

// ZIPファイルのURL
$zipUrl = $releaseData['zipball_url'];

// ダウンロード先のパス
$zipFile = __DIR__ . '/My-ToDo-latest.zip';

// 解凍先の一時ディレクトリ
$tempExtractPath = __DIR__ . '/My-ToDo-temp';

// ZIPファイルをcURLでダウンロード
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $zipUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // リダイレクトを許可
curl_setopt($ch, CURLOPT_USERAGENT, 'PHP'); // 再度User-Agentを設定
$fileData = curl_exec($ch);
if ($fileData === false) {
    die('ZIPファイルのダウンロードに失敗しました: ' . curl_error($ch));
}
curl_close($ch);

// ダウンロードしたデータをZIPファイルに保存
if (file_put_contents($zipFile, $fileData) === false) {
    die('ZIPファイルの保存に失敗しました。');
}

// ZIPファイルを解凍
$zip = new ZipArchive;
if ($zip->open($zipFile) === TRUE) {
    $zip->extractTo($tempExtractPath);
    $zip->close();
    echo 'ZIPファイルの解凍に成功しました。';
} else {
    die('ZIPファイルの解凍に失敗しました。');
}

// 解凍後のディレクトリを取得（通常は1つだけのディレクトリが生成される）
$extractedDir = glob($tempExtractPath . '/*', GLOB_ONLYDIR)[0] ?? null;
if (!$extractedDir || !is_dir($extractedDir)) {
    die('解凍されたディレクトリが見つかりませんでした。');
}

// 解凍されたファイルを現在のディレクトリに移動
foreach (scandir($extractedDir) as $file) {
    if ($file === '.' || $file === '..') {
        continue;
    }
    rename($extractedDir . '/' . $file, __DIR__ . '/' . $file);
}

// 一時ディレクトリを削除
function deleteDirectory($dir) {
    if (!is_dir($dir)) return;
    foreach (scandir($dir) as $file) {
        if ($file === '.' || $file === '..') continue;
        $filePath = $dir . '/' . $file;
        is_dir($filePath) ? deleteDirectory($filePath) : unlink($filePath);
    }
    rmdir($dir);
}

deleteDirectory($tempExtractPath);

// ZIPファイルを削除（不要な場合）
unlink($zipFile);

echo 'ファイルの移動とクリーンアップが完了しました。正常にインストールが完了しました。';
