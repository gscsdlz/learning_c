<?php
function auto_loader($className) {
    if (file_exists(APPPATH. '/Model/'.$className.".php"))
        require APPPATH. '/Model/'.$className.".php";
    else if (file_exists(APPPATH. '/Include/'.$className.".php"))
        require APPPATH. '/Include/'.$className.".php";
}

function session_check() {
    session_start();
    if (isset ( $_SESSION ['timeout'] )) { // 检查登录情况
        if ($_SESSION ['timeout'] < time ()) { // 登录超时
            $_SESSION = array ();
            session_destroy ();
            setcookie ( 'PHPSESSID', '', time () - 3600, '/', '', 0, 0 );
            return false;
        }
        $_SESSION ['timeout'] = time () + TimeOut; // 刷新时间戳 @config.php
        return true;
    }
    return false;
}

function post($key, $default = null) {
    if (isset($_POST[$key]))
        return $_POST[$key];
    else
        return $default;
}

function get($key, $default = null) {
    if (isset($_GET[$key]))
        return $_GET[$key];
    else
        return $default;
}



