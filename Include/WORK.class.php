<?php
class WORK {
	static function create($className) {
		if ($className == 'UserController')
			return new UserController ();
		else if ($className == 'ChapterController') {
		    return new ChapterController();
        } else if ($className == 'ProblemController') {
            return new ProblemController();
        } else if ($className == 'ExamController') {
            return new ExamController();
        } else if ($className == 'ResourceController') {
            return new ResourceController();
        }  else {
			return new IndexController();
		}
	}
}
