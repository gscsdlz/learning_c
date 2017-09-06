<?php
class WORK {
	static function create($className) {
		if ($className == 'UserController')
			return new UserController ();
		else if ($className == 'ChapterController') {
		    return new ChapterController();
        } else {
			require_once 'indexControl.php';
			return new indexControl ();
		}
	}
}
