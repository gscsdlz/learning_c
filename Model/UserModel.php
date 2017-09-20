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

    public function create_tea($username, $realname, $password)
    {
        $res = parent::query_one("SELECT COUNT(*) FROM tea_user WHERE tea_number = ?",
            [$username]);
        if($res[0] == 0) {
            $id = parent::query("INSERT INTO tea_user (tea_number, tea_name, password) VALUES (?,?,?)",
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
    
    public function get_all_user_by_class($classname) {
        return parent::query_fetch_all("SELECT stu_id, stu_name, stu_number, grade, class FROM stu_user WHERE class = ? ORDER BY stu_number ASC",
            [$classname]);
    }

    public function bind_class($classname, $tea_id) {
        return parent::query("INSERT INTO tea_stu_bind (tea_id, class) VALUES (?, ?)",
            [$tea_id, $classname]);
    }

    public function unbind_class($classname) {
        return parent::query("DELETE FROM tea_stu_bind WHERE `class` like ?",
            [$classname]);
    }

    public function get_teacher(){
        return parent::query_fetch_all("SELECT tea_id, tea_name FROM tea_user WHERE 1");
    }

    public function get_class($tea_id) {
        return parent::query_fetch_all("SELECT class FROM tea_stu_bind WHERE tea_id = ?",
            [$tea_id]);
    }

    ////////////////
///
    public function get_tea() {
        $res = parent::query_fetch_all("SELECT tea_id, tea_number, tea_name, privilege, reg_time FROM tea_user");
        foreach ($res as &$row) {
            $res1 = parent::query_fetch_all("SELECT class FROM tea_stu_bind WHERE tea_id = ?",
                [$row[0]]);
            $row[] = $res1;
        }
        unset($row);

        return $res;
    }

    public function update_tea($res)
    {
        return parent::query("UPDATE tea_user SET tea_name = ?, tea_number = ?, privilege = ? WHERE tea_id = ?",
            array_values($res));
    }

    public function del_tea($res)
    {
        return parent::query("DELETE FROM tea_user WHERE tea_id = ?",
            array_values($res));
    }

    public function get_stu($page = 1) {
        $l = ($page - 1) * 10;
        $r = 10;

        return parent::query_fetch_all("SELECT stu_id, stu_number, stu_name, class, tea_name, grade FROM stu_user LEFT JOIN tea_stu_bind USING (class) LEFT JOIN tea_user USING(tea_id) ORDER BY stu_id LIMIT $l, $r ");
    }

    public function update_stu($res)
    {
        return parent::query("UPDATE stu_user SET stu_name = ?, stu_number = ?, class = ?, grade = ? WHERE stu_id = ?",
            array_values($res));
    }

    public function del_stu($res)
    {
        return parent::query("DELETE FROM stu_user WHERE stu_id = ?",
            array_values($res));
    }

}