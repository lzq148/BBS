<?php
// PHP代码放在HTML前面
date_default_timezone_set('Asia/Shanghai');
session_start();

// 检查是否已登录
if(!isset($_SESSION['user']) || empty($_SESSION['user']['username'])) {
    header('location:index.php');
    exit;
}

// 处理表单提交
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(!empty($_POST['title']) && !empty($_POST['body']) && isset($_POST['level']) && $_POST['level'] !== '') {
        $conn = new mysqli('localhost', 'root', 'lzqwmx111', "mybbs");
        if ($conn->connect_error) {
            die("数据库连接失败！");
        }

        // 如果session中没有user_id，从数据库查询获取
        if(!isset($_SESSION['user']['user_id']) || empty($_SESSION['user']['user_id'])) {
            $username = $_SESSION['user']['username'];
            $sql_user = "SELECT id FROM users WHERE username = '$username'";
            $result = $conn->query($sql_user);
            if($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $_SESSION['user']['user_id'] = $row['id'];
            } else {
                echo "<script>alert('用户信息错误，请重新登录'); window.location.href='login.php';</script>";
                exit;
            }
        }

        $user_id = $_SESSION['user']['user_id'];
        $title = $_POST['title'];
        $body = $_POST['body'];
        $level = $_POST['level'];
        $time = date('Y-m-d H:i:s');

        $sql = "INSERT INTO article (user_id, title, body, level, time) values('$user_id','$title','$body','$level','$time')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('文章保存成功'); window.location.href='index.php';</script>";
            exit;
        } else {
            echo "<script>alert('文章保存失败: " . $conn->error . "')</script>";
        }
        $conn->close();
    } else {
        echo "<script>alert('请填写完整的信息，包括选择权限')</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文章发表界面</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;  /* 增加最大宽度 */
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #555;
            font-size: 16px;
        }

        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .form-group textarea {
            height: 400px;  /* 增加高度 */
            resize: vertical;
            line-height: 1.6;
            font-family: 'Arial', sans-serif;
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
        }

        /* 响应式设计 */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
                margin: 10px;
            }
            
            .form-group textarea {
                height: 300px;  /* 移动端适当减小高度 */
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>发表文章</h1>
    <form method="post" action="">
        <div class="form-group">
            <label for="title">文章标题</label>
            <input type="text" id="title" name="title" placeholder="请输入文章标题" required>
        </div>
        <div class="form-group">
            <label for="level">隐私设置</label>
            <select id="level" name="level" required>
                <option value="">请选择权限</option>
                <option value="0">私密（仅自己可见）</option>
                <option value="1">公开（所有人可见）</option>
            </select>
        </div>
        <div class="form-group">
            <label for="content">文章内容</label>
            <textarea id="content" name="body" placeholder="请输入文章内容..." required></textarea>
        </div>
        <button type="submit" class="submit-btn">发布文章</button>
    </form>
</div>
</body>
</html>