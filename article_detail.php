<?php
session_start();
include("conn.php");

// 检查是否传递了文章ID
if(!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('文章不存在'); window.location.href='index.php';</script>";
    exit;
}

$article_id = (int)$_GET['id'];

$conn = new mysqli('localhost', 'root', 'root', "mybbs");
if ($conn->connect_error) {
    die("连接失败");
}

// 查询文章详情
$sql = "SELECT a.*, u.username, u.name 
        FROM article a 
        LEFT JOIN users u ON a.user_id = u.id 
        WHERE a.id = $article_id";
$result = $conn->query($sql);

if($result->num_rows == 0) {
    echo "<script>alert('文章不存在'); window.location.href='index.php';</script>";
    exit;
}

$article = $result->fetch_assoc();

// 检查权限：私密文章只能作者本人查看
if($article['level'] == 0) {
    if(!isset($_SESSION['user']) || $_SESSION['user']['user_id'] != $article['user_id']) {
        echo "<script>alert('您没有权限查看此文章'); window.location.href='index.php';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?> - 文章详情</title>
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
            max-width: 800px;
            margin: 20px auto;
            padding: 0 20px;
        }
        .article-detail {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .article-title {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 20px;
            text-align: center;
        }
        .article-meta {
            color: #666;
            font-size: 14px;
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .article-content {
            color: #444;
            line-height: 1.8;
            font-size: 16px;
            margin-bottom: 30px;
        }
        .article-content p {
            margin-bottom: 15px;
        }
        .privacy-badge {
            padding: 4px 12px;
            border-radius: 3px;
            font-size: 14px;
            font-weight: bold;
        }
        .privacy-0 { background: #ffebee; color: #c62828; }
        .privacy-1 { background: #e8f5e8; color: #2e7d32; }
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
        .action-buttons {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
<div class="header">
    <div class="header-content">
        <div class="logo">轻语</div>
        <nav class="nav">
            <a href="index.php">首页</a>
            <a href="user_center.php">用户中心</a>
            <?php if(isset($_SESSION['user'])): ?>
                <a href="wenzhang.php">发布文章</a>
            <?php endif; ?>
        </nav>
    </div>
</div>

<div class="container">
    <div class="article-detail">
        <h1 class="article-title">
            <?php if($article['level'] == 0): ?>
                🔒 <?php echo htmlspecialchars($article['title']); ?>
            <?php else: ?>
                <?php echo htmlspecialchars($article['title']); ?>
            <?php endif; ?>
        </h1>

        <div class="article-meta">
                <span>作者：
                    <?php
                    if(!empty($article['name'])) {
                        echo htmlspecialchars($article['name']);
                    } else {
                        echo htmlspecialchars($article['username']);
                    }
                    ?>
                    <?php if(isset($_SESSION['user']) && $_SESSION['user']['user_id'] == $article['user_id']): ?>
                        <strong>(我)</strong>
                    <?php endif; ?>
                </span>
            <span>发布时间：<?php echo $article['time']; ?></span>
            <span class="privacy-badge privacy-<?php echo $article['level']; ?>">
                    <?php echo $article['level'] == 0 ? '私密' : '公开'; ?>
                </span>
            <span>字数：<?php echo countChineseChars($article['body']); ?>字</span>
        </div>

        <div class="article-content">
            <?php echo nl2br(htmlspecialchars($article['body'])); ?>
        </div>

        <div class="action-buttons">
            <a href="user_center.php" class="btn">返回用户中心</a>
            <a href="index.php" class="btn btn-secondary">返回首页</a>
            <?php if(isset($_SESSION['user']) && $_SESSION['user']['user_id'] == $article['user_id']): ?>
                <a href="edit_article.php?id=<?php echo $article['id']; ?>" class="btn" style="background: #28a745;">编辑文章</a>
                <a href="delete_article.php?id=<?php echo $article['id']; ?>" class="btn" style="background: #dc3545;"
                   onclick="return confirm('确定要删除这篇文章吗？此操作不可恢复！')">删除文章</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $conn->close(); ?>
</body>
</html>
