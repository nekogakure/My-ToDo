<?php

function sendLine($message, $taskContent)
{
/*    $line_api_url = 'https://api.line.me/v2/bot/message/push';
    $line_token = 'YOUR_ACCESS_TOKEN'; // LINEのチャネルアクセストークンを設定

    // グループIDを読み込む
    $groupId = 'YOUR_GROUP_ID'; // ここにグループIDを記入

    // 送信するメッセージデータの構築
    $data = [
        'to' => $groupId, // グループIDを指定
        'messages' => [
            [
                'type' => 'text',
                'text' => $message . '：' . $taskContent . '（https://mytodo.f5.si）'
            ]
        ]
    ];

    // ヘッダー情報
    $headers = [
        'Authorization: Bearer ' . $line_token,
        'Content-Type: application/json'
    ];

    // cURLを使ったリクエストの初期化
    $ch = curl_init($line_api_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // リクエストを実行
    $result = curl_exec($ch);

    // エラーチェック
    if (curl_errno($ch)) {
        echo 'エラー: ' . curl_error($ch);
    }

    // cURLの終了
    curl_close($ch);*/
}