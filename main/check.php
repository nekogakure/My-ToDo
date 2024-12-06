<?php
ini_set('display_errors', 0);
error_reporting(0);
function version_check($user_dir) {
    // パスの設定
    $main_config_path = '../../main/config.json';
    $user_config_path = __DIR__ . '/config.json';

    // ファイルの存在確認
    if (!file_exists($main_config_path) || !file_exists($user_config_path)) {
        throw new Exception('config.jsonが見つかりません。');
    }

    // JSONの読み込み
    $main_config = json_decode(file_get_contents($main_config_path), true);
    $user_config = json_decode(file_get_contents($user_config_path), true);

    // JSONのバージョンキーが存在するか確認
    if (!isset($main_config['version']) || !isset($user_config['version'])) {
        throw new Exception('config.jsonにバージョン情報が含まれていません。');
    }

    // バージョン比較
    if ($main_config['version'] !== $user_config['version']) {
        $new_version = $main_config['version'];
        echo <<<HTML
<script>

    if (confirm("新しいMy ToDoのバージョン($new_version)が使用可能です。今すぐ更新しますか？（詳しいアップデート内容はコチラ→ https://github.com/nekogakure/My-ToDo/releases/）")) {
            window.location.href = 'updater.php';
    }
</script>
HTML;
    }
}
?>