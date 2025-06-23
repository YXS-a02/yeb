<?php
$db_user = "root";
$password = "150abcd051";
 
// 创建连接
$conn = new mysqli('localhost', $db_user, $password);
 
// 检测连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
} 
echo "连接成功";
?>

<?php
echo $db_user
?>