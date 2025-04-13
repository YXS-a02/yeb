<?php
// chat_send_ajax.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim($_POST['username']));
    $message = htmlspecialchars(trim($_POST['message']));

    if (!empty($username) && !empty($message)) {
        file_put_contents('chat_log.txt', date('Y-m-d H:i:s').' ['.$username.'] '.$message."\n", FILE_APPEND);
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Empty fields!']);
    }
} else {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid request method!']);
}
?>