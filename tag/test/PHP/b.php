<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>多媒体播放器</title>
    <style>
        :root {
            --primary-color: #00ff9d;
            --background-dark: #1a1a1a;
            --background-medium: #252525;
            --background-light: #333;
            --text-color: #fff;
            --text-secondary: #ccc;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            background: var(--background-dark);
            color: var(--text-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            display: flex;
            grid-template-columns: 1fr 350px;
            gap: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .player-section {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .card {
            background: var(--background-light);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        
        .player-container {
            position: relative;
        }
        
        #mediaElement {
            width: 100%;
            border-radius: 10px 10px 0 0;
            background: #000;
            max-height: 480px;
        }
        
        .controls {
            padding: 15px;
            background: var(--background-medium);
            border-radius: 0 0 10px 10px;
        }
        
        .progress-container {
            width: 100%;
            height: 6px;
            background: #555;
            margin: 10px 0;
            cursor: pointer;
            border-radius: 3px;
            position: relative;
        }
        
        #progressBar {
            height: 100%;
            background: var(--primary-color);
            width: 0%;
            border-radius: 3px;
            transition: width 0.2s ease;
        }
        
        .button-container {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        button {
            background: none;
            border: none;
            color: var(--text-color);
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: background 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        button:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .btn-icon {
            font-size: 1.2rem;
            width: 24px;
            height: 24px;
        }
        
        input[type="range"] {
            width: 100px;
            height: 4px;
            accent-color: var(--primary-color);
        }
        
        .time-display {
            font-size: 0.9em;
            color: var(--text-secondary);
            margin: 0 10px;
        }
        
        .media-info {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .cover-art {
            width: 80px;
            height: 80px;
            border-radius: 5px;
            background: #444;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #666;
        }
        
        .cover-art img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .info-text {
            flex: 1;
        }
        
        .info-text h3 {
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .info-text p {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        
        .api-management {
            margin-bottom: 15px;
        }
        
        .api-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        select {
            flex: 1;
            padding: 8px 12px;
            border-radius: 5px;
            background: var(--background-medium);
            color: var(--text-color);
            border: 1px solid #444;
        }
        
        .api-form {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 10px;
        }
        
        input[type="text"] {
            padding: 8px 12px;
            border-radius: 5px;
            background: var(--background-medium);
            color: var(--text-color);
            border: 1px solid #444;
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: #000;
            border-radius: 5px;
            padding: 8px 15px;
            font-weight: bold;
        }
        
        .btn-primary:hover {
            background: #00e58c;
        }
        
        .file-list {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .file-list h3 {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .file-item {
            padding: 10px;
            cursor: pointer;
            border-radius: 5px;
            margin: 5px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background 0.2s;
        }
        
        .file-item:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        
        .file-item.active {
            background: var(--primary-color);
            color: #000;
        }
        
        .file-icon {
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
        }
        
        .file-name {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .history-list {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .history-item {
            padding: 10px;
            border-radius: 5px;
            margin: 5px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }
        
        .history-item:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        
        .history-info {
            flex: 1;
            overflow: hidden;
        }
        
        .history-name {
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .history-time {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }
        
        .empty-state {
            text-align: center;
            padding: 20px;
            color: var(--text-secondary);
        }
        
        .error-message {
            color: #ff6b6b;
            padding: 10px;
            background: rgba(255, 107, 107, 0.1);
            border-radius: 5px;
            margin: 10px 0;
        }
        
        /* 隐藏默认控件 */
        video::-webkit-media-controls {
            display: none !important;
        }
        
        @media (max-width: 992px) {
            .container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="player-section">
            <div class="player-container card">
                <video id="mediaElement" controls="false"></video>
                <div class="controls">
                    <div class="progress-container" id="progressContainer">
                        <div id="progressBar"></div>
                    </div>
                    <div class="button-container">
                        <button id="playPauseBtn" title="播放/暂停">
                            <span class="btn-icon">▶</span>
                        </button>
                        <span class="time-display">
                            <span id="currentTime">0:00</span> / 
                            <span id="duration">0:00</span>
                        </span>
                        <input type="range" id="volumeSlider" min="0" max="1" step="0.1" value="1" title="音量">
                        <select id="playbackRate" title="播放速度">
                            <option value="0.5">0.5x</option>
                            <option value="1" selected>1x</option>
                            <option value="1.5">1.5x</option>
                            <option value="2">2x</option>
                        </select>
                        <button id="fullscreenBtn" title="全屏">
                            <span class="btn-icon">⤢</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="media-info card">
                <div class="cover-art" id="coverArt">
                    <span>🎵</span>
                </div>
                <div class="info-text">
                    <h3 id="mediaTitle">选择媒体文件开始播放</h3>
                    <p id="mediaMeta">--</p>
                </div>
            </div>
        </div>
        
        <div class="sidebar">
            <div class="api-management card">
                <h3>API 管理</h3>
                <div class="api-selector">
                    <select id="apiSelector">
                        <option value="./api.php">默认 API (api.php)</option>
                    </select>
                    <button id="refreshApiBtn" title="刷新API" class="btn-primary">↻</button>
                </div>
                <div class="api-form">
                    <input type="text" id="newApiInput" placeholder="输入API URL">
                    <button id="addApiBtn" class="btn-primary">添加</button>
                </div>
                <div id="apiError" class="error-message" style="display: none;"></div>
            </div>
            
            <div class="file-list card">
                <h3>
                    <span>媒体文件列表</span>
                    <span id="loadingIndicator" style="display:none;">加载中...</span>
                </h3>
                <div id="fileItems">
                    <div class="empty-state">请选择API并刷新列表</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // 全局变量
        let db = null;
        let currentApi = './index.php';
        let currentMediaInfo = null;
        let dbVersion = 2;
        
        // DOM元素
        const mediaElement = document.getElementById('mediaElement');
        const playPauseBtn = document.getElementById('playPauseBtn');
        const progressBar = document.getElementById('progressBar');
        const progressContainer = document.getElementById('progressContainer');
        const currentTimeDisplay = document.getElementById('currentTime');
        const durationDisplay = document.getElementById('duration');
        const volumeSlider = document.getElementById('volumeSlider');
        const playbackRate = document.getElementById('playbackRate');
        const fullscreenBtn = document.getElementById('fullscreenBtn');
        const fileItemsContainer = document.getElementById('fileItems');
        const apiSelector = document.getElementById('apiSelector');
        const refreshApiBtn = document.getElementById('refreshApiBtn');
        const newApiInput = document.getElementById('newApiInput');
        const addApiBtn = document.getElementById('addApiBtn');
        const coverArt = document.getElementById('coverArt');
        const mediaTitle = document.getElementById('mediaTitle');
        const mediaMeta = document.getElementById('mediaMeta');
        const historyItems = document.getElementById('historyItems');
        const loadingIndicator = document.getElementById('loadingIndicator');
        const apiError = document.getElementById('apiError');
        
        // 初始化
        document.addEventListener('DOMContentLoaded', () => {
            initDatabase();
            setupEventListeners();
            
            // 默认加载第一个API的文件列表
            setTimeout(() => {
                refreshFileList();
            }, 500);
        });
        
        // 初始化IndexedDB
        function initDatabase() {
            const request = indexedDB.open('MediaPlayerDB', dbVersion);
            
            request.onerror = (event) => {
                console.error('数据库打开失败:', event.target.error);
                showError('数据库初始化失败: ' + event.target.error.message);
            };
            
            request.onupgradeneeded = (event) => {
                console.log('数据库升级需要，版本:', event.oldVersion, '->', event.newVersion);
                db = event.target.result;
                
                if (event.oldVersion < 1) {
                    if (!db.objectStoreNames.contains('apis')) {
                        const apiStore = db.createObjectStore('apis', { keyPath: 'id', autoIncrement: true });
                        apiStore.createIndex('url', 'url', { unique: true });
                        console.log('创建apis对象存储');
                    }
                    
                    if (!db.objectStoreNames.contains('history')) {
                        const historyStore = db.createObjectStore('history', { keyPath: 'id', autoIncrement: true });
                        historyStore.createIndex('timestamp', 'timestamp', { unique: false });
                        console.log('创建history对象存储');
                    }
                } else {
                    if (!db.objectStoreNames.contains('apis')) {
                        const apiStore = db.createObjectStore('apis', { keyPath: 'id', autoIncrement: true });
                        apiStore.createIndex('url', 'url', { unique: true });
                        console.log('创建apis对象存储');
                    }
                    
                    if (!db.objectStoreNames.contains('history')) {
                        const historyStore = db.createObjectStore('history', { keyPath: 'id', autoIncrement: true });
                        historyStore.createIndex('timestamp', 'timestamp', { unique: false });
                        console.log('创建history对象存储');
                    }
                }
            };
            
            request.onsuccess = (event) => {
                db = event.target.result;
                console.log('数据库初始化成功，版本:', db.version);
                
                db.onversionchange = () => {
                    db.close();
                    console.log('数据库已更新，请重新加载页面');
                    showError('数据库已更新，请重新加载页面');
                };
                
                loadApiList();
                loadPlaybackHistory();
            };
        }
        
        // 显示错误信息
        function showError(message) {
            apiError.textContent = message;
            apiError.style.display = 'block';
            setTimeout(() => {
                apiError.style.display = 'none';
            }, 5000);
        }
        
        // 加载API列表
        function loadApiList() {
            if (!db) {
                console.error('数据库未初始化');
                return;
            }
            
            if (!db.objectStoreNames.contains('apis')) {
                console.error('apis对象存储不存在');
                showError('数据库结构错误，请刷新页面');
                return;
            }
            
            try {
                const transaction = db.transaction(['apis'], 'readonly');
                const store = transaction.objectStore('apis');
                const request = store.getAll();
                
                request.onsuccess = (event) => {
                    const apis = event.target.result;
                    apiSelector.innerHTML = '<option value="./api.php">默认 API (api.php)</option>';
                    
                    apis.forEach(api => {
                        const option = document.createElement('option');
                        option.value = api.url;
                        option.textContent = api.name || api.url;
                        option.selected = api.url === currentApi;
                        apiSelector.appendChild(option);
                    });
                };
                
                request.onerror = (event) => {
                    console.error('加载API列表失败:', event.target.error);
                    showError('加载API列表失败: ' + event.target.error.message);
                };
            } catch (error) {
                console.error('访问数据库失败:', error);
                showError('访问数据库失败: ' + error.message);
            }
        }
        
        // 添加API
        function addApi() {
            const url = newApiInput.value.trim();
            if (!url) {
                showError('请输入API URL');
                return;
            }
            
            if (!db) {
                showError('数据库未就绪');
                return;
            }
            
            if (!db.objectStoreNames.contains('apis')) {
                showError('数据库结构错误，请刷新页面');
                return;
            }
            
            try {
                const transaction = db.transaction(['apis'], 'readwrite');
                const store = transaction.objectStore('apis');
                const request = store.add({ url, name: url, added: new Date() });
                
                request.onsuccess = (event) => {
                    newApiInput.value = '';
                    loadApiList();
                    console.log('API添加成功');
                };
                
                request.onerror = (event) => {
                    console.error('添加API失败:', event.target.error);
                    if (event.target.error.name === 'ConstraintError') {
                        showError('该API已存在');
                    } else {
                        showError('添加API失败: ' + event.target.error.message);
                    }
                };
            } catch (error) {
                console.error('访问数据库失败:', error);
                showError('访问数据库失败: ' + error.message);
            }
        }
        
        // 刷新文件列表
        function refreshFileList() {
            const apiUrl = apiSelector.value;
            if (!apiUrl) return;
            
            currentApi = apiUrl;
            loadingIndicator.style.display = 'block';
            fileItemsContainer.innerHTML = '';
            
            fetch(`${apiUrl}?event=lmf`)
                .then(response => response.json())
                .then(data => {
                    loadingIndicator.style.display = 'none';
                    
                    if (data.error) {
                        fileItemsContainer.innerHTML = `<div class="error-message">错误: ${data.error}</div>`;
                        return;
                    }
                    
                    if (!data.files || data.files.length === 0) {
                        fileItemsContainer.innerHTML = '<div class="empty-state">没有找到媒体文件</div>';
                        return;
                    }
                    
                    data.files.forEach(file => {
                        const fileItem = document.createElement('div');
                        fileItem.className = 'file-item';
                        fileItem.dataset.path = file.path;
                        
                        const icon = document.createElement('div');
                        icon.className = 'file-icon';
                        icon.innerHTML = file.type === 'audio' ? '🎵' : '🎬';
                        
                        const name = document.createElement('div');
                        name.className = 'file-name';
                        name.textContent = file.name;
                        
                        fileItem.appendChild(icon);
                        fileItem.appendChild(name);
                        
                        fileItem.addEventListener('click', () => {
                            document.querySelectorAll('.file-item').forEach(item => {
                                item.classList.remove('active');
                            });
                            
                            fileItem.classList.add('active');
                            
                            playMediaFile(file.path, file.name);
                            
                            fetchMediaInfo(file.path);
                        });
                        
                        fileItemsContainer.appendChild(fileItem);
                    });
                })
                .catch(error => {
                    loadingIndicator.style.display = 'none';
                    fileItemsContainer.innerHTML = `<div class="error-message">加载失败: ${error.message}</div>`;
                    console.error('加载文件列表失败:', error);
                });
        }
        
        // 获取媒体文件信息
        function fetchMediaInfo(filePath) {
            fetch(`${currentApi}?event=mfi&file=${encodeURIComponent(filePath)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('获取媒体信息失败:', data.error);
                        return;
                    }
                    
                    currentMediaInfo = data;
                    updateMediaInfoDisplay(data);
                    
                    fetchCover(data.path);
                })
                .catch(error => {
                    console.error('获取媒体信息失败:', error);
                });
        }
        
        // 更新媒体信息显示
        function updateMediaInfoDisplay(info) {
            mediaTitle.textContent = info.name;
            
            const sizeMB = (info.size / (1024 * 1024)).toFixed(2);
            
            let durationText = '--';
            if (info.duration) {
                durationText = formatTime(info.duration);
            }
            
            mediaMeta.textContent = `${info.type.toUpperCase()} | ${sizeMB} MB | ${durationText}`;
        }
        
        // 获取封面
        function fetchCover(filePath) {
            coverArt.innerHTML = '<span>🎵</span>';
            
            fetch(`${currentApi}?event=mfp&file=${encodeURIComponent(filePath)}`)
                .then(response => {
                    if (response.ok && response.headers.get('content-type').startsWith('image/')) {
                        return response.blob();
                    }
                    throw new Error('没有封面');
                })
                .then(blob => {
                    const url = URL.createObjectURL(blob);
                    coverArt.innerHTML = `<img src="${url}" alt="封面">`;
                })
                .catch(error => {
                    console.log('没有封面:', error.message);
                });
        }
        
        // 播放媒体文件
        function playMediaFile(filePath, fileName) {
            const url = `${currentApi}?event=mf&file=${encodeURIComponent(filePath)}`;
            mediaElement.src = url;
            
            mediaElement.play()
                .then(() => {
                    playPauseBtn.innerHTML = '<span class="btn-icon">⏸</span>';
                    addToPlaybackHistory(filePath, fileName);
                })
                .catch(error => {
                    console.error('播放失败:', error);
                    showError('播放失败: ' + error.message);
                });
        }
        
        // 添加到播放历史
        function addToPlaybackHistory(filePath, fileName) {
            if (!db) return;
            
            if (!db.objectStoreNames.contains('history')) {
                console.error('history对象存储不存在');
                return;
            }
            
            const timestamp = new Date();
            const historyItem = {
                filePath,
                fileName,
                timestamp: timestamp.getTime(),
                displayTime: timestamp.toLocaleString()
            };
            
            try {
                const transaction = db.transaction(['history'], 'readwrite');
                const store = transaction.objectStore('history');
                const request = store.add(historyItem);
                
                request.onsuccess = () => {
                    loadPlaybackHistory();
                };
                
                request.onerror = (event) => {
                    console.error('保存播放历史失败:', event.target.error);
                };
            } catch (error) {
                console.error('访问数据库失败:', error);
            }
        }
        
        // 加载播放历史
        function loadPlaybackHistory() {
            if (!db) return;
            
            if (!db.objectStoreNames.contains('history')) {
                console.error('history对象存储不存在');
                return;
            }
            
            try {
                const transaction = db.transaction(['history'], 'readonly');
                const store = transaction.objectStore('history');
                const index = store.index('timestamp');
                const request = index.openCursor(null, 'prev');
                
                historyItems.innerHTML = '';
                let count = 0;
                
                request.onsuccess = (event) => {
                    const cursor = event.target.result;
                    
                    if (cursor && count < 20) {
                        const item = cursor.value;
                        
                        const historyItem = document.createElement('div');
                        historyItem.className = 'history-item';
                        
                        const icon = document.createElement('div');
                        icon.className = 'file-icon';
                        icon.innerHTML = '🕒';
                        
                        const info = document.createElement('div');
                        info.className = 'history-info';
                        
                        const name = document.createElement('div');
                        name.className = 'history-name';
                        name.textContent = item.fileName;
                        
                        const time = document.createElement('div');
                        time.className = 'history-time';
                        time.textContent = item.displayTime;
                        
                        info.appendChild(name);
                        info.appendChild(time);
                        
                        historyItem.appendChild(icon);
                        historyItem.appendChild(info);
                        
                        historyItem.addEventListener('click', () => {
                            playMediaFile(item.filePath, item.fileName);
                            fetchMediaInfo(item.filePath);
                        });
                        
                        historyItems.appendChild(historyItem);
                        count++;
                        cursor.continue();
                    } else if (historyItems.children.length === 0) {
                        historyItems.innerHTML = '<div class="empty-state">暂无播放记录</div>';
                    }
                };
                
                request.onerror = (event) => {
                    console.error('加载播放历史失败:', event.target.error);
                };
            } catch (error) {
                console.error('访问数据库失败:', error);
            }
        }
        
        // 修复进度条点击跳转功能
        function setupProgressBarClick() {
            progressContainer.addEventListener('click', (e) => {
                // 确保媒体已加载且有时长
                if (!mediaElement.src || isNaN(mediaElement.duration) || mediaElement.duration <= 0) {
                    console.log('媒体未加载完成，无法跳转');
                    return;
                }
                
                const rect = progressContainer.getBoundingClientRect();
                // 计算点击位置在进度条上的百分比
                const clickPosition = (e.clientX - rect.left) / rect.width;
                // 确保百分比在0-1之间
                const percent = Math.max(0, Math.min(1, clickPosition));
                // 计算对应的播放时间
                const newTime = percent * mediaElement.duration;
                
                console.log(`跳转到: ${newTime.toFixed(2)} 秒 (${percent.toFixed(2)}%)`);
                
                // 设置新的播放时间
                mediaElement.currentTime = newTime;
                
                // 更新进度条显示
                progressBar.style.width = `${percent * 100}%`;
            });
        }
        
        // 设置事件监听器
        function setupEventListeners() {
            // 播放/暂停控制
            playPauseBtn.addEventListener('click', () => {
                if (mediaElement.paused) {
                    mediaElement.play();
                    playPauseBtn.innerHTML = '<span class="btn-icon">⏸</span>';
                } else {
                    mediaElement.pause();
                    playPauseBtn.innerHTML = '<span class="btn-icon">▶</span>';
                }
            });
            
            // 进度条更新
            mediaElement.addEventListener('timeupdate', () => {
                // 确保有有效的时长
                if (mediaElement.duration && mediaElement.duration > 0) {
                    const progress = (mediaElement.currentTime / mediaElement.duration) * 100;
                    progressBar.style.width = `${progress}%`;
                    currentTimeDisplay.textContent = formatTime(mediaElement.currentTime);
                }
            });
            
            // 设置进度条点击事件
            setupProgressBarClick();
            
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
                } else if (mediaElement.msRequestFullscreen) {
                    mediaElement.msRequestFullscreen();
                }
            });
            
            // API选择变化
            apiSelector.addEventListener('change', () => {
                refreshFileList();
            });
            
            // 刷新API按钮
            refreshApiBtn.addEventListener('click', () => {
                refreshFileList();
            });
            
            // 添加API按钮
            addApiBtn.addEventListener('click', addApi);
            
            // 按Enter添加API
            newApiInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    addApi();
                }
            });
            
            // 初始化持续时间显示
            mediaElement.addEventListener('loadedmetadata', () => {
                if (mediaElement.duration && mediaElement.duration > 0) {
                    durationDisplay.textContent = formatTime(mediaElement.duration);
                }
            });
            
            // 媒体结束事件
            mediaElement.addEventListener('ended', () => {
                playPauseBtn.innerHTML = '<span class="btn-icon">▶</span>';
            });
            
            // 媒体加载错误处理
            mediaElement.addEventListener('error', () => {
                console.error('媒体加载错误');
                showError('媒体加载失败，请检查文件格式或网络连接');
            });
        }
        
        // 时间格式化
        function formatTime(seconds) {
            if (isNaN(seconds)) return '0:00';
            
            const minutes = Math.floor(seconds / 60);
            seconds = Math.floor(seconds % 60);
            return `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }
    </script>
</body>
</html>