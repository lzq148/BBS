<?php
session_start();
include("conn.php");

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

if(empty($keyword)) {
    header('location: index.php');
    exit;
}

$conn = new mysqli('localhost', 'root', 'lzqwmx111', "mybbs");
if ($conn->connect_error) {
    die("数据库连接失败！");
}

// 搜索文章（只搜索公开文章，不搜索私密文章）
$sql = "SELECT a.*, u.username, u.name 
        FROM article a 
        LEFT JOIN users u ON a.user_id = u.id 
        WHERE a.level = 1  -- 只搜索公开文章
        AND (a.title LIKE '%$keyword%' OR a.body LIKE '%$keyword%')
        ORDER BY a.time DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>搜索结果 - <?php echo htmlspecialchars($keyword); ?></title>
    <style>
        .header a {
            color: white !important;
        }
        .header a:hover {
            color: #3498db !important;
        }
        body { font-family: Arial; margin: 0; background: #f5f5f5; }
        .header { background: #2c3e50; color: white; padding: 15px 20px; }
        .header-content { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 24px; font-weight: bold; }
        .nav { display: flex; align-items: center; gap: 20px; }
        .nav a, .user-info a { color: white ; text-decoration: none; }
        .nav a:hover,.user-info a:hover { color: #3498db ; }
        .user-info { display: flex; align-items: center; gap: 15px; }
        .welcome { color: #ecf0f1; display: flex; align-items: center; gap: 10px; }
        .avatar { width: 30px; height: 30px; border-radius: 50%; background: #3498db; display: flex; align-items: center; justify-content: center; font-size: 14px; }
        .container { max-width: 1200px; margin: 20px auto; padding: 0 20px; }
        .article { background: white; padding: 20px; margin-bottom: 15px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .article-title { font-size: 18px; font-weight: bold; color: #2c3e50; margin-bottom: 10px; }
        .article-title a { color: #2c3e50; text-decoration: none; }
        .article-title a:hover { color: #3498db; }
        .article-meta { color: #666; font-size: 14px; display: flex; gap: 15px; flex-wrap: wrap; }
        .left-section { display: flex; align-items: center; gap: 30px; }
        .right-section { display: flex; align-items: center; gap: 20px; }
        .privacy-badge {
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .privacy-1 { background: #e8f5e8; color: #2e7d32; }
        
        /* 搜索框样式 */
        .search-container {
            display: flex;
            align-items: center;
        }
        .search-form {
            display: flex;
            gap: 8px;
        }
        .search-input {
            padding: 8px 15px;
            border: 2px solid #3498db;
            border-radius: 20px;
            outline: none;
            font-size: 14px;
            width: 200px;
            transition: all 0.3s ease;
        }
        .search-input:focus {
            border-color: #2980b9;
            box-shadow: 0 0 8px rgba(52, 152, 219, 0.3);
        }
        .search-btn {
            padding: 8px 20px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .search-btn:hover {
            background: #2980b9;
            transform: translateY(-1px);
        }
        
        /* 搜索结果页面样式 */
        .search-results {
            background: white;
            padding: 25px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .search-title {
            color: #2c3e50;
            margin-bottom: 15px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        .no-results {
            text-align: center;
            color: #666;
            padding: 40px;
            font-style: italic;
        }
        .result-count {
            font-size: 14px;
            color: #666;
            font-weight: normal;
        }
    </style>
</head>
<body>
<div class="header">
    <div class="header-content">
        <div class="left-section">
            <div class="logo">BBS</div>
            <nav class="nav">
                <a href="index.php">首页</a>
                <a href="user_center.php">用户中心</a>
                <?php if(isset($_SESSION['user'])): ?>
                    <a href="wenzhang.php">发布文章</a>
                <?php endif; ?>
            </nav>
        </div>

        <div class="right-section">
            <!-- 搜索框 -->
            <div class="search-container">
                <form method="get" action="search.php" class="search-form">
                    <input type="text" name="keyword" class="search-input" 
                           value="<?php echo htmlspecialchars($keyword); ?>" 
                           placeholder="搜索文章..." required>
                    <button type="submit" class="search-btn">搜索</button>
                </form>
            </div>
            
            <?php if(isset($_SESSION['user'])): ?>
                <div class="welcome">
                    <div class="avatar"><?php echo strtoupper(substr($_SESSION['user']['username'], 0, 1)); ?></div>
                    <span>欢迎 <?php echo $_SESSION['user']['username']; ?></span>
                </div>
                <a href="out.php">退出</a>
            <?php else: ?>
                <a href="login.php">登录</a>
                <a href="register.php">注册</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="container">
    <div class="search-results">
        <h2 class="search-title">
            搜索 "<?php echo htmlspecialchars($keyword); ?>"
            <span class="result-count">(找到 <?php echo $result->num_rows; ?> 篇相关文章)</span>
        </h2>
        
        <?php if($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="article">
                    <div class="article-title">
                        <a href="article_detail.php?id=<?php echo $row['id']; ?>">
                            <?php echo htmlspecialchars($row['title']); ?>
                        </a>
                    </div>
                    <div class="article-meta">
                        <span>作者：
                            <?php
                            if(!empty($row['name'])) {
                                echo htmlspecialchars($row['name']);
                            } else {
                                echo htmlspecialchars($row['username']);
                            }
                            ?>
                        </span>
                        <span>时间：<?php echo $row['time']; ?></span>
                        <span class="privacy-badge privacy-1">公开</span>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-results">
                没有找到包含 "<?php echo htmlspecialchars($keyword); ?>" 的文章
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $conn->close(); ?>
</body>
</html>