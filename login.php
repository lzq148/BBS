<?php session_start();
$conn =new mysqli('localhost', 'root', 'lzqwmx111',"mybbs");
if ($conn->connect_error){
    die("数据库连接失败！");//进行连接数据库
}
if(isset($_SESSION['user']) && $_SESSION['user']['username'] !=null ) {
    header('location:index.php');//监测是否已经登录，已经登录直接跳转
}
if (!empty($_POST["username"]) and !empty($_POST["password"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);

       if ($stmt) {
        // 绑定参数
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $_SESSION['user'] = $result->fetch_assoc();
            $stmt->close();
            $conn->close();
            header("Location: index.php");
            exit;
        } else {
            $stmt->close();
            $conn->close();
            die("<script>alert('账号密码错误！')</script>");
        }
    } else {
        die("数据库查询失败");
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN" >
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>林间小屋</title>
    <link rel="stylesheet" href="/login.css">
    </head>
<body>
<div class="login-container">
    <H2>欢迎登录</H2>
    <form method="POST" action="">
        <div class="form-group">
            <input type="text" id="username" name="username" placeholder="请输入用户名" required autofocus>
        </div>
        <div class="form-group">
            <input type="password" id="password" name="password" placeholder="请输入密码" required>
        </div>
        <button type="submit" >立即登录</button>
        </form>
    <div class="footer-link">
        <a href="register.php" >注册账号</a>
    </div>
</body>
</html>








