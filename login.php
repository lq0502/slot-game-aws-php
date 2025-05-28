<?php
// エラーメッセージを表示する（デバッグ用）
ini_set('display_errors', 1);
error_reporting(E_ALL);
// ログイン状態を管理する
session_start();
// データベース接続ファイルを読み込む
require 'db.php';

// ユーザーへのメッセージ初期化
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

      // ユーザー名とパスワードの一致を確認（SHA256でハッシュ化）
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND password = SHA2(?, 256)");
       $stmt->execute([$username, $password]);
       $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $username;
        header('Location: index.php'); // 登录成功后跳转到游戏
        exit;
    } else {
        // 認証失敗
        $message = "❌ ユーザー名またはパスワードが間違っています";
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>ユーザーログイン</title>
    <style>
        body { background-color: #121212; color: white; font-family: Arial; text-align: center; }
        form { margin-top: 50px; }
        input { margin: 10px; padding: 8px; }
        .msg { margin-top: 20px; color: #FFD700; }
    </style>
</head>
<body>
<h1>アカウントにログイン</h1>

<!-- 登録成功時のメッセージ -->
<?php if (isset($_GET['registered'])): ?>
    <div class="msg">✅ 登録が完了しました。ログインしてください。</div>
<?php endif; ?>

<form method="POST">
    <input name="username" placeholder="ユーザー名" required><br>
    <input name="password" type="password" placeholder="パスワード" required><br>
    <button type="submit">ログイン</button>
</form>
<div class="msg"><?= htmlspecialchars($message) ?></div>
</body>
</html>
