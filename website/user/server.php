<?php
$host = 'localhost';// 数据库主机地址
$dbname = 'user';// 数据库名称
$username = 'root';// 用户名
$password = '150abcd051';// 密码
try {
    $db_user = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db_user->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // 设置错误模式为异常处理
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
?>
<?php
function cidndb($db, $id) {
    try {
        // 查询语句
        $stmt = $db->prepare("SELECT id FROM main WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // 如果查询结果有记录，则返回 true，否则返回 false
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        // 捕获并处理数据库错误
        return false;
    }
};
function vuser($DB, $id, $password) {
    $stmt = $DB->prepare("SELECT * FROM main WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $result = $stmt->execute();
    // 如果结果集中有数据，则返回true；否则返回false
    //var_dump($result);  正确应返回PDOStatement对象
    if ($result) {
        return true;
    } else {
        return false;
    }
};
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
    $event = htmlspecialchars(trim($_POST['event']));
    if ($event === 'login') {
        $u_id = $_POST['id'];
        $u_pwd = $_POST['pwd'];
        if (cidndb($db_user,$u_id)) {
            if (vuser($db_user,$u_id,$u_pwd)) {
                echo json_encode(['runrow' => 'ok']);   
            }else{
                echo json_encode(['runrow' => 'no']); 
            }      
        }else{
            echo json_encode(['runrow' => 'no']); 
        };
    };
}else{
    echo 'debug';
    $event = $_GET['event'];
    if ($event == '') {
        echo 'plase enter by get';
    };
};
?>