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

    public function create_stu($username, $realname, $password) {
        $res = parent::query_one("SELECT COUNT(*) FROM stu_user WHERE stu_number = ?",
            [$username]);
        if($res[0] == 0) {
            $id = parent::query("INSERT INTO stu_user (stu_number, stu_name, password) VALUES (?,?,?)",
                [$username, $realname, sha1($password)]);
            return $id;
        } else {
            return -1;
        }
    }
}