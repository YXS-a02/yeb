<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF - 8">
    <meta name="viewport" content="width=device-width, initial-scale=1.1">
    <title>数据库读取展示</title>
</head>
<body>
<?php
$host = 'localhost';
$user = 'root';
$password = '150abcd051';
$dbName = 'yeb';

// 创建一个新的mysqli实例并传入参数完成连接操作
$conn = new mysqli($host, $user, $password, $dbName);

// 检查连接状态，如果失败则抛出异常信息
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
} else {
    echo "读取成功！";
}

// 设置字符集防止中文乱码问题
$conn->set_charset('utf8');

// 执行查询语句
$sql = "SELECT * FROM user";
$result = $conn->query($sql);

echo "内容:<br><hr>";

if ($result->num_rows > 0) {
    // 输出表头
    echo "<table border='25'><tr><th>id</th><th>name</th><th>pwd</th></tr>";

    // 输出每行数据
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . htmlspecialchars($row["id"]) . "</td><td>" . htmlspecialchars($row["name"]) . "</td><td>" . "</td></tr>";
    }

    echo "</table>";
} else {
    echo "0 results";
}

// 关闭连接
$conn->close();
?>
</body>
</html>