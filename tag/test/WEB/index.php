<!DOCTYPE html>
<html>
<head>
    <title>文件管理</title>
    <style>
        .inline-forms { display: flex; }
        .inline-forms form { margin-right: 10px; }
    </style>
</head>
<body>
<h1>文件管理器</h1>

<!-- Inline forms for upload and new directory -->
<div class="inline-forms">
    <!-- 文件上传表单 -->
    <form action="" method="post" enctype="multipart/form-data">
        上传
        <input type="file" name="uploadFile" style="font-size:10px">
        <input type="submit" value="上传">
    </form>

    <!-- 创建新目录表单 -->
    <form action="" method="post">
        新建目录
        <input type="text" name="newDir">
        <input type="submit" value="创建">
    </form>
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
        <?php if ($file !== '.' && $file !== '..'): ?>
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