<?php
/**
 * 安装向导
 */
header('Content-type:text/html;charset=utf-8');

define('SR_INSTALL_PATH', __DIR__ . '/');
// 检测是否安装过
if (file_exists(SR_INSTALL_PATH . 'install.lock')) {
    die('你已经安装过该系统，重新安装需要先删除./Public/install/install.lock 文件');
}

if (!isset($_GET['c'])) {
    $_GET['c'] = 'agreement';
}
switch ($_GET['c']) {
    // 同意协议页面
    case 'agreement':
        require SR_INSTALL_PATH . '/agreement.php';
        break;
    // 检测环境页面
    case 'test':
        require SR_INSTALL_PATH . '/test.php';
        break;
    // 创建数据库页面
    case 'create':
        require SR_INSTALL_PATH . '/create.php';
        break;
    case 'success':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = $_POST;
            // 连接数据库
            $link = @new mysqli("{$data['DB_HOST']}:{$data['DB_PORT']}", $data['DB_USER'], $data['DB_PWD']);
            // 获取错误信息
            $error = $link->connect_error;
            if (!is_null($error)) {
                // 转义防止和alert中的引号冲突
                $error = addslashes($error);
                die("<script>alert('数据库链接失败:$error');history.go(-1)</script>");
            }
            // 设置字符集
            $link->query("SET NAMES 'utf8'");
            $link->server_info > 5.0 or die("<script>alert('请将您的mysql升级到5.0以上');history.go(-1)</script>");
            // 创建数据库并选中
            if (!$link->select_db($data['DB_NAME'])) {
                $create_sql = 'CREATE DATABASE IF NOT EXISTS ' . $data['DB_NAME'] . ' DEFAULT CHARACTER SET utf8;';
                $link->query($create_sql) or die('创建数据库失败');
                $link->select_db($data['DB_NAME']);
            }
            // 导入sql数据并创建表
            $bjyblog_str = file_get_contents('./bjyblog.sql');
            $sql_array = preg_split("/;[\r\n]+/", str_replace('bjy_', $data['DB_PREFIX'], $bjyblog_str));
            foreach ($sql_array as $k => $v) {
                if (!empty($v)) {
                    $link->query($v);
                }
            }
            $link->close();
            $db_str = <<<php
<?php
return array(

//*************************************数据库设置*************************************
    'DB_TYPE'               =>  'mysqli',                 // 数据库类型
    'DB_HOST'               =>  '{$data['DB_HOST']}',     // 服务器地址
    'DB_NAME'               =>  '{$data['DB_NAME']}',     // 数据库名
    'DB_USER'               =>  '{$data['DB_USER']}',     // 用户名
    'DB_PWD'                =>  '{$data['DB_PWD']}',      // 密码
    'DB_PORT'               =>  '{$data['DB_PORT']}',     // 端口
    'DB_PREFIX'             =>  '{$data['DB_PREFIX']}',   // 数据库表前缀
);
php;
            // 创建数据库链接配置文件
            $dbfile = __DIR__ . '/../../../Application/Common/Conf/db.php';
            file_put_contents($dbfile, $db_str);
            @touch(SR_INSTALL_PATH . 'install.lock');
            require SR_INSTALL_PATH . 'success.php';
        }
        break;

}

