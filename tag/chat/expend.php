<?php
function lgdb(){
$host = 'localhost';       // 数据库主机地址
$dbname = 'chat';     // 数据库名称
$username = 'root';        // 用户名
$password = '150abcd051';           // 密码

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // 设置错误模式为异常处理
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
};
//echo 'aa';
lgdb();
?>