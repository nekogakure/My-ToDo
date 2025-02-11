# My ToDo
これはねこがくれが作成した、簡単かつ軽量なToDoソフトです。簡易的ながらも、LINEと連携させることができ、通知を送信することもできます。

FAQ:[こちら](https://github.com/nekogakure/My-ToDo/blob/main/src/FAQ.md)です。

## インストールする
```
$cd <サーバーのディレクトリ>
$git clone https://github.com/nekogakure/My-ToDo.git todo
```
を実行して、ダウンロードします

## 使い方
**重要：LINE Notifyのサービス廃止に基づき、version1.2.0からLINE Notifyは使用できなくなりました。そのため、過去のバージョンは2025年夏頃までしか使えません**

### 対応OSおよびレンタルサーバー
- Ubuntu
- Debian
- RasberryPi OS
- Cloudfree
- Xserver

### セットアップ（Apache2）
1. PHPのモジュールをインストールします
   ```
   $ sudo apt install -y libapache2-mod-php
   ```
2. cURLをインストールします
   ```
   $ sudo apt-get install php-curl
   ```

3. ZipArchiveをインストールします
   ```
   $ sudo apt-get install php-zip
   ```

4. php.iniの確認をします
      - extension=zip
      - extension=curl
が存在するか、もしくはコメントアウトされていないか確認してください。その後、
   ```
   $ sudo systemctl restart apache2
   ```
でApacheを再起動してください。

5. admin.phpの4~7行目をコメントアウト

6. admin.phpにアクセスし、管理者のユザネ、パスワード、ディレクトリ名（なんでもいい）を記入し送信

7. admin.phpの4~7行目のコメントアウトをはずし、管理者のユーザー名のところを書き換える

#### ユーザー登録をする方法
1. admin.phpに管理者アカウントでログインしてアクセス

2. ユーザー名、パスワード、ディレクトリ名を入力

3. 送信

#### ソースコードのアップデート
mainディレクトリをアップデートしたら、自動で全体をアップデートしてくれます

### LINEへの通知機能を持たせる場合
1. 通知機能を持たせたいアカウントと紐づけているディレクトリの/WEBHOOKディレクトリのindex.phpのコメントアウトを外す

2. /WEBHOOKディレクトリのWEBHOOK.phpの一行目に`<?php`を追加する

3. LINE公式アカウントを開設する（各自調べてください）

4. LINE Messaging APIを使用するためのプロパイダを作成する

5. WEBHOOK URLに https://ドメイン/WEBHOOK/webhook.php を指定する。（SSLがないとエラー吐きます。また、ベーシック認証をかけている場合はWEBHOOKディレクトリのみをBasic認証から外してください。その際、./index.phpのincludeの部分も適時書き換えてください。）

6. 通知を送信したいグループに先ほど作った公式アカウントを追加する（設定の「トークルームへの参加を許可する」をオンにしてください」

7. 適当に、「あああ」などとグループにメッセージを送る。

8. ./WEBHOOKにid.txtが生成されるのでコピペする
    
9. ./WEBHOOK/index.phpのYOUR_GROUP_IDを先程のグループIDに書き換える
    
10. MessagengAPIのトークンを生成する（ついでにWEBHOOK URLをhttps://ドメイン/WEBHOOK/index.php に書き換えておく（データ圧迫防止）

11. ./index.phpのYOUR_TOKENを先程のトークンに変える。
    
12. サイトにアクセスしてタスクを追加すると、 「タスクが追加されました：タスクの名前 （URL）」と送られてくるはずです。

わからないことがある場合、気軽にissuesで聞いてください。できる限りサポートします。「必ず」ではありません（私も中学生なので）

## 機能
- タスクの追加
- タスクの削除
- タスクにコメントをつける
- タスクの完了、未完了が一目でわかる
- LINE MessagingAPIを使用してLINEに通知を送信する
- アカウント登録、ログイン
- タスクにタグを追加する
- タスクを検索する
- アカウントごとに違うタスクリストを保有

## 既知の不具合、虚弱性
- 削除したタスクと違うタスクが削除された、と通知されることがある（実際は削除されていない）：未修正
- タスクを削除時、todo.txt（タスク保存ファイル）では反映されているが、見かけ反映されない：***修正済***
- アップデートした際、チャネルトークンとグループIDが削除される：***修正済***
- 一つだけしかアカウントを作れない：***修正済***

このほかにバグや虚弱性が見つかった際はissesで報告よろしくお願いします

## 今後実装する予定の機能
- LINEアカウント、Googleアカウントでログイン

### 法的表示
[Apache 2.0](https://github.com/nekogakure/My-ToDo/blob/main/LICENSE)

このソフトウェアを使用したことによって生じた問題やトラブルなどに関して、私は一切責任を負いません。

©︎2024 nekogakure.
