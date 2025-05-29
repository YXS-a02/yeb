<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>PHP多媒体播放器</title>
    <style>
        body {
            background: #1a1a1a;
            color: white;
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            font-family: Arial, sans-serif;
            padding: 20px;
            gap: 20px;
        }

        .player-container {
            width: 60%;
            max-width: 800px;
            margin: 20px;
            background: #333;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
            order: 1; /* Place player first in flex order */
        }

        #mediaElement {
            width: 100%;
            border-radius: 10px 10px 0 0;
        }

        .controls {
            padding: 15px;
        }

        .progress-container {
            width: 100%;
            height: 5px;
            background: #555;
            margin: 10px 0;
            cursor: pointer;
        }

        #progressBar {
            height: 100%;
            background: #00ff9d;
            width: 0%;
        }

        .button-container {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        button {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: background 0.3s;
        }

        button:hover {
            background: rgba(255,255,255,0.1);
        }

        input[type="range"] {
            width: 100px;
            height: 4px;
            accent-color: #00ff9d;
        }

        .time-display {
            font-size: 0.9em;
            color: #ccc;
        }

        .file-list {
            width: 35%;
            max-width: 400px;
            background: #333;
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
            order: 2; /* Place file list second in flex order */
        }

        .file-item {
            padding: 10px;
            cursor: pointer;
            border-radius: 5px;
            margin: 5px 0;
        }

        .file-item:hover {
            background: rgba(255,255,255,0.1);
        }

        .file-item.active {
            background: #00ff9d;
            color: #1a1a1a;
        }

        video::-webkit-media-controls {
            display: none !important;
        }
    </style>
</head>
<body>
    <div class="player-container">
        <video id="mediaElement" controls="false"></video>
        <div class="controls">
            <div class="progress-container" id="progressContainer">
                <div id="progressBar"></div>
            </div>
            <div class="button-container">
                <button id="playPauseBtn">▶</button>
                <span class="time-display">
                    <span id="currentTime">0:00</span> / 
                    <span id="duration">0:00</span>
                </span>
                <input type="range" id="volumeSlider" min="0" max="1" step="0.1" value="1">
                <select id="playbackRate">
                    <option value="0.5">0.5x</option>
                    <option value="1" selected>1x</option>
                    <option value="1.5">1.5x</option>
                    <option value="2">2x</option>
                </select>
                <button id="fullscreenBtn">⤢</button>
            </div>
        </div>
    </div>

    <div class="file-list" id="fileList">
        <h3>服务器媒体文件列表</h3>
        <div id="fileItems">
            <?php
            // 定义vaa文件夹路径
            $mediaFolder = 'vaa';
            
            // 检查文件夹是否存在
            if (is_dir($mediaFolder)) {
                // 打开目录
                if ($dh = opendir($mediaFolder)) {
                    // 读取目录内容
                    while (($file = readdir($dh)) !== false) {
                        // 跳过当前目录和上级目录
                        if ($file == '.' || $file == '..') continue;
                        
                        // 获取文件扩展名
                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        
                        // 支持的媒体文件扩展名
                        $supportedFormats = ['mp4', 'webm', 'ogg', 'mp3', 'wav'];
                        
                        // 如果是支持的媒体文件
                        if (in_array($ext, $supportedFormats)) {
                            $filePath = urlencode($mediaFolder . '/' . $file);
                            echo "<div class='file-item' data-src='$filePath'>$file</div>";
                        }
                    }
                    closedir($dh);
                }
            } else {
                echo "<p>vaa文件夹不存在或无法访问</p>";
            }
            ?>
        </div>
    </div>

    <script>
        const mediaElement = document.getElementById('mediaElement');
        const playPauseBtn = document.getElementById('playPauseBtn');
        const progressBar = document.getElementById('progressBar');
        const progressContainer = document.getElementById('progressContainer');
        const currentTimeDisplay = document.getElementById('currentTime');
        const durationDisplay = document.getElementById('duration');
        const volumeSlider = document.getElementById('volumeSlider');
        const playbackRate = document.getElementById('playbackRate');
        const fullscreenBtn = document.getElementById('fullscreenBtn');
        const fileItems = document.querySelectorAll('.file-item');

        // 播放媒体文件
        function playMediaFile(filePath) {
            mediaElement.src = filePath;
            mediaElement.play()
                .then(() => {
                    playPauseBtn.textContent = '⏸';
                })
                .catch(error => {
                    console.error('播放失败:', error);
                });
        }

        // 为每个文件项添加点击事件
        fileItems.forEach(item => {
            item.addEventListener('click', () => {
                // 移除所有active类
                fileItems.forEach(i => {
                    i.classList.remove('active');
                });
                // 添加active类到当前项
                item.classList.add('active');
                // 播放选中的文件
                playMediaFile(item.dataset.src);
            });
        });

        // 播放/暂停控制
        playPauseBtn.addEventListener('click', () => {
            if (mediaElement.paused) {
                mediaElement.play();
                playPauseBtn.textContent = '⏸';
            } else {
                mediaElement.pause();
                playPauseBtn.textContent = '▶';
            }
        });

        // 进度条更新
        mediaElement.addEventListener('timeupdate', () => {
            const progress = (mediaElement.currentTime / mediaElement.duration) * 100;
            progressBar.style.width = `${progress}%`;
            currentTimeDisplay.textContent = formatTime(mediaElement.currentTime);
        });

        // 点击进度条跳转
        progressContainer.addEventListener('click', (e) => {
            const rect = progressContainer.getBoundingClientRect();
            const pos = (e.clientX - rect.left) / rect.width;
            mediaElement.currentTime = pos * mediaElement.duration;
        });

        // 音量控制
        volumeSlider.addEventListener('input', (e) => {
            mediaElement.volume = e.target.value;
        });

        // 播放速度控制
        playbackRate.addEventListener('change', (e) => {
            mediaElement.playbackRate = e.target.value;
        });

        // 全屏控制
        fullscreenBtn.addEventListener('click', () => {
            if (mediaElement.requestFullscreen) {
                mediaElement.requestFullscreen();
            } else if (mediaElement.webkitRequestFullscreen) {
                mediaElement.webkitRequestFullscreen();
            }
        });

        // 时间格式化
        function formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            seconds = Math.floor(seconds % 60);
            return `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }

        // 初始化持续时间显示
        mediaElement.addEventListener('loadedmetadata', () => {
            durationDisplay.textContent = formatTime(mediaElement.duration);
        });

        // 自动播放第一个文件（如果有）
        if (fileItems.length > 0) {
            fileItems[0].click();
        }
    </script>
</body>
</html>