<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>title</title>
    <style>
         .top{list-style-type: none;margin: 0;padding: 0;overflow: hidden;background-color: #333;height: 23px;display: flex;}
    </style>
</head>
<?php
// 数据库连接配置
$servername = "localhost";
$username = "root";
$password = "150abcd051";
$dbname = "user";

try {
    // 创建PDO连接
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // 设置PDO错误模式为异常
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // SQL查询语句
    $sql = "SELECT note, email FROM information";
    
    // 准备语句并执行查询
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    // 设置结果集为关联数组
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    
    // 获取所有结果
    $rows = $stmt->fetchAll();
    
    // 输出结果
    if (!count($rows) > 0) {
        echo "no";
    };
} catch(PDOException $e) {
    echo "错误: " . $e->getMessage();
}
// 关闭连接
$conn = null;
?>
<body>
    <div class="top">
        <a href="../home.html" style="color: aliceblue;text-decoration: none;">YEB</a>
        <input id="sarah">
        <button onclick="sarah()">sarah</button>
        <div id="sud" style="color: aliceblue;"></div>
    </div>
    <div>
        <div style="background-image: url(../../file/src/greenbp.gif);height: 180px;display: flex;">
            <div><img src="../../file/src/bp.gif" width="100px" height="100px" style="margin-top: 40px;margin-left: 64px;"></div>
            <div>
                <div style="margin-top: 40px;"></div>
                <a style="font-size: 50px;">name</a>
                <br>
                <a style="font-size: 25px;">id</a>
            </div>
        </div>
        <div>
            <div style="float: left;background-image: url(../../file/src/redbp.gif);width: 70%;min-height: 300px;">a</div>
            <div style="float: right;width: 30%;background-color: aquamarine;min-height: 300px;">
                <div>note<br><?phpecho $rows['note'];?></div>
                <div onclick="window.open('http://www.bing.com')"></div>
                <div>tel</div>
                <div>email</div>
                <div>email</div>
            </div>
        </div>
    </div>
    <div>
        
    </div>
    <script src="./sarah.js"></script>
    <script>
        var user=localStorage.getItem('user',user);document.getElementById('sud').innerHTML= user;
    </script>
</body>
</html>