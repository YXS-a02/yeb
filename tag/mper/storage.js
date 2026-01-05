// storage.js - 存储相关功能

class StorageManager {
    constructor(dbName = 'MediaPlayerDB', version = 1) {
        this.dbName = dbName;
        this.version = version;
        this.db = null;
        this.isReady = false;
    }

    // 初始化数据库
    async init() {
        return new Promise((resolve, reject) => {
            try {
                const request = indexedDB.open(this.dbName, this.version);

                request.onerror = (event) => {
                    console.error('数据库打开失败:', event.target.error);
                    reject(event.target.error);
                };

                request.onupgradeneeded = (event) => {
                    console.log('数据库升级需要');
                    this.db = event.target.result;

                    // 创建或更新对象存储
                    if (!this.db.objectStoreNames.contains('apis')) {
                        const apiStore = this.db.createObjectStore('apis', { keyPath: 'id', autoIncrement: true });
                        apiStore.createIndex('url', 'url', { unique: true });
                        console.log('创建apis对象存储');
                    }

                    if (!this.db.objectStoreNames.contains('history')) {
                        const historyStore = this.db.createObjectStore('history', { keyPath: 'id', autoIncrement: true });
                        historyStore.createIndex('timestamp', 'timestamp', { unique: false });
                        console.log('创建history对象存储');
                    }

                    if (!this.db.objectStoreNames.contains('settings')) {
                        const settingsStore = this.db.createObjectStore('settings', { keyPath: 'key' });
                        console.log('创建settings对象存储');
                    }
                };

                request.onsuccess = (event) => {
                    this.db = event.target.result;
                    this.isReady = true;
                    console.log('数据库初始化成功');

                    this.db.onversionchange = () => {
                        this.db.close();
                        console.log('数据库已更新，请重新加载页面');
                    };

                    resolve();
                };
            } catch (error) {
                console.error('数据库初始化异常:', error);
                reject(error);
            }
        });
    }

    // API 管理
    async getAllApis() {
        if (!this.isReady) throw new Error('数据库未就绪');
        
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['apis'], 'readonly');
            const store = transaction.objectStore('apis');
            const request = store.getAll();

            request.onsuccess = (event) => resolve(event.target.result);
            request.onerror = (event) => reject(event.target.error);
        });
    }

    async addApi(url, name = null) {
        if (!this.isReady) throw new Error('数据库未就绪');
        
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['apis'], 'readwrite');
            const store = transaction.objectStore('apis');
            const request = store.add({
                url,
                name: name || url,
                added: new Date().toISOString()
            });

            request.onsuccess = (event) => resolve(event.target.result);
            request.onerror = (event) => reject(event.target.error);
        });
    }

    async deleteApi(id) {
        if (!this.isReady) throw new Error('数据库未就绪');
        
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['apis'], 'readwrite');
            const store = transaction.objectStore('apis');
            const request = store.delete(id);

            request.onsuccess = () => resolve();
            request.onerror = (event) => reject(event.target.error);
        });
    }

    async deleteApiByUrl(url) {
        const apis = await this.getAllApis();
        const api = apis.find(a => a.url === url);
        if (api) {
            return await this.deleteApi(api.id);
        }
        throw new Error('API未找到');
    }

    // 播放历史
    async addPlayHistory(filePath, fileName) {
        if (!this.isReady) return;
        
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['history'], 'readwrite');
            const store = transaction.objectStore('history');
            const request = store.add({
                filePath,
                fileName,
                timestamp: Date.now(),
                displayTime: new Date().toLocaleString()
            });

            request.onsuccess = () => resolve();
            request.onerror = (event) => reject(event.target.error);
        });
    }

    async getPlayHistory(limit = 50) {
        if (!this.isReady) return [];
        
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['history'], 'readonly');
            const store = transaction.objectStore('history');
            const index = store.index('timestamp');
            const request = index.getAll();

            request.onsuccess = (event) => {
                const history = event.target.result
                    .sort((a, b) => b.timestamp - a.timestamp)
                    .slice(0, limit);
                resolve(history);
            };
            request.onerror = (event) => reject(event.target.error);
        });
    }

    // 设置管理
    async getSetting(key, defaultValue = null) {
        if (!this.isReady) return defaultValue;
        
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['settings'], 'readonly');
            const store = transaction.objectStore('settings');
            const request = store.get(key);

            request.onsuccess = (event) => {
                resolve(event.target.result ? event.target.result.value : defaultValue);
            };
            request.onerror = (event) => reject(event.target.error);
        });
    }

    async setSetting(key, value) {
        if (!this.isReady) return;
        
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['settings'], 'readwrite');
            const store = transaction.objectStore('settings');
            const request = store.put({ key, value });

            request.onsuccess = () => resolve();
            request.onerror = (event) => reject(event.target.error);
        });
    }
}

// 创建全局实例
const storageManager = new StorageManager();

// 导出
if (typeof module !== 'undefined' && module.exports) {
    module.exports = storageManager;
}