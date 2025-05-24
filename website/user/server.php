<?php
$r_v = '1.0';
$host = 'localhost';
$dbname = 'user';
$username = 'root';
$password = '150abcd051';
try {
    $db_user = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db_user->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
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
    $event = isset($_POST['event']) ? trim($_POST['event']) : '';
    if ($event === 'login') {
        $u_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $u_pwd = isset($_POST['pwd']) ? trim($_POST['pwd']) : '';
        
        if ($u_id === false || $u_pwd === '') {
            echo json_encode(['runrow' => 'pno']);
            exit;
        }
        $user = validateUserCredentials($db_user, $u_id, $u_pwd);
        if ($user !== false) {
            echo json_encode([
                'runrow' => 'yes',
                'username' => $user['name']
            ]);
        } else {
            echo json_encode(['runrow' => 'pno']);
        }
    }
    if ($event === 'singup') {
        //a
    }
} else {
    header('Content-Type: text/html');
    echo 'debug<hr>';
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $event = isset($_GET['event']) ? $_GET['event'] : '';
        if ($event == 'v') {
            echo $r_v . '<br>';
        } elseif ($event == 'help') {
            echo 'newing...';
        }
    }
    echo '<hr>Please enter by POST<br>';
}
?>