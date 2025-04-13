<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $pwb = $_POST['pwb'] ?? '';
    if (!empty($name)) {
        echo "Hello, " . htmlspecialchars($name) . "! Welcome to our site.";
    } else {
        echo "Please enter a valid name.";
    }
} else {
    echo "Invalid request method.";
}
    echo $pwb
?>