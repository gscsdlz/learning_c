<?php
class WORK {
	static function create($className) {
		if ($className == 'UserController')
			return new UserController ();
		else {
			require_once 'indexControl.php';
			return new indexControl ();
		}
	}
}
?>