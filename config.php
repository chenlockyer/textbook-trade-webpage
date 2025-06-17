<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 数据库配置
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'admin123');
define('DB_NAME', 'textbook_trade');

// 创建数据库连接
$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// 检查连接
if ($db->connect_error) {
    die("数据库连接失败: " . $db->connect_error);
}

// 设置字符集
$db->set_charset("utf8mb4");
?>