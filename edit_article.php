<?php
session_start();

// 检查是否已登录
if(!isset($_SESSION['user']) || empty($_SESSION['user']['username'])) {
    header('location:index.php');
    exit;
}

// 检查是否传递了文章ID
if(!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('文章不存在'); window.location.href='user_center.php';</script>";
    exit;
}

$article_id = (int)$_GET['id'];

$conn = new mysqli('localhost', 'root', 'root', "mybbs");
if ($conn->connect_error) {
    die("数据库连接失败！");
}

// 查询文章详情
$sql = "SELECT a.*, u.username, u.name 
        FROM article a 
        LEFT JOIN users u ON a.user_id = u.id 
        WHERE a.id = $article_id";
$result = $conn->query($sql);

if($result->num_rows == 0) {
    echo "<script>alert('文章不存在'); window.location.href='user_center.php';</script>";
    exit;
}

$article = $result->fetch_assoc();

// 检查权限：只能编辑自己的文章
if($article['user_id'] != $_SESSION['user']['user_id']) {
    echo "<script>alert('您只能编辑自己的文章'); window.location.href='user_center.php';</script>";
    exit;
}

// 处理表单提交
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(!empty($_POST['title']) && !empty($_POST['body']) && isset($_POST['level']) && $_POST['level'] !== '') {
        $title = $conn->real_escape_string($_POST['title']);
        $body = $conn->real_escape_string($_POST['body']);
        $level = $conn->real_escape_string($_POST['level']);

        // 更新文章
        $update_sql = "UPDATE article SET title = '$title', body = '$body', level = '$level' WHERE id = $article_id";

        if ($conn->query($update_sql) === TRUE) {
            echo "<script>alert('文章更新成功'); window.location.href='article_detail.php?id=$article_id';</script>";
            exit;
        } else {
            echo "<script>alert('文章更新失败: " . $conn->error . "')</script>";
        }
    } else {
        echo "<script>alert('请填写完整的信息')</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>编辑文章</title>
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
        .edit-card {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .page-title {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 30px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 25px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #2c3e50;
            font-size: 16px;
        }
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        .form-control:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        .form-select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 16px;
            background: white;
        }
        .btn {
            padding: 12px 30px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            margin-right: 10px;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-secondary:hover {
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
        }
        .action-buttons {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        textarea.form-control {
            height: 400px;
            resize: vertical;
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
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
            <a href="wenzhang.php">发布文章</a>
        </nav>
    </div>
</div>

<div class="container">
    <div class="edit-card">
        <h1 class="page-title">编辑文章</h1>
        <form method="post" action="">
            <div class="form-group">
                <label for="title">文章标题</label>
                <input type="text" id="title" name="title" class="form-control" 
                       value="<?php echo htmlspecialchars($article['title']); ?>" 
                       placeholder="请输入文章标题" required>
            </div>
            
            <div class="form-group">
                <label for="level">隐私设置</label>
                <select id="level" name="level" class="form-select" required>
                    <option value="">请选择权限</option>
                    <option value="0" <?php echo $article['level'] == 0 ? 'selected' : ''; ?>>私密（仅自己可见）</option>
                    <option value="1" <?php echo $article['level'] == 1 ? 'selected' : ''; ?>>公开（所有人可见）</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="content">文章内容</label>
                <textarea id="content" name="body" class="form-control" 
                          placeholder="请输入文章内容" required><?php echo htmlspecialchars($article['body']); ?></textarea>
            </div>
            
            <div class="action-buttons">
                <button type="submit" class="btn">更新文章</button>
                <a href="article_detail.php?id=<?php echo $article_id; ?>" class="btn btn-secondary">取消编辑</a>
            </div>
        </form>
    </div>
</div>

<?php $conn->close(); ?>
</body>
</html>
