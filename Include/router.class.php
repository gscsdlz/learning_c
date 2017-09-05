<?php
class router {
	public $control;
	public $action;
	public function __construct() {
		$this->control = 'indexControl';
		$this->action = 'index';
		if (isset ( $_SERVER ['REQUEST_URI'] ) && $_SERVER ['REQUEST_URI'] != '/') {

			$path = $_SERVER ['REQUEST_URI'];
			$pathstmp = explode ( '?', trim ( $path, '/' ) );
			$paths = explode ( '/', $pathstmp [0] );
			array_shift($paths);
			if (isset ( $paths [0] )) {
				$this->control = ucfirst($paths[0]) . "Controller";
			}
			if (isset ( $paths [1] )) {
				$this->action = $paths [1];
			}
			if (isset ( $pathstmp [1] )) {
				$paths = explode ( '&', $pathstmp [1] );
				$i = 0;
				$count = count ( $paths );
				while ( $i < $count ) {
					if (isset ( $paths [$i + 1] )) {
						$_GET [$path [$i]] = $paths [$i + 1];
					}
					$i += 2;
				}
			} else {
				//URL => /control/method  一般用于不需要参数的方法或者参数通过post方式传输
				//URL => /control/method/arg 
				//URL => /control/method/arg1/arg2 一般仅用于比赛模式中，使用args1标记比赛ID，args2实现具体参数
				
				$count = count ( $paths );
				if($count >= 3)
					$_GET['id'] = $paths[2];
				if($count >= 4)
					$_GET['pid'] = $paths[3];
                if($count >= 5)
                    $_GET['string'] = $paths[4];
			}
		}
	}
}
?>