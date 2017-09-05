<?php
date_default_timezone_set('PRC');
define('APPPATH', dirname(__FILE__));

require APPPATH . '/Include/function.php';
require APPPATH . '/Config/config.php';
require APPPATH . '/Include/DB.class.php';
require APPPATH . '/ext/smarty/Smarty.class.php';
require APPPATH . '/Include/router.class.php';
require APPPATH . '/Include/WORK.class.php';

spl_autoload_register('auto_loader');

$route = new router();
$controllerClass = $route->control;
$action = $route->action;
$control = WORK::create($controllerClass);
$control->$action();
