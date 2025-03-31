<!DOCTYPE html>
<html>
<head>
    <title>文件管理</title>
    <style>
        .inline-forms { display: flex; }
        .inline-forms form { margin-right: 10px; }
        .popup {display: none;position: fixed;left: 50%;top: 50%;transform: translate(-50%, -50%);width: 200px;height: 175px;background-color: gray;border: 1px solid #ccc;padding: 20px;z-index: 9999;}
    </style>
</head>
<body>
<script>
    // 显示弹窗
    function showA() {document.getElementById('upl').style.display = 'block';}
    function showB() {document.getElementById('newd').style.display = 'block';}
    // 隐藏弹窗
    function hideA() {document.getElementById('upl').style.display = 'none';}
    function hideB() {document.getElementById('newd').style.display = 'none';}
</script>
<h1>文件管理器</h1>
<!-- Inline forms for upload and new directory -->
<div class="inline-forms" style="text-align: center">
    <!-- 文件上传表单 -->
    <div id="upl" class="popup">
        <form action="" method="post" enctype="multipart/form-data">
            <div style="background-color:blue;">上传</div>
            <input type="file" name="uploadFile" style="font-size:10px"><br>
            <input type="submit" value="上传"><br>
            <button onclick="hideA()">关闭</button>
        </form>
    </div>
    <!-- 创建新目录表单 -->
    <div id="newd" class="popup">
        <form action="" method="post">
            <div style="background-color:blue;">新建目录</div>
            <input type="submit" value="创建"><br>
            <input type="text" name="newDir"><br>
            <button onclick="hideA()">关闭</button>
        </form>
    </div>
    <div style="background-color:black;">
        <button onclick="showA()">上传</button>
        <button onclick="showB()">新建文件夹</button>
    </div>
</div>
<?php
$directory = __DIR__; // 获取当前脚本所在的目录

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['newDir'])) { // 创建新目录
        $newDir = $_POST['newDir'];
        if (!empty($newDir)) {
            mkdir("$directory/$newDir", 0777, true);
        }
    } elseif (isset($_FILES['uploadFile'])) { // 上传文件
        $file = $_FILES['uploadFile'];
        $fileName = basename($file["name"]);
        if (strpos($fileName, "index") === false) { // 确保文件名不包含"index"
            move_uploaded_file($file["tmp_name"], "$directory/$fileName");
        }
    }
}

// 删除或下载文件处理
if (isset($_GET['action']) && isset($_GET['file'])) {
    $target = "$directory/" . basename($_GET['file']);
    if ($_GET['action'] == 'delete' && file_exists($target)) {
        unlink($target); // 删除文件
    } elseif ($_GET['action'] == 'download' && file_exists($target)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header("Content-Disposition: attachment; filename=$target");
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($target));
        readfile($target);
        exit;
    }
}

// 获取目录下的所有文件和文件夹信息
$files = scandir($directory);
?>

<table border="1">
    <tr>
        <th>文件名</th>
        <th>大小</th>
        <th>创建日期</th>
        <th>操作</th>
    </tr>
    <?php foreach ($files as $file): ?>
        <?php if ($file !== '.' && $file !== '..' && $file !== 'index.php' && $file !== 'web.config'): ?>
            <?php $filePath = "$directory/$file"; ?>
            <tr>
                <td><a href="<?php echo htmlspecialchars($file); ?>" target="_blank"><?php echo htmlspecialchars($file); ?></a></td>
                <td><?php echo filesize($filePath); ?> 字节</td>
                <td><?php echo date("Y-m-d H:i:s", filectime($filePath)); ?></td>
                <td>
                    <a href="?action=download&file=<?php echo urlencode($file); ?>">下载</a> |
                    <a href="?action=delete&file=<?php echo urlencode($file); ?>" onclick="return confirm('确定要删除吗？');">删除</a>
                </td>
            </tr>
        <?php endif; ?>
    <?php endforeach; ?>
</table>
</body>
</html>