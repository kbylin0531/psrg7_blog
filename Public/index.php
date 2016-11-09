<?php
//调试模式
const SR_DEBUG_MODE_ON = true;
//包含web模块
include '../Sharin/web.inc';
//初始化
Sharin::register();
//开启应用
#Sharin::start();

// 检测是否是新安装
if (SR_DEBUG_MODE_ON) {
    $install_dir = __DIR__ . '/Public/install/';
    if (is_dir($install_dir) && !file_exists($install_dir . 'install.lock')) {
        // 组装安装url
        $url = $_SERVER['HTTP_HOST'] . trim($_SERVER['SCRIPT_NAME'], 'index.php') . 'Public/install/index.php';
        // 使用http://域名方式访问；避免./Public/install 路径方式的兼容性和其他出错问题
        header("Location:http://$url");
        die;
    }
}

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG', SR_DEBUG_MODE_ON);

// 定义应用目录
define('APP_PATH', SR_PATH_APP);

// 定义缓存目录
define('RUNTIME_PATH', SR_PATH_RUNTIME);

// 定义模板主题
define('DEFAULT_THEME', 'default');

// 定义模板文件默认目录
define('TMPL_PATH', SR_PATH_APP.'Template/' . DEFAULT_THEME . '/');

// 引入ThinkPHP入口文件
require SR_PATH_VENDOR.'/ThinkPHP/ThinkPHP.php';
