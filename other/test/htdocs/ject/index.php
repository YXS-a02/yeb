<!DOCTYPE html>
<html>
<head>
    <title>文件管理</title>
    <style>
        /* 模态框的样式 */
        .modal {
            display: none; /* 默认隐藏 */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        /* 关闭按钮样式 */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
<h1>文件管理器</h1>

<!-- 触发模态框的按钮 -->
<div>
    <button id="uploadTrigger">文件上传</button>
    <button id="newDirTrigger">新建文件夹</button>
</div>

<!-- 文件上传模态框 -->
<div id="uploadModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <br>
        <form action="" method="post" enctype="multipart/form-data">
            选择文件上传:
            <input type="file" name="uploadFile">
            <input type="submit" value="上传">
        </form>
    </div>
</div>

<!-- 新建文件夹模态框 -->
<div id="newDirModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <br>
        <form action="" method="post">
            新建目录名称:
            <input type="text" name="newDir">
            <input type="submit" value="创建">
        </form>
    </div>
</div>

<?php
// 处理表单提交、删除下载等功能的PHP代码保持不变，请参考之前的回答。
?>

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

<script>
// 获取模态框元素
var uploadModal = document.getElementById("uploadModal");
var newDirModal = document.getElementById("newDirModal");

// 获取打开模态框的按钮
var uploadBtn = document.getElementById("uploadTrigger");
var newDirBtn = document.getElementById("newDirTrigger");

// 获取关闭按钮
var spanUpload = uploadModal.getElementsByClassName("close")[0];
var spanNewDir = newDirModal.getElementsByClassName("close")[0];

// 点击按钮打开对应的模态框
uploadBtn.onclick = function() { uploadModal.style.display = "block"; }
newDirBtn.onclick = function() { newDirModal.style.display = "block"; }

// 点击关闭按钮关闭模态框
spanUpload.onclick = function() { uploadModal.style.display = "none"; }
spanNewDir.onclick = function() { newDirModal.style.display = "none"; }

// 点击模态框外部区域也可以关闭模态框
window.onclick = function(event) {
    if (event.target == uploadModal) {
        uploadModal.style.display = "none";
    } else if (event.target == newDirModal) {
        newDirModal.style.display = "none";
    }
}
</script>

<!-- 文件列表等其他内容保持不变，请根据需要添加 -->
</body>
</html>