<?php
class WORK {
	static function create($className) {
		if ($className == 'UserController')
			return new UserController ();
		else if ($className == 'ChapterController') {
		    return new ChapterController();
        } else if ($className == 'ProblemController') {
            return new ProblemController();
        } else {
			return new IndexController();
		}
	}
}
