<?php
require 'db.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // ユーザー名とパスワードは最低3文字
    if (strlen($username) < 3 || strlen($password) < 3) {
        $message = "ユーザー名とパスワードは3文字以上必要です";
    } else {
        $hashed = hash('sha256', $password);

        try {
            // データベースに登録
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->execute([$username, $hashed]);

            // 成功したらログインページへ
            header('Location: login.php?registered=1');
            exit;
        } catch (PDOException $e) {
            // 重複ユーザー名などで失敗
            $message = "⚠️ 登録失敗：ユーザー名はすでに存在します";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ユーザー登録</title>
    <style>
        body { background-color: #121212; color: white; font-family: Arial; text-align: center; }
        form { margin-top: 50px; }
        input { margin: 10px; padding: 8px; }
        .msg { margin-top: 20px; color: #FFD700; }
    </style>
</head>
<body>
<h1>新規アカウント登録</h1>
<form method="POST">
    <input name="username" placeholder="ユーザー名" required><br>
    <input name="password" type="password" placeholder="パスワード" required><br>
    <button type="submit">登録</button>
</form>
<div class="msg"><?= htmlspecialchars($message) ?></div>
</body>
</html>
