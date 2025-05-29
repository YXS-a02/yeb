<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // 开发环境允许跨域

// 数据库配置
$host = 'localhost';
$dbname = 'chat';
$username = 'root';
$password = '150abcd051';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['status' => 'error', 'msg' => 'Database connection failed.']));
}

// 保存消息
function saveMessage($pdo, $username, $message) {
    try {
        $stmt = $pdo->prepare("INSERT INTO messages (username, message) VALUES (:username, :message)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':message', $message);
        return $stmt->execute();
    } catch (Exception $ex) {
        error_log("Save message failed: " . $ex->getMessage());
        return false;
    }
}

// 获取消息（按时间倒序，限制 50 条）
function getMessages($pdo) {
    try {
        $stmt = $pdo->query("SELECT username, message, timestamp FROM messages ORDER BY timestamp DESC LIMIT 50");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $ex) {
        error_log("Get messages failed: " . $ex->getMessage());
        return [];
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
            echo json_encode(['status' => 'error', 'msg' => 'Username and message cannot be empty.']);
            exit;
        }

        if (saveMessage($pdo, $username, $message)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Failed to save message.']);
        }
        exit;
    }
    // 在 server.php 的 POST 处理部分添加：
    if ($action === 'clear_messages') {
        try {
            $stmt = $pdo->prepare("TRUNCATE TABLE messages");
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'Failed to clear messages']);
            }
        } catch (Exception $ex) {
            error_log("Clear messages error: " . $ex->getMessage());
            echo json_encode(['status' => 'error', 'msg' => 'Database error']);
        }
        exit;
    }
    // 获取消息
    if ($action === 'get_messages') {
        $messages = getMessages($pdo);
        echo json_encode(['status' => 'success', 'messages' => $messages]);
        exit;
    }
}

// 无效请求
echo json_encode(['status' => 'error', 'msg' => 'Invalid request.']);