<?php
/**
 * 加载并解析配置文件
 * 
 * @param string $configFilePath 配置文件路径
 * @return array 返回解析后的配置数组
 * @throws Exception 如果配置文件不存在、无法读取或格式错误会抛出异常
 */
function loadConfig($configFilePath) {
    if (!file_exists($configFilePath)) {
        throw new Exception('配置文件不存在');
    }

    $jsonContent = file_get_contents($configFilePath);
    if ($jsonContent === false) {
        throw new Exception('无法读取配置文件');
    }

    $config = json_decode($jsonContent, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('配置文件格式错误: ' . json_last_error_msg());
    }

    return $config;
}

function getDatabaseConnection($f_db_config = [],$f_db_name) {
    // 设置默认配置，允许传入参数覆盖
    $host = $f_db_config['DB_HOST'] ?? '127.0.0.1';
    $dbname = $f_db_name ?? '';
    $username = $f_db_config['DB_USER'] ?? 'root';
    $password = $f_db_config['DB_PASS'] ?? '150abcd051';

    try {
        // 创建PDO连接
        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8", 
            $username, 
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        // 开发环境返回详细错误，生产环境隐藏敏感信息
        $errorDetail = (ENVIRONMENT === 'development') 
            ? ['error' => $e->getMessage()] 
            : [];
        
        header('Content-Type: application/json');
        die(json_encode(array_merge(
            ['status' => 'error', 'msg' => '数据库连接失败'],
            $errorDetail
        )));
    }
}
?>