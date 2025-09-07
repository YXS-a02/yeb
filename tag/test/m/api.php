<?php
// api.php - 媒体文件API
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// 默认工作目录
$baseDir = './';

// 处理API请求
if (isset($_GET['event'])) {
    switch ($_GET['event']) {
        case 'lmf': // 读取媒体文件列表
            echo json_encode(getMediaFiles());
            break;
            
        case 'mfi': // 获取媒体文件信息
            if (!isset($_GET['file'])) {
                echo json_encode(['error' => '缺少文件参数']);
                break;
            }
            $file = validateFilePath($_GET['file']);
            echo json_encode(getMediaFileInfo($file));
            break;
            
        case 'mfp': // 获取媒体文件封面
            if (!isset($_GET['file'])) {
                echo json_encode(['error' => '缺少文件参数']);
                break;
            }
            $file = validateFilePath($_GET['file']);
            getMediaFileCover($file);
            break;
            
        case 'mf': // 获取文件
            if (!isset($_GET['file'])) {
                echo json_encode(['error' => '缺少文件参数']);
                break;
            }
            $file = validateFilePath($_GET['file']);
            outputMediaFile($file);
            break;
            
        default:
            echo json_encode(['error' => '未知的事件类型']);
            break;
    }
} else {
    echo json_encode(['error' => '缺少事件参数']);
}

// 获取媒体文件列表
function getMediaFiles() {
    global $baseDir;
    $mediaFiles = [];
    $supportedFormats = ['mp4', 'webm', 'ogg', 'mp3', 'wav', 'm4a', 'flac', 'avi', 'mov', 'mkv'];
    
    if (!is_dir($baseDir)) {
        return ['error' => '目录不存在'];
    }
    
    if ($dh = opendir($baseDir)) {
        while (($file = readdir($dh)) !== false) {
            if ($file == '.' || $file == '..') continue;
            
            $filePath = $baseDir . $file;
            if (is_dir($filePath)) continue;
            
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($ext, $supportedFormats)) {
                $mediaFiles[] = [
                    'name' => $file,
                    'path' => $file,
                    'size' => filesize($filePath),
                    'type' => in_array($ext, ['mp3', 'wav', 'm4a', 'flac']) ? 'audio' : 'video'
                ];
            }
        }
        closedir($dh);
    }
    
    return ['files' => $mediaFiles];
}

// 获取媒体文件信息
function getMediaFileInfo($file) {
    global $baseDir;
    $filePath = $baseDir . $file;
    
    if (!file_exists($filePath)) {
        return ['error' => '文件不存在'];
    }
    
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $isAudio = in_array($ext, ['mp3', 'wav', 'm4a', 'flac']);
    
    // 获取媒体时长
    $duration = getMediaDuration($filePath);
    
    // 尝试获取封面信息
    $hasCover = checkMediaHasCover($filePath);
    
    return [
        'name' => $file,
        'path' => $file,
        'size' => filesize($filePath),
        'type' => $isAudio ? 'audio' : 'video',
        'duration' => $duration,
        'bitrate' => null,
        'has_cover' => $hasCover
    ];
}

// 检查媒体文件是否有封面
function checkMediaHasCover($filePath) {
    // 使用ffprobe检查是否有视频流或封面
    $cmd = "ffprobe -v quiet -print_format json -show_streams " . escapeshellarg($filePath);
    $output = shell_exec($cmd);
    $info = json_decode($output, true);
    
    if ($info && isset($info['streams'])) {
        foreach ($info['streams'] as $stream) {
            // 检查是否有视频流或封面艺术流
            if (($stream['codec_type'] == 'video' && $stream['disposition']['attached_pic'] == 1) || 
                (isset($stream['tags']['DURATION']) && $stream['codec_type'] == 'video')) {
                return true;
            }
        }
    }
    
    return false;
}

// 获取媒体时长（使用ffprobe）
function getMediaDuration($filePath) {
    $cmd = "ffprobe -v quiet -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 " . escapeshellarg($filePath);
    $duration = shell_exec($cmd);
    
    if ($duration !== null) {
        return floatval($duration);
    }
    
    return null;
}

// 获取媒体文件封面（使用ffmpeg提取）
function getMediaFileCover($file) {
    global $baseDir;
    $filePath = $baseDir . $file;
    
    if (!file_exists($filePath)) {
        outputDefaultCover();
        return;
    }
    
    // 临时封面文件路径
    $coverPath = sys_get_temp_dir() . '/cover_' . md5($filePath) . '.jpg';
    
    // 如果已有缓存的封面且未过期（1天），直接输出
    if (file_exists($coverPath) && (time() - filemtime($coverPath)) < 86400) {
        header('Content-Type: image/jpeg');
        readfile($coverPath);
        return;
    }
    
    // 使用ffmpeg提取封面
    $cmd = "ffmpeg -i " . escapeshellarg($filePath) . 
           " -an -vcodec mjpeg -vframes 1 -y " . escapeshellarg($coverPath) . " 2>&1";
    
    $output = shell_exec($cmd);
    
    // 检查是否成功提取封面
    if (file_exists($coverPath) && filesize($coverPath) > 0) {
        header('Content-Type: image/jpeg');
        readfile($coverPath);
        return;
    }
    
    // 尝试其他方法提取封面（针对音频文件）
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if (in_array($ext, ['mp3', 'm4a', 'flac'])) {
        // 使用ffmpeg提取音频封面
        $cmd = "ffmpeg -i " . escapeshellarg($filePath) . 
               " -an -vcodec copy -y " . escapeshellarg($coverPath) . " 2>&1";
        
        $output = shell_exec($cmd);
        
        if (file_exists($coverPath) && filesize($coverPath) > 0) {
            header('Content-Type: image/jpeg');
            readfile($coverPath);
            return;
        }
    }
    
    // 如果所有方法都失败，返回默认封面
    outputDefaultCover();
}

// 输出默认封面（Base64编码的PNG图像）
function outputDefaultCover() {
    // 简单的灰色默认封面
    $defaultCover = 'iVBORw0KGgoAAAANSUhEUgAAAJAAAACQAQMAAADdiHD7AAAABlBMVEUAAAAAAAClZ7nPAAAAAnRSTlMAAHaTzTgAAAA/SURBVFjDY2AgCv7//w8l/mMAGfD/P4j9H0r8J8D+j8T+j8T+j8T+j8T+j8T+j8T+j8T+j8T+j8T+j8T+j8T+DwDedAicpH1V5gAAAABJRU5ErkJggg==';
    
    header('Content-Type: image/png');
    echo base64_decode($defaultCover);
}

// 输出媒体文件
function outputMediaFile($file) {
    global $baseDir;
    $filePath = $baseDir . $file;
    
    if (!file_exists($filePath)) {
        echo json_encode(['error' => '文件不存在']);
        return;
    }
    
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $mimeTypes = [
        'mp4' => 'video/mp4',
        'webm' => 'video/webm',
        'ogg' => 'video/ogg',
        'mp3' => 'audio/mpeg',
        'wav' => 'audio/wav',
        'm4a' => 'audio/mp4',
        'flac' => 'audio/flac',
        'avi' => 'video/x-msvideo',
        'mov' => 'video/quicktime',
        'mkv' => 'video/x-matroska'
    ];
    
    if (!isset($mimeTypes[$ext])) {
        echo json_encode(['error' => '不支持的文件类型']);
        return;
    }
    
    // 设置适当的缓存头
    header('Content-Type: ' . $mimeTypes[$ext]);
    header('Content-Length: ' . filesize($filePath));
    header('Accept-Ranges: bytes');
    
    // 支持断点续传
    if (isset($_SERVER['HTTP_RANGE'])) {
        handleRangeRequest($filePath, $mimeTypes[$ext]);
    } else {
        readfile($filePath);
    }
}

// 处理范围请求（断点续传）
function handleRangeRequest($filePath, $mimeType) {
    $size = filesize($filePath);
    $start = 0;
    $end = $size - 1;
    $length = $size;
    
    // 解析Range头
    if (isset($_SERVER['HTTP_RANGE'])) {
        $range = $_SERVER['HTTP_RANGE'];
        if (preg_match('/bytes=(\d+)-(\d+)?/', $range, $matches)) {
            $start = intval($matches[1]);
            if (isset($matches[2])) {
                $end = intval($matches[2]);
            }
            $length = $end - $start + 1;
            
            header('HTTP/1.1 206 Partial Content');
            header("Content-Range: bytes $start-$end/$size");
        }
    }
    
    header('Content-Type: ' . $mimeType);
    header('Content-Length: ' . $length);
    header('Accept-Ranges: bytes');
    
    // 读取并输出文件的部分内容
    $file = fopen($filePath, 'rb');
    fseek($file, $start);
    $buffer = 1024 * 8;
    
    while (!feof($file) && ($p = ftell($file)) <= $end) {
        if ($p + $buffer > $end) {
            $buffer = $end - $p + 1;
        }
        echo fread($file, $buffer);
        flush();
    }
    
    fclose($file);
}

// 验证文件路径安全性
function validateFilePath($file) {
    // 防止目录遍历攻击
    $file = str_replace(['../', '..\\'], '', $file);
    $file = basename($file);
    return $file;
}
?>