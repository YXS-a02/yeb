<?php
// 设置基础目录
$base_dir = 'user_dirs'; // 所有用户目录的父目录
$dir = isset($_GET['dir']) ? trim($_GET['dir']) : 'default';
$file = isset($_GET['file']) ? $_GET['file'] : '';
$content = '';
$message = '';

// 过滤目录名称（只允许字母、数字、下划线和连字符）
$directory = preg_replace('/[^a-zA-Z0-9_-]/', '', $dir);

// 确保目录存在
if (!file_exists($directory)) {
    mkdir($directory, 0755, true);
}

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save'])) {
        $filename = isset($_POST['filename']) ? trim($_POST['filename']) : '';
        $content = isset($_POST['content']) ? $_POST['content'] : '';
        
        if (!empty($filename)) {
            // 确保文件名安全
            $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $filename);
            if (!strpos($filename, '.')) {
                $filename .= '.txt';
            }
            
            $filepath = $directory . '/' . $filename;
            if (file_put_contents($filepath, $content) !== false) {
                $message = "文件保存成功！";
                $file = $filename;
            } else {
                $message = "保存文件时出错！";
            }
        } else {
            $message = "请输入文件名！";
        }
    }
}

// 如果是编辑现有文件，读取内容
if (!empty($file) && file_exists($directory . '/' . $file)) {
    $content = file_get_contents($directory . '/' . $file);
}

// 获取目录中的文件列表
$files = [];
if ($handle = opendir($directory)) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != ".." && !is_dir($directory . '/' . $entry)) {
            $files[] = $entry;
        }
    }
    closedir($handle);
    sort($files);
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>简易PHP文本编辑器</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
        }
        .container {
            display: flex;
            gap: 20px;
        }
        .file-list {
            width: 200px;
        }
        .editor {
            flex-grow: 1;
        }
        textarea {
            width: 100%;
            height: 400px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: monospace;
        }
        input[type="text"], button {
            padding: 8px 12px;
            margin: 5px 0;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            background-color: #f0f0f0;
            border-radius: 4px;
        }
        .file-item {
            padding: 5px;
            cursor: pointer;
        }
        .file-item:hover {
            background-color: #f0f0f0;
        }
        .file-item.active {
            background-color: #d0e0ff;
            font-weight: bold;
        }
        .directory-selector {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <h1>简易PHP文本编辑器</h1>
    
    <?php if (!empty($message)): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    
    <div class="directory-selector">
        <form method="get">
            <label>当前目录：
                <input type="text" name="dir" value="<?php echo htmlspecialchars($dir); ?>">
            </label>
            <button type="submit">切换</button>
        </form>
    </div>
    
    <div class="container">
        <div class="file-list">
            <h3>文件列表</h3>
            <?php foreach ($files as $f): ?>
                <div class="file-item <?php echo ($f === $file) ? 'active' : ''; ?>">
                    <a href="?file=<?php echo urlencode($f); ?>&dir=<?php echo urlencode($dir); ?>"><?php echo htmlspecialchars($f); ?></a>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="editor">
            <form method="post" action="?dir=<?php echo urlencode($dir); ?>">
                <div>
                    <label for="filename">文件名:</label>
                    <input type="text" id="filename" name="filename" value="<?php echo htmlspecialchars($file); ?>" required>
                </div>
                <div>
                    <textarea id="content" name="content" placeholder="在这里输入文本内容..."><?php echo htmlspecialchars($content); ?></textarea>
                </div>
                <div>
                    <button type="submit" name="save">保存</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>