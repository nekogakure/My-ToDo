# よくある質問、バグ

## エラー
#### READMEのような画面が表示されず、phpがプレーンテキストとして表示される
PHPのモジュールがインストールされていません。
```
$ sudo apt install -y libapache2-mod-php
```
を実行してください。その後サーバーを再起動してください

#### installer.phpの10行目でエラーが出る
cURLがインストールされていません。
```
$ sudo apt-get install php-curl
```
を実行してください。その後サーバーを再起動してください

#### installer.phpの12行目でエラーが出る
zipArchiveがインストールされていません。
```
$ sudo apt-get install php-zip
```
を実行してください。その後サーバーを再起動してください
