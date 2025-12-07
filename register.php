<!doctype html>
<html lang="zh-CN" >
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>注册账号BBS论坛</title>
    <link rel="stylesheet" href="static/login.css">
    <link rel="stylesheet" href="static/register.css">
</head>
<body>
<div class="login-container">
    <h2>注册新账号</h2>
    <form method="POST" action="" onsubmit="return validateForm()">
        <div class="form-group">
            <label for="username">用户名</label>
            <input type="text" id="username" name="username"
                   placeholder="4-20"
                   pattern="[A-Za-z0-9]{4,20}"
                   required autofocus>
        </div>
        <div class="form-group">
            <label for="password">密码</label>
            <input type="password" id="password" name="password"
                   placeholder="至少6位密码"
                   minlength="6"
                   required>
            <div class="password-strength">密码强度:<span id="strength-text">-</span></div>
        </div>
        <div class="form-group">
            <label for="password_verify">确认密码</label>
            <input type="password" id="password_verify" name="password_verify"
                   placeholder="再次输入密码"
                   required>
            <div class="password-match">密码匹配</div>
            <div class="password-mismatch">密码不匹配</div>
        </div>
        <button type="submit" class="register-btn">立即注册</button>
    </form>
    <div class="forgot-links">
        <a href="login.php">已有账号？立即登录</a>
    </div>
</div>
<script>

    const password =document.getElementById('password');
    const passwordVerify =document.getElementById('password_verify');
    const matchMsg =document.querySelector('.password-strength');
    const mismatchMsg =document.querySelector('.password-mismatch');
    function checkPasswordMatch() {
        if (password.value && passwordVerify.value){
            if (password.value === passwordVerify.value){
                matchMsg.style.display = 'block';
                mismatchMsg.style.display = 'none';
            }else {
                matchMsg.style.display = 'none';
                mismatchMsg.style.display = 'block';
            }
        }
    }
    password.addEventListener('input', checkPasswordMatch);
    passwordVerify.addEventListener('input', checkPasswordMatch);


    password.addEventListener('input',function(){
        const strengthText=document.getElementById('strength-text');
        const strength = calculateStrength(password.value);
        strengthText.textContent = strength.text;
        strengthText.style.color = strength.color;
    });
    function calculateStrength(pw){
        const hasLower = /[a-z]/.test(pw);
        const hasUpper = /[A-Z]/.test(pw);
        const hasNumber = /\d/.test(pw);
        const hasSpecial = /[!@#$%^&*]/.test(pw);

        let score = 0;
        if(pw.length >= 6) score++;
        if(pw.length >= 8) score++;
        if(hasLower && hasUpper) score++;
        if(hasNumber) score++;
        if(hasSpecial) score++;
        switch(score){
            case '0': case 1:
                return {text:'弱',color:'#e53e3e' };
            case '2': case 3:
                return {text:'中',color:'#d69e2e' };
            default:
                return {text:'强',color:'#38a169' };

        }
    }
function validateForm(){
        if (password.value !==passwordVerify.value){
            alert('两次输入不一致');
            return false;
        }
        return true;
}
</script>
</body>
</html>
<?php
if (!empty($_POST["username"])and!empty($_POST["password"])) {
    $conn = new mysqli('localhost', 'root', '123456', "mybbs", 3306);
    if ($conn->connect_error) {
        die("数据库连接失败！");
    }
    $username = $_POST['username'];
    $password = $_POST['password'];
    $sql1 = "select username from users where username='$username'";
    $jieguo = $conn->query($sql1);
//判断是否存在相同的用户名
    if ($jieguo->num_rows > 0) {
        die("<script>alert('用户名存在') </script>");
    }
    $sql = "INSERT INTO users(username,password,level) values('$username','$password',0);";
    if ($conn->query($sql) === TRUE) {
        header("location:login.php");
        exit;
    } else {
        echo "<script>alert('注册失败')</script>";
    }
    $conn->close();
}




