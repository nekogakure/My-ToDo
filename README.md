# My ToDo
これはねこがくれが作成した、簡単かつ軽量なToDoソフトです。簡易的ながらも、LINEと連携させることができ、通知を送信することもできます。

![イメージ画像](https://github.com/nekogakure/My-ToDo/blob/main/src/image.png)

## インストール
[インストーラー](https://github.com/nekogakure/My-ToDo/releases/tag/Installer)をインストールしたいサーバーのディレクトリで実行します。

>./todoディレクトリに設置した場合、https://example.com/todo/installer.php にアクセス
これだけで、インストールを自動で行ってくれます。

## 使い方
**重要：LINE Notifyのサービス廃止に基づき、version1.2.0からLINE Notifyは使用できなくなりました。そのため、過去のバージョンは2025年頃までしか使えません**
1. LINE公式アカウントを開設する（各自調べてください）
2. LINE MessagengAPIを使用するためのプロパイダを作成する
3. WEBHOOK URLに https://ドメイン/WEBHOOK/webhook.php を指定する。（SSLがないとエラー吐きます。また、ベーシック認証をかけている場合はWEBHOOKディレクトリのみをBasic認証から外してください。その際、./index.phpのincludeの部分も適時書き換えてください。）
4. 通知を送信したいグループに先ほど作った公式アカウントを追加する（設定の「トークルームへの参加を許可する」をオンにしてください」
5. 適当に、「あああ」などとグループにメッセージを送る。
6. ./WEBHOOKにid.txtが生成されるのでコピペする
7. ./WEBHOOK/index.phpのYOUR_GROUP_IDを先程のグループIDに書き換える
8. MessagengAPIのトークンを生成する（ついでにWEBHOOK URLをhttps://ドメイン/WEBHOOK/index.php に書き換えておく（データ圧迫防止）
9. ./index.phpのYOUR_TOKENを先程のトークンに変える。
10. サイトにアクセスしてタスクを追加すると、 「タスクが追加されました：タスクの名前 （URL）」と送られてくるはずです。

たったこれだけで、だれでもToDoアプリを使用することができます。また、これだと誰でもアクセスできてしまうのでBasic認証をかけることで擬似的にアカウントを作れます。

## 機能
- タスクの追加
- タスクの削除
- タスクにコメントをつける
- タスクの完了、未完了が一目でわかる
- （新機能）タスクを削除してもarchive.phpでは全てのデータが残っている
- （改善）LINE NotifyからLINE MessagingAPIに変更
- アカウント登録、ログイン（登録するファイルにだけBasic認証をかけるとより良いです）

## 既知の不具合、虚弱性
- 削除したタスクと違うタスクが削除された、と通知されることがある（実際は削除されていない）
- コメント投稿時、時々ブラウザがクラッシュする

## LICENSE
[Apache 2.0](https://github.com/nekogakure/My-ToDo/blob/main/LICENSE)
