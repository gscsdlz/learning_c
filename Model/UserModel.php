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
    
    public function get_all_user_by_class($classname, $page) {
        if(is_null($page))
            return parent::query_fetch_all("SELECT stu_id, stu_name, stu_number, grade, class FROM stu_user WHERE class = ? ORDER BY stu_number ASC",
                [$classname]);
        else {
            $l = ($page - 1) * 10;
            $r = 10;
            $res =  parent::query_fetch_all("SELECT stu_id, stu_name, stu_number, COUNT(DISTINCT log_id),COUNT(pro_id),COUNT(DISTINCT pro_id) FROM stu_user LEFT JOIN pro_log USING (stu_id) LEFT JOIN pro_result USING(log_id) WHERE class like ? GROUP BY stu_id LIMIT $l, $r",
                [$classname]);
            foreach ($res as &$row) {
                $r = parent::query_one("SELECT COUNT(*) FROM pro_result LEFT JOIN problem USING(pro_id) LEFT JOIN pro_log USING(log_id) WHERE pro_result.option_id = problem.option_id AND stu_id = ?",
                    [$row[0]]);
                $row[] = $r[0];
            }
            unset($row);
            return $res;
        }
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

    ///////////////////////
    public function get_tea_info($tid) {
        $res1 = parent::query_one("SELECT tea_id, tea_name, tea_number, privilege, reg_time, last_time FROM tea_user WHERE tea_id = ?",
            [$tid], PDO::FETCH_NAMED);
        $res2 = parent::query_fetch_all("SELECT * FROM tea_stu_bind  WHERE tea_id = ?",
            [$tid]);
        return [$res1, $res2];
    }

    public function update_tea_pass($pass, $tid) {
        return parent::query("UPDATE tea_user SET password = ? WHERE tea_id = ?",[
            sha1($pass), $tid
        ]);
    }

    public function update_tea_info($tid, $name, $number ){
        return parent::query("UPDATE tea_user SET tea_name = ?, tea_number = ? WHERE tea_id = ? ",
            [$name, $number, $tid]);
    }
}