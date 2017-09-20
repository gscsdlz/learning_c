<?php

/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/9/11
 * Time: 17:25
 */
class ExamModel extends DB
{
    public function __construct()
    {
        parent::__construct();
    }

    public function init($user_id, $ids, $chapterID)
    {
        if(count($ids) == 0)
            return false;
        $log_id = parent::query("INSERT INTO pro_log (stu_id, stime, etime, sec_cha_id) VALUES (?, ?, 0, ?)",
            [$user_id, time(), $chapterID]);
        foreach ($ids as $pro_id) {
            parent::query("INSERT INTO pro_result (log_id, pro_id, option_id) VALUES (?,?,?)",
                [$log_id, $pro_id, 0]);
        }
        return $log_id;
    }

    public function get_problem($log_id)
    {
        return parent::query_fetch_all("SELECT pro_id FROM pro_result WHERE log_id = ?",
            [$log_id]);
    }

    public function save($log_id, $res) {

        foreach($res as  $k => $v)
            parent::query("UPDATE pro_result SET option_id = ? WHERE pro_id = ? AND log_id = ?",
                [$v, $k, $log_id]);
        parent::query("UPDATE pro_log SET etime = ? WHERE log_id = ?",
            [time(), $log_id]);
    }

    public function get_result($log_id) {
        $res1 = parent::query_fetch_all("SELECT pro_result.pro_id, log_id, pro_result.option_id, pro_info, problem.option_id FROM `pro_result` LEFT JOIN problem USING (pro_id) WHERE log_id = ?",
            [$log_id]);
        foreach($res1 as $row) {
            $res2[] = parent::query_fetch_all("SELECT * FROM pro_option WHERE pro_id = ?", [$row[0]]);
        }
        $res3 = parent::query_one("SELECT stime, etime,chapter_name FROM pro_log LEFT JOIN chapter USING (sec_cha_id) WHERE log_id = ?",
            [$log_id]);
        return [$res1, $res2, $res3];
    }

    public function get_all_exam($id) {
        return parent::query_fetch_all("SELECT log_id, stime, etime, chapter_name, COUNT(pro_id) FROM pro_log INNER JOIN chapter USING(sec_cha_id) LEFT JOIN pro_result USING(log_id) WHERE stu_id = ? GROUP BY (log_id)",
            [$id]);
    }

    public function count_collect($id) {
        return parent::query_one("SELECT COUNT(*) FROM stu_collected WHERE stu_id = ?",
            [$id]);
    }

    public function get_collect($uid) {
        $pros = parent::query_fetch_all("SELECT pro_id FROM stu_collected WHERE stu_id = ?",
            [$uid]);
        foreach ($pros as &$row) {
            $res = parent::query_fetch_all("SELECT pro_info, option_id FROM problem WHERE pro_id = ?",
                [$row[0]]);
            $row[] = $res[0];
            $res = parent::query_fetch_all("SELECT * FROM pro_option WHERE pro_id = ?",
                [$row[0]]);
            $row[] = $res;
        }
        unset($row);
        return $pros;
    }

    public function count_pro($uid) {
        $num1 = parent::query_one("SELECT COUNT(pro_id) FROM pro_result LEFT JOIN pro_log USING (log_id) WHERE stu_id = ?",
            [$uid]);
        $num2 = parent::query_one("SELECT COUNT(*) FROM pro_result LEFT JOIN pro_log USING (log_id) LEFT JOIN problem USING (pro_id) WHERE stu_id = ? AND problem.option_id = pro_result.option_id ",
            [$uid]);
        return [$num2[0], $num1[0]];
    }

    public function get_info($uid) {
        $res1 = parent::query_fetch_all("SELECT stu_id, pro_log.sec_cha_id, chapter_name, COUNT(DISTINCT log_id),COUNT(pro_id) FROM stu_user LEFT JOIN pro_log USING (stu_id) LEFT JOIN pro_result USING(log_id) LEFT JOIN chapter USING (sec_cha_id) WHERE stu_user.stu_id = ? GROUP BY pro_log.sec_cha_id ",
            [$uid]);
        foreach ($res1 as &$row) {
            $res = parent::query_one("SELECT COUNT(*) FROM pro_result LEFT JOIN problem USING(pro_id) LEFT JOIN pro_log USING(log_id) WHERE pro_result.option_id = problem.option_id AND stu_id = ? AND pro_log.sec_cha_id = ?",
                [$row[0], $row[1]]);
            $row[] = $res[0];
        }
        unset($row);
        return $res1;
    }
}