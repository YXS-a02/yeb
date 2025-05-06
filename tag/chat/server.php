<?php
$host = 'localhost';$dbname = 'chat';$db_username = 'root';$db_password = '150abcd051';           // 密码
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // 设置错误模式为异常处理
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
?>
<?php
function saveMessage($pdo, $username, $message){
    try{
        $stmt = $pdo->prepare("INSERT INTO messages(username, message) VALUES(:username, :message)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':message', $message);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }catch(Exception $ex){
        echo "Error saving the message to database.";
        return false;
    }
}
// chat_send_ajax.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['e' == 'send']) {
        $username = htmlspecialchars(trim($_POST['username']));
        $message = htmlspecialchars(trim($_POST['message']));
        if (!empty($username) && !empty($message)) {
            file_put_contents('chat.txt', date('Y-m-d H:i:s').' ['.$username.'] '.$message."\n", FILE_APPEND);
            saveMessage($pdo,$username,$message);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'msg' => '不能为空!']);
        }
    }
}  
?>


