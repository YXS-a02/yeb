// api.js - API 相关功能

// 检查 API 是否可用
async function checkApiAvailability(apiUrl) {
    try {
        const response = await fetch(`${apiUrl}?event=test`);
        return response.ok;
    } catch (error) {
        return false;
    }
}

// 获取文件列表
async function fetchFileList(apiUrl) {
    try {
        const response = await fetch(`${apiUrl}?event=lmf`);
        const data = await response.json();
        return data;
    } catch (error) {
        throw new Error(`获取文件列表失败: ${error.message}`);
    }
}

// 获取媒体文件
function getMediaFileUrl(apiUrl, filePath) {
    return `${apiUrl}?event=mf&file=${encodeURIComponent(filePath)}`;
}

// 获取媒体信息
async function fetchMediaInfo(apiUrl, filePath) {
    try {
        const response = await fetch(`${apiUrl}?event=mfi&file=${encodeURIComponent(filePath)}`);
        const data = await response.json();
        return data;
    } catch (error) {
        throw new Error(`获取媒体信息失败: ${error.message}`);
    }
}

// 获取ID3信息
async function fetchID3Info(apiUrl, filePath) {
    try {
        const response = await fetch(`${apiUrl}?event=id3&file=${encodeURIComponent(filePath)}`);
        const data = await response.json();
        return data;
    } catch (error) {
        console.log('没有ID3信息');
        return null;
    }
}

// 获取封面
async function fetchCover(apiUrl, filePath) {
    try {
        const response = await fetch(`${apiUrl}?event=mfp&file=${encodeURIComponent(filePath)}`);
        
        if (response.ok && response.headers.get('content-type')?.startsWith('image/')) {
            return await response.blob();
        }
        throw new Error('没有封面或响应不是图片');
    } catch (error) {
        throw new Error(`获取封面失败: ${error.message}`);
    }
}

// 导出函数
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        checkApiAvailability,
        fetchFileList,
        getMediaFileUrl,
        fetchMediaInfo,
        fetchID3Info,
        fetchCover
    };
}