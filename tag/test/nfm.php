<?php
// 设置时区
date_default_timezone_set('Asia/Shanghai');

// 基础目录（脚本所在目录）
$baseDir = __DIR__;

// 获取当前目录（防止目录遍历攻击）
$currentDir = $baseDir;
if (isset($_GET['dir'])) {
    $requestedDir = $_GET['dir'];
    $requestedPath = realpath($baseDir . DIRECTORY_SEPARATOR . $requestedDir);
    
    // 验证请求的目录是否在基础目录下
    if ($requestedPath && strpos($requestedPath, $baseDir) === 0) {
        $currentDir = $requestedPath;
    }
}

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['newDir'])) { // 创建新目录
        $newDir = $_POST['newDir'];
        if (!empty($newDir)) {
            $newDirPath = $currentDir . DIRECTORY_SEPARATOR . $newDir;
            if (!file_exists($newDirPath)) {
                mkdir($newDirPath, 0777, true);
            }
        }
    } elseif (isset($_FILES['uploadFile'])) { // 上传文件
        $file = $_FILES['uploadFile'];
        $fileName = basename($file["name"]);
        if (!empty($fileName) && strpos($fileName, "index") === false) {
            $targetPath = $currentDir . DIRECTORY_SEPARATOR . $fileName;
            move_uploaded_file($file["tmp_name"], $targetPath);
        }
    }
    
    // 防止表单重复提交
    header("Location: ".$_SERVER['REQUEST_URI']);
    exit;
}

// 删除或下载文件处理
if (isset($_GET['action']) && isset($_GET['file'])) {
    $target = $currentDir . DIRECTORY_SEPARATOR . basename($_GET['file']);
    $currentDirParam = isset($_GET['dir']) ? '&dir=' . urlencode($_GET['dir']) : '';
    
    if ($_GET['action'] == 'delete' && file_exists($target)) {
        if (is_dir($target)) {
            // 删除目录（需要空目录）
            @rmdir($target);
        } else {
            // 删除文件
            unlink($target);
        }
        // 重定向回当前目录
        header("Location: ?".$currentDirParam);
        exit;
    } elseif ($_GET['action'] == 'download' && file_exists($target) && !is_dir($target)) {
        $fileName = basename($target);
        $safeFileName = preg_replace('/[^\x20-\x7E]/', '', $fileName);
        
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $safeFileName . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($target));
        readfile($target);
        exit;
    }
}

// 获取当前目录下的所有文件和文件夹
$files = scandir($currentDir);
$fileCount = 0;
$folderCount = 0;
$totalSize = 0;

// 文件大小格式化函数
function formatSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' 字节';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文件管理</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .directory {
            font-weight: bold;
        }
        .breadcrumb {
            margin-bottom: 15px;
        }
        .breadcrumb a {
            text-decoration: none;
            color: #0066cc;
        }
        .stats {
            margin: 15px 0;
            padding: 10px;
            background: #f8f8f8;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <h1>文件管理器</h1>
    
    <!-- 面包屑导航 -->
    <div class="breadcrumb">
        <a href="?">根目录</a>
        <?php 
        $relativePath = str_replace($baseDir, '', $currentDir);
        $pathParts = explode(DIRECTORY_SEPARATOR, trim($relativePath, DIRECTORY_SEPARATOR));
        $currentPath = '';
        foreach ($pathParts as $part) {
            if (!empty($part)) {
                $currentPath .= $part . DIRECTORY_SEPARATOR;
                echo ' / <a href="?dir=' . urlencode($currentPath) . '">' . htmlspecialchars($part) . '</a>';
            }
        }
        ?>
    </div>

    <!-- 统计信息 -->
    <div class="stats">
        文件数量: <?php echo $fileCount; ?> | 
        文件夹数量: <?php echo $folderCount; ?> | 
        总大小: <?php echo formatSize($totalSize); ?>
    </div>

    <!-- 工具栏 -->
    <div>
        <form action="" method="post" enctype="multipart/form-data" style="display:inline-block; margin-right:10px;">
            <input type="file" name="uploadFile">
            <input type="submit" value="上传">
        </form>
        <form action="" method="post" style="display:inline-block;">
            <input type="text" name="newDir" placeholder="新文件夹名">
            <input type="submit" value="新建文件夹">
        </form>
    </div>

    <!-- 文件列表表格 -->
    <table>
        <tr>
            <th>名称</th>
            <th>类型</th>
            <th>大小</th>
            <th>修改日期</th>
            <th>操作</th>
        </tr>
        <!-- 上级目录链接 -->
        <?php if ($currentDir !== $baseDir): ?>
            <?php 
            $parentDir = dirname($currentDir);
            $relativeParent = str_replace($baseDir . DIRECTORY_SEPARATOR, '', $parentDir);
            ?>
            <tr>
                <td class="directory">
                    <a href="?dir=<?php echo urlencode($relativeParent); ?>">.. (上级目录)</a>
                </td>
                <td>目录</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
        <?php endif; ?>
        
        <?php 
        // 重新扫描目录并统计
        $files = scandir($currentDir);
        $fileCount = 0;
        $folderCount = 0;
        $totalSize = 0;
        
        foreach ($files as $file): ?>
            <?php if ($file !== '.' && $file !== '..'): ?>
                <?php 
                $filePath = $currentDir . DIRECTORY_SEPARATOR . $file;
                $isDir = is_dir($filePath);
                $isProtected = ($file === 'index.php' || $file === 'web.config');
                
                // 跳过受保护的文件
                if ($isProtected) continue;
                
                // 统计信息
                if ($isDir) {
                    $folderCount++;
                } else {
                    $fileCount++;
                    $totalSize += filesize($filePath);
                }
                
                // 获取相对路径用于URL
                $relativeFile = str_replace($baseDir . DIRECTORY_SEPARATOR, '', $filePath);
                ?>
                <tr>
                    <td class="<?php echo $isDir ? 'directory' : 'file'; ?>">
                        <?php if ($isDir): ?>
                            <!-- 目录：点击进入 -->
                            <a href="?dir=<?php echo urlencode($relativeFile); ?>">
                                <?php echo htmlspecialchars($file); ?>/
                            </a>
                        <?php else: ?>
                            <!-- 文件：直接链接到文件 -->
                            <a href="<?php echo htmlspecialchars($file); ?>">
                                <?php echo htmlspecialchars($file); ?>
                            </a>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $isDir ? '目录' : '文件'; ?></td>
                    <td><?php echo $isDir ? '-' : formatSize(filesize($filePath)); ?></td>
                    <td><?php echo date("Y-m-d H:i:s", filemtime($filePath)); ?></td>
                    <td>
                        <?php if (!$isDir): ?>
                            <a href="?action=download&file=<?php echo urlencode($file); ?>&dir=<?php echo urlencode($relativePath); ?>">下载</a> |
                        <?php endif; ?>
                        <a href="?action=delete&file=<?php echo urlencode($file); ?>&dir=<?php echo urlencode($relativePath); ?>" onclick="return confirm('确定要删除吗？');">删除</a>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </table>
</body>
</html>