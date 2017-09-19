<?php

class UserModel extends DB
{
    public function __construct()
    {
        parent::__construct();
    }

    public function check_user($username, $password, $act)
    {
        if($act == 0)
            $sql = "SELECT password, stu_id FROM stu_user WHERE stu_number = ?";
        else
            $sql = "SELECT password, tea_id, privilege FROM tea_user WHERE tea_number = ?";

        $arg = parent::query_one($sql, [$username]);

        if($arg[0] == sha1($password)) {
            if($act == 0)
                parent::query("UPDATE stu_user SET last_time = ? WHERE stu_number = ?",
                    [time(), $username]);
            else
                parent::query("UPDATE tea_user SET last_time = ? WHERE tea_number = ?",
                    [time(), $username]);
            return $arg;
        } else
            return false;
    }

    public function create_stu($username, $realname, $password)
    {
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

    public function get_stu_info($stu_name) {
        return parent::query_one("SELECT * FROM stu_user WHERE stu_id = ?",
            [$stu_name], PDO::FETCH_NAMED);
    }

    public function update_pass($pass1, $pass2, $stu_id) {
        return parent::query("UPDATE stu_user SET password = ? WHERE stu_id = ?",
            [sha1($pass1), $stu_id]);
    }

    public function update($user_id, $stu_name, $stu_number, $class, $grade) {
        return parent::query("UPDATE stu_user SET stu_name = ?, stu_number = ?, class=?, grade = ? WHERE stu_id = ?",
            [$stu_name, $stu_number, $class, $grade, $user_id]);
    }
}