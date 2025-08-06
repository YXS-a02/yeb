<?php
// 加载配置文件
$configFile = __DIR__ . '/../config.json';
if (!file_exists($configFile)) {
    die("Configuration file not found");
}

$config = json_decode(file_get_contents($configFile), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die("Invalid configuration format");
}

// 设置版本和数据库连接
$r_v = $config['VERSION'] ?? '1.0';

try {
    $db_user = new PDO(
        "mysql:host={$config['DB_HOST']};dbname={$config['DB_NAME']};charset=utf8",
        $config['DB_USER'],
        $config['DB_PASS']
    );
    $db_user->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db_user->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // 禁用预处理模拟
} catch (PDOException $e) {
    error_log("DB Connection Error: " . $e->getMessage());
    die('Database connection failed');
}

function checkUserIdExists($db, $id) {
    try {
        $stmt = $db->prepare("SELECT id FROM main WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    } catch (PDOException $e) {
        error_log("Database error in checkUserIdExists: " . $e->getMessage());
        return false;
    }
}

function validateUserCredentials($DB, $id, $password) {
    try {
        $stmt = $DB->prepare("SELECT id, name FROM main WHERE id = :id AND password = :password LIMIT 1");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':password', $password, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in validateUserCredentials: " . $e->getMessage());
        return false;
    }
}

// 处理请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $event = $_POST['event'] ?? '';
    $response = ['runrow' => 'error'];

    try {
        if ($event === 'login') {
            $u_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $u_pwd = $_POST['pwd'] ?? '';

            if (!$u_id || empty($u_pwd)) {
                $response['runrow'] = 'pno';
            } else {
                $user = validateUserCredentials($db_user, $u_id, $u_pwd);
                $response = $user ? [
                    'runrow' => 'yes',
                    'username' => $user['name']
                ] : ['runrow' => 'pno'];
            }
        }
        // 可在此添加其他事件处理（如'singup'）
    } catch (Exception $e) {
        error_log("Request Error: " . $e->getMessage());
        $response['error'] = 'Server error';
    }
    
    echo json_encode($response);
    exit;
} else {
    // GET 请求处理
    header('Content-Type: text/html');
}
?>