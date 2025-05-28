<?php
// データベース接続設定
$host = 'localhost';
$db = 'slot_game';
$user = 'slotuser';// MySQLユーザー名
$pass = '123456'; // そのパスワード

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
} catch (PDOException $e) {
    // 接続失敗時のエラーメッセージ
    die("データベース接続に失敗しました: " . $e->getMessage());
}
?>