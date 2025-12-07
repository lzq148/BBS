<?php
session_start();
include("conn.php");

// 验证登录状态
if(!isset($_SESSION['user']) || empty($_SESSION['user']['username'])) {
    echo "<script>alert('请先登录'); window.location.href='login.php';</script>";
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

// 查询文章信息
$sql = "SELECT * FROM article WHERE id = $article_id";
$result = $conn->query($sql);

if($result->num_rows == 0) {
    echo "<script>alert('文章不存在'); window.location.href='user_center.php';</script>";
    exit;
}

$article = $result->fetch_assoc();

// 检查权限：只能删除自己的文章
if($article['user_id'] != $_SESSION['user']['user_id']) {
    echo "<script>alert('您只能删除自己的文章'); window.location.href='user_center.php';</script>";
    exit;
}

// 删除文章
$delete_sql = "DELETE FROM article WHERE id = $article_id";

if ($conn->query($delete_sql) === TRUE) {
    echo "<script>alert('文章删除成功'); window.location.href='user_center.php';</script>";
    exit;
} else {
    echo "<script>alert('文章删除失败: " . $conn->error . "'); window.location.href='user_center.php';</script>";
    exit;
}

$conn->close();

?>
