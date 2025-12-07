<?php
session_start();
include("conn.php");

// 验证登录状态
if(!isset($_SESSION['user']) || empty($_SESSION['user']['username'])) {
    echo "<script>alert('请先登录'); window.location.href='login.php';</script>";
    exit;
}

$conn = new mysqli('localhost', 'root', 'root', "mybbs");
if ($conn->connect_error) {
    die("数据库连接失败！");
}

// 如果session中没有user_id，从数据库查询获取
if(!isset($_SESSION['user']['user_id']) || empty($_SESSION['user']['user_id'])) {
    $username = $_SESSION['user']['username'];
    $sql_user = "SELECT id FROM users WHERE username = '$username'";
    $result_user = $conn->query($sql_user);
    if($result_user->num_rows > 0) {
        $row = $result_user->fetch_assoc();
        $_SESSION['user']['user_id'] = $row['id'];
    } else {
        echo "<script>alert('用户信息错误，请重新登录'); window.location.href='login.php';</script>";
        exit;
    }
}

// 获取当前用户ID
$user_id = $_SESSION['user']['user_id'];
$username = $_SESSION['user']['username'];

// 查询用户的所有文章（包括私密和公开）
$sql = "SELECT * FROM article WHERE user_id = $user_id ORDER BY time DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户中心</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            min-height: 100vh;
        }
        .header {
            background: #2c3e50;
            color: white;
            padding: 15px 20px;
        }
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
        }
        .nav {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .nav a {
            color: white;
            text-decoration: none;
        }
        .nav a:hover {
            color: #3498db;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }
        .user-info {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            text-align: center;
        }
        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #3498db;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: white;
            margin: 0 auto 15px;
        }
        .stats {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 20px;
        }
        .stat-item {
            text-align: center;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }
        .stat-label {
            font-size: 14px;
            color: #666;
        }
        .articles-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .article-item {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        .article-item:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .article-title {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .article-meta {
            color: #666;
            font-size: 14px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .privacy-badge {
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .privacy-0 { background: #ffebee; color: #c62828; }
        .privacy-1 { background: #e8f5e8; color: #2e7d32; }
        .private-article {
            border-left: 4px solid #dc3545;
            background: #fff5f5;
        }
        .btn {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            border: none;
            cursor: pointer;
        }
        .btn-secondary {
            background: #6c757d;
        }
        .no-articles {
            text-align: center;
            color: #666;
            padding: 40px;
        }
    </style>
</head>
<body>
<div class="header">
    <div class="header-content">
        <div class="logo">轻语</div>
        <nav class="nav">
            <a href="index.php">首页</a>
            <a href="wenzhang.php">发布文章</a>
            <a href="user_center.php" style="color: #3498db;">用户中心</a>
            <a href="out.php">退出</a>
        </nav>
    </div>
</div>

<div class="container">
    <!-- 用户信息 -->
    <div class="user-info">
        <div class="user-avatar">
            <?php echo strtoupper(substr($username, 0, 1)); ?>
        </div>
        <h2>欢迎，<?php echo htmlspecialchars($username); ?></h2>
        <p>用户ID: <?php echo $user_id; ?></p>

        <div class="stats">
            <div class="stat-item">
                <div class="stat-number"><?php echo $result->num_rows; ?></div>
                <div class="stat-label">总文章数</div>
            </div>
            <div class="stat-item">
                <?php
                // 统计私密文章数量
                $private_sql = "SELECT COUNT(*) as count FROM article WHERE user_id = $user_id AND level = 0";
                $private_result = $conn->query($private_sql);
                $private_count = $private_result->fetch_assoc()['count'];
                ?>
                <div class="stat-number"><?php echo $private_count; ?></div>
                <div class="stat-label">私密文章</div>
            </div>
            <div class="stat-item">
                <?php
                // 统计公开文章数量
                $public_sql = "SELECT COUNT(*) as count FROM article WHERE user_id = $user_id AND level = 1";
                $public_result = $conn->query($public_sql);
                $public_count = $public_result->fetch_assoc()['count'];
                ?>
                <div class="stat-number"><?php echo $public_count; ?></div>
                <div class="stat-label">公开文章</div>
            </div>
        </div>
    </div>

    <!-- 文章列表 -->
    <div class="articles-section">
        <h3>我的文章</h3>
        <?php if($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="article-item <?php echo $row['level'] == 0 ? 'private-article' : ''; ?>">
                    <div class="article-title">
                        <?php if($row['level'] == 0): ?>
                            🔒 <?php echo htmlspecialchars($row['title']); ?>
                        <?php else: ?>
                            <?php echo htmlspecialchars($row['title']); ?>
                        <?php endif; ?>
                    </div>
                    <div class="article-meta">
                        <span>发布时间：<?php echo $row['time']; ?></span>
                        <span class="privacy-badge privacy-<?php echo $row['level']; ?>">
                                <?php echo $row['level'] == 0 ? '私密' : '公开'; ?>
                            </span>
                        <span>字数：<?php echo countChineseChars($row['body']); ?>字</span>
                    </div>
                    <div style="margin-top: 10px;">
                        <a href="article_detail.php?id=<?php echo $row['id']; ?>" class="btn">查看详情</a>
                        <?php if($row['level'] == 0): ?>
                            <span style="color: #dc3545; font-size: 12px;">（仅自己可见）</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-articles">
                <p>您还没有发表过文章</p>
                <a href="wenzhang.php" class="btn">去发表第一篇文章</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $conn->close(); ?>
</body>
</html>
