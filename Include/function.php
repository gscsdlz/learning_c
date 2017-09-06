<?php
function auto_loader($className) {
    if (file_exists(APPPATH. '/Model/'.$className.".php"))
        require APPPATH. '/Model/'.$className.".php";
    else if (file_exists(APPPATH. '/Include/'.$className.".php"))
        require APPPATH. '/Include/'.$className.".php";
}

function session_check() {

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



