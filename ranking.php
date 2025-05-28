<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require 'db.php';

$currentUserId = $_SESSION['user_id'];
$currentUsername = $_SESSION['username'];

$stmt = $pdo->query("
    SELECT users.id, users.username, SUM(scores.score) AS total_score, MAX(scores.created_at) AS latest_play
    FROM scores 
    JOIN users ON scores.user_id = users.id 
    GROUP BY users.id 
    ORDER BY total_score DESC, latest_play ASC
    LIMIT 10
");
$topScores = $stmt->fetchAll(PDO::FETCH_ASSOC);

$rankingStmt = $pdo->query("
    SELECT users.id, users.username, SUM(scores.score) AS total_score
    FROM scores 
    JOIN users ON scores.user_id = users.id 
    GROUP BY users.id 
    ORDER BY total_score DESC
");
$allScores = $rankingStmt->fetchAll(PDO::FETCH_ASSOC);

$myRank = null;
$myScore = null;
foreach ($allScores as $index => $row) {
    if ($row['id'] == $currentUserId) {
        $myRank = $index + 1;
        $myScore = $row['total_score'];
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>スコアランキング</title>
    <style>
        body { background-color: #121212; color: white; font-family: Arial; text-align: center; }
        table { margin: 40px auto; border-collapse: collapse; width: 70%; color: white; }
        th, td { border: 1px solid #FFD700; padding: 10px; }
        th { background-color: #FFD700; color: black; }
        tr.highlight { background-color: rgba(255, 215, 0, 0.3); }
        caption { font-size: 24px; margin: 20px; }
        .top-bar {
            position: fixed;
            top: 10px;
            right: 10px;
            background: rgba(255,255,255,0.1);
            padding: 10px;
            border-radius: 5px;
            z-index: 10001;
        }
        a { color: #FFD700; text-decoration: underline; }
    </style>
</head>
<body>
<div class="top-bar">
    ようこそ、<?= htmlspecialchars($currentUsername) ?> ｜ <a href="logout.php">ログアウト</a>
</div>

<caption>🎉 スコアランキング（トップ10）🎉</caption>
<table>
    <tr>
        <th>順位</th>
        <th>ユーザー名</th>
        <th>スコア</th>
        <th>最新プレイ日時</th>
    </tr>
    <?php foreach ($topScores as $index => $row): ?>
        <tr class="<?= $row['id'] == $currentUserId ? 'highlight' : '' ?>">
            <td><?= $index + 1 ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= $row['total_score'] ?></td>
            <td><?= $row['latest_play'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php if ($myRank > 10): ?>
    <div style="margin-top: 30px;">
        🎯 現在の順位は <strong style="color: #FFD700;"><?= $myRank ?></strong> 位、スコアは <strong><?= $myScore ?></strong>
    </div>
<?php endif; ?>
</body>
</html>
