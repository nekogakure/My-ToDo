<?php
$inputData = file_get_contents('php://input');
$events = json_decode($inputData, true);

// イベントが存在するか確認
if (isset($events['events'])) {
    foreach ($events['events'] as $event) {
        // イベントタイプがメッセージであり、グループの情報がある場合
        if ($event['type'] === 'message' && isset($event['source']['groupId'])) {
            $groupId = $event['source']['groupId'];

            // グループIDをid.txtに保存
            file_put_contents('id.txt', $groupId . ' <-これがグループIDです。');
            echo "Group ID has been saved.";
            break; // 最初のグループIDだけ保存して終了
        }
    }
} else {
    echo "No events found.";
}