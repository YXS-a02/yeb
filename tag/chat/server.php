<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // 开发环境允许跨域

// 加载配置文件
$configFile = __DIR__ . '/../../website/config.json';
if (!file_exists($configFile)) {
    die(json_encode(['status' => 'error', 'msg' => '配置文件不存在']));
}

$jsonContent = file_get_contents($configFile);
if ($jsonContent === false) {
    die(json_encode(['status' => 'error', 'msg' => '无法读取配置文件']));
}

$config = json_decode($jsonContent, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die(json_encode(['status' => 'error', 'msg' => '配置文件格式错误: ' . json_last_error_msg()]));
}

// 使用配置数据
$host = $config['DB_HOST'] ?? '127.0.0.1';
$dbname = $config['DB_NAME_CHAT'] ?? 'chat';
$username = $config['DB_USER'] ?? 'root';
$password = $config['DB_PASS'] ?? '150abcd051';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // 禁用模拟预处理
} catch (PDOException $e) {
    die(json_encode([
        'status' => 'error', 
        'msg' => '数据库连接失败',
        'error' => $e->getMessage() // 开发环境显示详细信息
    ]));
}

// 保存消息
function saveMessage($pdo, $username, $message) {
    try {
        $stmt = $pdo->prepare("INSERT INTO messages (username, message) VALUES (:username, :message)");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);
        return $stmt->execute();
    } catch (Exception $ex) {
        error_log("保存消息失败: " . $ex->getMessage());
        return false;
    }
}

// 获取消息（按时间倒序，限制 50 条）
function getMessages($pdo) {
    try {
        $stmt = $pdo->query("SELECT id, username, message, timestamp 
                            FROM messages 
                            ORDER BY timestamp DESC 
                            LIMIT 50");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $ex) {
        error_log("获取消息失败: " . $ex->getMessage());
        return [];
    }
}

// 清空消息
function clearMessages($pdo) {
    try {
        $stmt = $pdo->prepare("TRUNCATE TABLE messages");
        return $stmt->execute();
    } catch (Exception $ex) {
        error_log("清空消息失败: " . $ex->getMessage());
        return false;
    }
}

// 处理请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;

    // 发送消息
    if ($action === 'send_message') {
        $username = trim($_POST['username'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (empty($username) || empty($message)) {
            echo json_encode(['status' => 'error', 'msg' => '用户名和消息不能为空']);
            exit;
        }

        // 防止XSS攻击
        $username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
        $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        if (saveMessage($pdo, $username, $message)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'msg' => '消息保存失败']);
        }
        exit;
    }
    
    // 获取消息
    if ($action === 'get_messages') {
        $messages = getMessages($pdo);
        // 反转数组使最新消息在最后
        $messages = array_reverse($messages);
        echo json_encode(['status' => 'success', 'messages' => $messages]);
        exit;
    }
    
    // 清空消息
    if ($action === 'clear_messages') {
        if (clearMessages($pdo)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'msg' => '清空消息失败']);
        }
        exit;
    }
}

// 无效请求
echo json_encode(['status' => 'error', 'msg' => '无效请求']);