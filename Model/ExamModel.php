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
}