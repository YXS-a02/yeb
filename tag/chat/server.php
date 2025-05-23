<?php
header('Content-Type: application/json');
// 数据库连接
$host = 'localhost';$dbname = 'chat';$username = 'root';$password = '150abcd051';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['status' => 'error', 'msg' => 'Database connection failed: ' . $e->getMessage()]));
}

// 保存消息到数据库
function saveMessage($pdo, $username, $message) {
    try {
        $stmt = $pdo->prepare("INSERT INTO messages (username, message) VALUES (:username, :message)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':message', $message);
        return $stmt->execute();
        $pdo->commit();
    } catch (Exception $ex) {
        return false;
    }
}
// 获取消息从数据库
function getMessages($pdo) {
    try {
        $stmt = $pdo->query("SELECT username, message, timestamp FROM messages");
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $messages;
    } catch (Exception $ex) {
        return [];
    }
}
// chat_send_ajax.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ((isset($_POST['action']) && $_POST['action'] == 'send')) {
        $username = htmlspecialchars(trim($_POST['username']));
        $message = htmlspecialchars(trim($_POST['message']));
        if (!empty($username) && !empty($message)) {
            //file_put_contents('chat.txt', date('Y-m-d H:i:s').' ['.$username.'] '.$message."\n", FILE_APPEND);
            saveMessage($pdo,$username,$message);
            echo json_encode(['status' => 'success']);
            exit;
        } else {
            echo json_encode(['status' => 'error', 'msg' => '不能为空!']);
        }
    }
}  
// 处理请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;

    if ($action === 'send_message') {
        $username = htmlspecialchars(trim($_POST['username']));
        $message = htmlspecialchars(trim($_POST['message']));

        if (!empty($username) && !empty($message)) {
            if (saveMessage($pdo, $username, $message)) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'Failed to save message']);
            }
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Username and message cannot be empty']);
        }
        exit;
    }

    if ($action === 'get_messages') {
        $messages = getMessages($pdo);
        if (!empty($messages)) {
            echo json_encode(['status' => 'success', 'messages' => $messages]);
        } else {
            echo json_encode(['status' => 'success', 'messages' => ['none']]);
        }
        exit;
    }
}

echo json_encode(['status' => 'error', 'msg' => 'what?']);
exit;
?>