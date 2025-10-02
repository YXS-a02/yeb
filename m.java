import javax.sound.sampled.*;
import java.io.*;
import java.net.*;
import java.util.*;
import java.util.concurrent.atomic.AtomicBoolean;

public class TermuxAudioPlayer {
    private Clip clip;
    private AtomicBoolean isPlaying = new AtomicBoolean(false);
    private AtomicBoolean isPaused = new AtomicBoolean(false);
    private String currentFile;
    private String currentApi = "http://your-api-server/api.php";
    private List<MediaFile> mediaFiles = new ArrayList<>();
    private Map<String, String> apiEndpoints = new HashMap<>();
    private Scanner scanner;
    
    // 音频信息
    private String currentTitle = "选择媒体文件开始播放";
    private String currentMeta = "--";
    private long currentDuration = 0;
    
    class MediaFile {
        String name;
        String path;
        String type;
        long size;
        long duration;
        
        MediaFile(String name, String path, String type, long size, long duration) {
            this.name = name;
            this.path = path;
            this.type = type;
            this.size = size;
            this.duration = duration;
        }
        
        @Override
        public String toString() {
            String typeIcon = "audio".equals(type) ? "🎵" : "🎬";
            String sizeMB = String.format("%.2f", size / (1024.0 * 1024.0));
            String durationStr = formatTime(duration);
            return String.format("%s %s | %s MB | %s", typeIcon, name, sizeMB, durationStr);
        }
    }
    
    public TermuxAudioPlayer() {
        scanner = new Scanner(System.in);
        initializeApiEndpoints();
    }
    
    private void initializeApiEndpoints() {
        apiEndpoints.put("默认API", "http://your-api-server/api.php");
    }
    
    // 修复了弃用警告的播放方法
    public void playLocalFile(String filePath) {
        try {
            stopCurrentPlayback();
            
            File audioFile = new File(filePath);
            if (!audioFile.exists()) {
                System.out.println("文件不存在: " + filePath);
                return;
            }
            
            AudioInputStream audioStream = AudioSystem.getAudioInputStream(audioFile);
            clip = AudioSystem.getClip();
            clip.open(audioStream);
            
            // 使用新的监听器方式
            clip.addLineListener(event -> {
                if (event.getType() == LineEvent.Type.STOP) {
                    synchronized(isPlaying) {
                        if (!isPaused.get()) {
                            isPlaying.set(false);
                            System.out.println("\n播放结束");
                        }
                    }
                }
            });
            
            clip.start();
            isPlaying.set(true);
            isPaused.set(false);
            currentFile = filePath;
            
            System.out.println("正在播放: " + audioFile.getName());
            
        } catch (UnsupportedAudioFileException | IOException | LineUnavailableException e) {
            System.out.println("播放错误: " + e.getMessage());
            e.printStackTrace();
        }
    }
    
    // 修复了弃用警告的网络播放方法
    public void playNetworkAudio(String filePath, String fileName) {
        try {
            stopCurrentPlayback();
            System.out.println("正在加载: " + fileName);
            
            String playUrl = currentApi + "?event=mf&file=" + 
                URLEncoder.encode(filePath, StandardCharsets.UTF_8.toString());
            
            File tempFile = downloadToTempFile(playUrl, fileName);
            if (tempFile == null) {
                System.out.println("下载文件失败");
                return;
            }
            
            playLocalFile(tempFile.getAbsolutePath());
            fetchMediaInfo(filePath, fileName);
            currentFile = filePath;
            
        } catch (Exception e) {
            System.out.println("播放失败: " + e.getMessage());
            e.printStackTrace();
        }
    }
    
    // 修复的停止播放方法
    private void stopCurrentPlayback() {
        if (clip != null) {
            try {
                // 先停止播放
                if (clip.isRunning()) {
                    clip.stop();
                }
                // 然后关闭资源
                clip.close();
            } catch (Exception e) {
                System.out.println("停止播放时出错: " + e.getMessage());
            }
        }
        isPlaying.set(false);
        isPaused.set(false);
    }
    
    public void stop() {
        stopCurrentPlayback();
        System.out.println("播放已停止");
    }
    
    public void pause() {
        if (clip != null && clip.isRunning()) {
            clip.stop();
            isPaused.set(true);
            System.out.println("播放已暂停");
        }
    }
    
    public void resume() {
        if (clip != null && !clip.isRunning() && isPaused.get()) {
            clip.start();
            isPaused.set(false);
            System.out.println("继续播放");
        }
    }
    
    // 修复的音量控制方法
    public void setVolume(float volume) {
        if (clip != null && clip.isControlSupported(FloatControl.Type.MASTER_GAIN)) {
            try {
                FloatControl gainControl = (FloatControl) clip.getControl(FloatControl.Type.MASTER_GAIN);
                float min = gainControl.getMinimum();
                float max = gainControl.getMaximum();
                float dB = min + (max - min) * volume;
                gainControl.setValue(dB);
                System.out.println("音量已设置为: " + (volume * 100) + "%");
            } catch (IllegalArgumentException e) {
                System.out.println("音量设置失败: " + e.getMessage());
            }
        } else {
            System.out.println("该音频设备不支持音量控制");
        }
    }
    
    // 修复的文件下载方法
    private File downloadToTempFile(String fileUrl, String fileName) {
        try {
            URL url = new URL(fileUrl);
            HttpURLConnection conn = (HttpURLConnection) url.openConnection();
            conn.setRequestMethod("GET");
            conn.setConnectTimeout(30000);
            conn.setReadTimeout(30000);
            
            // 创建安全的临时文件名
            String safeFileName = fileName.replaceAll("[^a-zA-Z0-9.-]", "_");
            String tempDir = System.getProperty("java.io.tmpdir");
            File tempFile = new File(tempDir, "audio_" + System.currentTimeMillis() + "_" + safeFileName);
            
            try (InputStream in = conn.getInputStream();
                 FileOutputStream out = new FileOutputStream(tempFile)) {
                
                byte[] buffer = new byte[8192];
                int bytesRead;
                long totalRead = 0;
                long contentLength = conn.getContentLengthLong();
                
                while ((bytesRead = in.read(buffer)) != -1) {
                    out.write(buffer, 0, bytesRead);
                    totalRead += bytesRead;
                    
                    // 显示下载进度
                    if (contentLength > 0) {
                        int progress = (int) ((totalRead * 100) / contentLength);
                        System.out.printf("下载进度: %d%%\r", progress);
                    }
                }
            }
            
            conn.disconnect();
            System.out.println("\n下载完成: " + tempFile.getAbsolutePath());
            return tempFile;
            
        } catch (Exception e) {
            System.out.println("下载失败: " + e.getMessage());
            return null;
        }
    }
    
    // 简化的媒体信息获取
    private void fetchMediaInfo(String filePath, String fileName) {
        currentTitle = fileName;
        currentMeta = "网络音频文件";
        displayCurrentTrackInfo();
    }
    
    private void displayCurrentTrackInfo() {
        System.out.println("\n=== 当前播放 ===");
        System.out.println("标题: " + currentTitle);
        System.out.println("信息: " + currentMeta);
        System.out.println("================\n");
    }
    
    private String formatTime(long seconds) {
        if (seconds <= 0) return "0:00";
        long minutes = seconds / 60;
        long secs = seconds % 60;
        return String.format("%d:%02d", minutes, secs);
    }
    
    // 简化的文件列表获取
    public void fetchMediaFiles() {
        System.out.println("模拟获取文件列表...");
        mediaFiles.clear();
        // 添加一些示例文件
        mediaFiles.add(new MediaFile("示例音乐1.mp3", "/music/sample1.mp3", "audio", 1024000, 180));
        mediaFiles.add(new MediaFile("示例音乐2.mp3", "/music/sample2.mp3", "audio", 2048000, 240));
        mediaFiles.add(new MediaFile("示例音频.wav", "/music/sample.wav", "audio", 512000, 120));
        System.out.println("加载了 " + mediaFiles.size() + " 个示例文件");
    }
    
    private void displayFileList() {
        System.out.println("\n=== 媒体文件列表 ===");
        if (mediaFiles.isEmpty()) {
            System.out.println("没有可用的媒体文件");
            return;
        }
        
        for (int i = 0; i < mediaFiles.size(); i++) {
            System.out.printf("%2d. %s\n", i + 1, mediaFiles.get(i));
        }
        System.out.println("===================\n");
    }
    
    // 用户界面方法
    public void showMainMenu() {
        System.out.println("=== Termux 音频播放器 ===");
        System.out.println("1. 刷新文件列表");
        System.out.println("2. 显示文件列表");
        System.out.println("3. 播放网络文件");
        System.out.println("4. 播放本地文件");
        System.out.println("5. 播放控制");
        System.out.println("6. 设置音量");
        System.out.println("7. 显示当前播放信息");
        System.out.println("8. 退出");
        System.out.print("请选择操作: ");
    }
    
    public void start() {
        System.out.println("音频播放器已启动");
        fetchMediaFiles();
        
        while (true) {
            showMainMenu();
            String choice = scanner.nextLine().trim();
            
            switch (choice) {
                case "1":
                    fetchMediaFiles();
                    break;
                case "2":
                    displayFileList();
                    break;
                case "3":
                    playNetworkFileMenu();
                    break;
                case "4":
                    playLocalFileMenu();
                    break;
                case "5":
                    playbackControlMenu();
                    break;
                case "6":
                    volumeControlMenu();
                    break;
                case "7":
                    displayCurrentTrackInfo();
                    break;
                case "8":
                    stopCurrentPlayback();
                    System.out.println("再见!");
                    return;
                default:
                    System.out.println("无效选择");
            }
        }
    }
    
    private void playNetworkFileMenu() {
        if (mediaFiles.isEmpty()) {
            System.out.println("没有可用的文件，请先刷新文件列表");
            return;
        }
        
        displayFileList();
        System.out.print("请输入文件编号: ");
        try {
            int fileIndex = Integer.parseInt(scanner.nextLine().trim()) - 1;
            if (fileIndex >= 0 && fileIndex < mediaFiles.size()) {
                MediaFile selectedFile = mediaFiles.get(fileIndex);
                playNetworkAudio(selectedFile.path, selectedFile.name);
            } else {
                System.out.println("无效的文件编号");
            }
        } catch (NumberFormatException e) {
            System.out.println("请输入有效的数字");
        }
    }
    
    private void playLocalFileMenu() {
        System.out.print("请输入本地文件路径: ");
        String filePath = scanner.nextLine().trim();
        if (filePath.isEmpty()) {
            System.out.println("使用示例音频文件");
            // 在Termux中尝试播放系统声音作为示例
            playLocalFile("/system/media/audio/notifications/OnTheHunt.ogg");
        } else {
            playLocalFile(filePath);
        }
    }
    
    private void playbackControlMenu() {
        System.out.println("\n=== 播放控制 ===");
        System.out.println("1. 播放/暂停");
        System.out.println("2. 停止");
        System.out.println("3. 继续播放");
        System.out.println("4. 返回主菜单");
        System.out.print("请选择操作: ");
        
        String choice = scanner.nextLine().trim();
        switch (choice) {
            case "1":
                if (isPlaying.get()) {
                    if (isPaused.get()) {
                        resume();
                    } else {
                        pause();
                    }
                } else {
                    System.out.println("没有正在播放的文件");
                }
                break;
            case "2":
                stop();
                break;
            case "3":
                resume();
                break;
            case "4":
                return;
            default:
                System.out.println("无效选择");
        }
    }
    
    private void volumeControlMenu() {
        System.out.print("请输入音量级别 (0-100): ");
        try {
            int volume = Integer.parseInt(scanner.nextLine().trim());
            if (volume >= 0 && volume <= 100) {
                setVolume(volume / 100.0f);
            } else {
                System.out.println("音量值必须在0到100之间");
            }
        } catch (NumberFormatException e) {
            System.out.println("请输入有效的数字");
        }
    }
    
    public static void main(String[] args) {
        // 设置字符编码以避免中文显示问题
        try {
            System.setOut(new PrintStream(System.out, true, "UTF-8"));
        } catch (Exception e) {
            // 忽略编码设置错误
        }
        
        new TermuxAudioPlayer().start();
    }
}