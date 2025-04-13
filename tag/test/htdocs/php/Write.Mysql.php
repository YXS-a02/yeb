<form action=""
method="post" enctype="multipart/form-data">
    <input type="text" name="id"><br>
    <input type="text" name="name"><br>
    <input type="text" name="pwd"><br>
    <input type="submit" value="ok">
</form>

<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $pwd = $_POST['pwd'];
        
        echo "◢登录中…<br>";
        $host = 'localhost';
        $user = 'root';
        $password = '150abcd051';
        $dbName = 'yeb';

        // 创建一个新的 mysqli 实例并传入参数完成连接操作
        $conn = new mysqli($host, $user, $password, $dbName);

        // 检查连接状态，如果失败则抛出异常信息
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        echo "◢登录成功！<br>";

        // 设置字符集防止中文乱码问题
        $conn->set_charset('utf8');

        // 使用预处理语句防止SQL注入
        echo "◢写入中…<br>";
        $sql = "INSERT INTO `user` (`id`, `name`) VALUES ( ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die('Prepare() failed: ' . htmlspecialchars($conn->error));
        }
        $stmt->bind_param( $id, $name);
        if ($stmt->execute()) {
            echo "◢New record created successfully(写入成功)<br>";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
        echo "◢结束<br>";
        
        // 执行查询语句
        $sql = "SELECT * FROM u";
        $result = $conn->query($sql);

        echo "内容:<br><hr>";

        if ($result->num_rows > 0) {
            // 输出每行数据
            while($row = $result->fetch_assoc()) {
                echo "◢";
                echo "id: " . $row["id"]. " - Name: " . $row["name"]. ",pwb:". "<br>";
            }
        } else {
            echo "0 results";
        }
        echo "<hr>";
    };
?>