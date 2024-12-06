<?php
ini_set('display_errors', 0);
error_reporting(0);
include('header.php')
// セッションを開始
session_start();

// セッションのデータをすべて削除
$_SESSION = [];

// セッションのクッキーを削除
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// セッションを完全に破棄
session_destroy();

// 指定されたURLにリダイレクト
header("Location: "GetPageURL());
exit();
