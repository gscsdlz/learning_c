<?php
function auto_loader($className) {
    if (file_exists(APPPATH. '/Model/'.$className.".php"))
        require APPPATH. '/Model/'.$className.".php";
    else if (file_exists(APPPATH. '/Include/'.$className.".php"))
        require APPPATH. '/Include/'.$className.".php";
}

function session_check() {

}