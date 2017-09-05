<?php

class UserModel extends DB
{
    public function __construct()
    {
        parent::__construct();
    }

    public function check_user($username, $password) {
        $arg = parent::query_one("SELECT password FROM stu_user WHERE stu_number = ?", [$username]);
        if($arg[0] == sha1($password))
            return true;
        else
            return false;
    }
}