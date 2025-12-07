<?php
$conn =new mysqli('localhost', 'root', 'root',"mybbs");
if ($conn->connect_error){
    die("数据库连接失败！");
}

//新改进的
// 设置字符集
$conn->set_charset("utf8mb4");

// 通用函数定义（使用function_exists检查避免重复定义）
if (!function_exists('countChineseChars')) {
    function countChineseChars($text) {
        // 去除所有空白字符（空格、换行、制表符等）
        $text = preg_replace('/\s+/', '', $text);
        
        if (function_exists('mb_strlen')) {
            return mb_strlen($text, 'UTF-8');
        } else {
            // 备用方案：使用正则表达式统计字符
            return preg_match_all('/./u', $text);
        }
    }
}

if (!function_exists('getUserStats')) {
    function getUserStats($conn, $user_id) {
        // 可以添加其他统计函数
        $stats = [];
        
        // 总文章数
        $sql = "SELECT COUNT(*) as count FROM article WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stats['total'] = $result->fetch_assoc()['count'];
        $stmt->close();
        
        return $stats;
    }
}
?>

<?php
// 在conn.php中添加HTML安全过滤函数
if (!function_exists('safe_html')) {
    function safe_html($html) {
        // 允许的HTML标签和属性
        $allowed_tags = '<p><br><div><span><strong><b><em><i><u><strike><ul><ol><li><blockquote><code><pre><h1><h2><h3><h4><h5><h6><table><tr><td><th><thead><tbody><a><img>';
        $allowed_attributes = 'href|src|alt|title|width|height|class|style|border|cellpadding|cellspacing';
        
        // 先进行基本的HTML转义
        $html = htmlspecialchars($html, ENT_QUOTES, 'UTF-8');
        
        // 然后允许安全的HTML标签
        $html = strip_tags($html, $allowed_tags);
        
        // 移除危险的属性（如onerror、onclick等）
        $html = preg_replace('/<(.*?)>/ie', "'<' . preg_replace('/javascript:/i', '', preg_replace('/([a-z]*)=([\`\'\"]*)javascript:([^\`\'\"]*)([\`\'\"])/i', '\1=\2\4', stripslashes('\\1'))) . '>'", $html);
        
        return $html;
    }
}

?>
