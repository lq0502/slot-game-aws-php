<?php
// エラー表示
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: text/plain; charset=UTF-8');

session_start();
require 'db.php';

// PHP が動作しているか確認
echo "PHP is working.\n";

// POSTのみを受け付ける
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     // スコアとユーザーIDを取得
    $score = intval($_POST['score'] ?? 0);
    $user_id = $_SESSION['user_id'] ?? null;

    // ログイン済みかつスコアが有効か確認
    if ($user_id && $score > 0) {
        try {
            $stmt = $pdo->prepare("INSERT INTO scores (user_id, score) VALUES (?, ?)");
            $stmt->execute([$user_id, $score]);
            echo "スコアを保存しました: $score\n";
        } catch (PDOException $e) {
            echo "❌ データベース保存エラー: " . $e->getMessage();
        }
    } else {
        echo "❌ ログインしていないか、スコアが無効です";
    }
} else {
    echo "❌ 無効なリクエスト方式（POSTで送信してください）";
}
