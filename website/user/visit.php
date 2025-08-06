<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户信息页面</title>
    <style>
        /* 样式保持不变 */
        .top {
            list-style-type: none;
            margin: 0;
            padding: 0 10px;
            overflow: hidden;
            background-color: #333;
            height: 23px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .top input, .top button {
            height: 20px;
        }
        .profile-header {
            background-image: url(../../file/src/greenbp.gif);
            height: 180px;
            display: flex;
            align-items: center;
        }
        .profile-pic {
            margin-left: 64px;
        }
        .content-area {
            display: flex;
            min-height: 300px;
        }
        .main-content {
            flex: 70%;
            background-image: url(../../file/src/redbp.gif);
        }
        .sidebar {
            flex: 30%;
            background-color: aquamarine;
            padding: 10px;
        }
        .error-message {
            color: red;
            padding: 10px;
        }
    </style>
</head>
<body>
<?php
// 初始化变量
$rows = [];
$userInfo = [];
$error = null;

// 加载配置文件
$configFile = __DIR__ . '/../config.json';
if (!file_exists($configFile)) {
    die("配置文件不存在: " . $configFile);
}

$jsonContent = file_get_contents($configFile);
if ($jsonContent === false) {
    die("无法读取配置文件: " . $configFile);
}

$config = json_decode($jsonContent, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die("配置文件格式错误: " . json_last_error_msg());
}

// 使用配置数据
$servername = $config['DB_HOST'] ?? '127.0.0.1';
$dbname = $config['DB_NAME'] ?? 'user';
$username = $config['DB_USER'] ?? 'root';
$password = $config['DB_PASS'] ?? '150abcd051';

function sbc($cookie_name){
    if(!isset($_COOKIE[$cookie_name])) {
        return null;
    } else {
        return $_COOKIE[$cookie_name];
    };
};

// 验证并获取uid参数
$uid = isset($_GET['uid']) ? trim($_GET['uid']) : sbc('u_id');

if ($uid) {
    try {
        // 创建PDO连接
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // 设置PDO错误模式为异常
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        
        // 查询information表
        $sql = "SELECT hdimg, note, email FROM information WHERE id = :id LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $uid, PDO::PARAM_STR);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // 查询main表获取用户名
        $sql2 = "SELECT name FROM main WHERE id = :id LIMIT 1";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bindParam(':id', $uid, PDO::PARAM_STR);
        $stmt2->execute();
        $userInfo = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        
    } catch(PDOException $e) {
        error_log("[visit:" . date('Y-m-d H:i:s') . "] 数据库错误: " . $e->getMessage() . "\n", 3, "error.log");
        $error = "系统暂时不可用，请稍后再试";
    } finally {
        // 确保连接关闭
        if (isset($conn)) {
            $conn = null;
        }
    }
} else {
    $error = "错误：缺少用户ID参数";
}
?>
    <div class="top">
        <a href="../home.html" style="color: aliceblue; text-decoration: none;">YEB</a>
        <input id="sarah" placeholder="搜索...">
        <button onclick="sarah()">搜索</button>
        <div id="sud" style="color: aliceblue;"></div>
    </div>
    
    <?php if ($error): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <div class="profile-header">
        <div class="profile-pic">
            <img src="../../file/src/<?php echo isset($rows[0]['hdimg']) ? htmlspecialchars($rows[0]['hdimg']) : 'bp.gif'; ?>" width="100px" height="100px" alt="用户头像" loading="lazy">
        </div>
        <div>
            <h1 style="font-size: 50px; margin: 0;"><?php echo isset($userInfo[0]['name']) ? htmlspecialchars($userInfo[0]['name']) : '未命名用户'; ?></h1>
            <p style="font-size: 25px; margin: 5px 0 0;">ID: <?php echo htmlspecialchars($uid ?? '未知'); ?></p>
        </div>
    </div>
    
    <div class="content-area">
        <div class="main-content">
            <p>这里是用户的主内容区域</p>
        </div>
        <div class="sidebar">
            <div>
                <h3>备注</h3>
                <p><?php echo isset($rows[0]['note']) ? nl2br(htmlspecialchars($rows[0]['note'])) : '暂无备注'; ?></p>
            </div>
            <div onclick="window.open('https://www.bing.com')" style="cursor: pointer; margin: 10px 0;">
                <p>点击访问 Bing</p>
            </div>
            <div>
                <h3>电话</h3>
                <p>待添加</p>
            </div>
            <div>
                <h3>邮箱</h3>
                <p><?php echo isset($rows[0]['email']) ? htmlspecialchars($rows[0]['email']) : '未提供'; ?></p>
            </div>
        </div>
    </div>
    
    <script src="./website.js"></script>
    <script>
        // 从本地存储获取用户名并显示
        document.addEventListener('DOMContentLoaded', function() {
            const user = yookie.get('u_name');
            if (user) {
                document.getElementById('sud').textContent = user;
            }
        });
        
        function sarah() {
            const query = document.getElementById('sarah').value.trim();
            if (query) {
                // 这里可以添加搜索功能
                alert('搜索: ' + query);
            } else {
                alert('请输入搜索内容');
            }
        }
    </script>
</body>
</html>