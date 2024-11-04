# My ToDo
これはねこがくれが作成した、簡単かつ軽量なToDoソフトです。簡易的ながらも、LINEと連携させることができ、通知を送信することもできます。

FAQ:[こちら](https://github.com/nekogakure/My-ToDo/blob/main/src/FAQ.md)です。

## インストール
### インストーラーを使用する場合
[インストーラー](https://github.com/nekogakure/My-ToDo/releases/tag/Installer)をインストールしたいサーバーのディレクトリで実行します。
> Apacheの場合、ドキュメントルートの/var/www/htmlです

>./todoディレクトリに設置した場合、https://example.com/todo/installer.php にアクセス
これだけで、インストールを自動で行ってくれます。

### それ以外の方法
```
$cd <サーバーのディレクトリ>
$git clone https://github.com/nekogakure/My-ToDo.git todo
```
を実行して、ダウンロードします

## 使い方
**重要：LINE Notifyのサービス廃止に基づき、version1.2.0からLINE Notifyは使用できなくなりました。そのため、過去のバージョンは2025年頃までしか使えません**

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
でApacheを再起動してください。そして、

5. LINE公式アカウントを開設する（各自調べてください）

6. LINE Messaging APIを使用するためのプロパイダを作成する

7. WEBHOOK URLに https://ドメイン/WEBHOOK/webhook.php を指定する。（SSLがないとエラー吐きます。また、ベーシック認証をかけている場合はWEBHOOKディレクトリのみをBasic認証から外してください。その際、./index.phpのincludeの部分も適時書き換えてください。）

8. 通知を送信したいグループに先ほど作った公式アカウントを追加する（設定の「トークルームへの参加を許可する」をオンにしてください」

9. 適当に、「あああ」などとグループにメッセージを送る。

10. ./WEBHOOKにid.txtが生成されるのでコピペする
    
11. ./WEBHOOK/index.phpのYOUR_GROUP_IDを先程のグループIDに書き換える
    
12. MessagengAPIのトークンを生成する（ついでにWEBHOOK URLをhttps://ドメイン/WEBHOOK/index.php に書き換えておく（データ圧迫防止）
13. ./index.phpのYOUR_TOKENを先程のトークンに変える。
    
14. サイトにアクセスしてタスクを追加すると、 「タスクが追加されました：タスクの名前 （URL）」と送られてくるはずです。

わからないことがある場合、気軽にissuesで聞いてください。できる限りサポートします。「必ず」ではありません（私も中学生なので）

## 機能
- タスクの追加
- タスクの削除
- タスクにコメントをつける
- タスクの完了、未完了が一目でわかる
- タスクを削除してもarchive.phpでは全てのデータが残っている
- LINE MessagingAPIを使用してLINEに通知を送信する
- アカウント登録、ログイン（./registerにBasic認証をかけるとより良いです）
- （新機能）タスクにタグを追加する
- （新機能）タスクを検索する

## 既知の不具合、虚弱性
- 削除したタスクと違うタスクが削除された、と通知されることがある（実際は削除されていない）：未修正
- コメント投稿時、時々ブラウザがクラッシュする：***修正済***
- タスクを削除時、todo.txt（タスク保存ファイル）では反映されているが、見かけ反映されない：未修正
- アップデートした際、チャネルトークンとグループIDが削除される：未修正
- 一つだけしかアカウントを作れない：未修正

## 今後実装する予定の機能
- LINEアカウント、Googleアカウントでログイン
- アカウントとtodoの紐づけ

## LICENSE
[Apache 2.0](https://github.com/nekogakure/My-ToDo/blob/main/LICENSE)
