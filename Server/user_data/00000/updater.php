<?php
function delete_non_txt_files($dir) {
    if (!is_dir($dir)) {
        throw new Exception("ディレクトリが見つかりません: $dir");
    }

    $files = scandir($dir);

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $filePath = $dir . DIRECTORY_SEPARATOR . $file;

        // ファイルまたはディレクトリの削除
        if (is_file($filePath) && pathinfo($filePath, PATHINFO_EXTENSION) !== 'txt') {
            unlink($filePath);
        } elseif (is_dir($filePath)) {
            // サブディレクトリを再帰的に削除
            delete_directory($filePath);
        }
    }
}

function delete_directory($dir) {
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        $filePath = $dir . DIRECTORY_SEPARATOR . $file;
        is_dir($filePath) ? delete_directory($filePath) : unlink($filePath);
    }
    return rmdir($dir);
}

function copy_directory($source, $destination) {
    if (!is_dir($source)) {
        throw new Exception("コピー元のディレクトリが見つかりません: $source");
    }

    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }

    $files = scandir($source);

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $srcPath = $source . DIRECTORY_SEPARATOR . $file;
        $destPath = $destination . DIRECTORY_SEPARATOR . $file;

        if (is_dir($srcPath)) {
            // 再帰的にディレクトリをコピー
            copy_directory($srcPath, $destPath);
        } else {
            copy($srcPath, $destPath);
        }
    }
}

try {
    // 操作対象ディレクトリの設定
    $targetDir = __DIR__; // 現在のディレクトリ
    $sourceDir = '../../main/'; // コピー元のディレクトリ

    // *.txt以外のファイルを削除
    delete_non_txt_files($targetDir);

    // ./mainの内容をコピー
    copy_directory($sourceDir, $targetDir);

    echo "アップデートが完了しました。";
} catch (Exception $e) {
    echo "エラー: " . $e->getMessage();
}
?>