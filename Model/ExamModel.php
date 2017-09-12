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

    public function init($user_id, $ids)
    {
        $log_id = parent::query("INSERT INTO pro_log (stu_id, stime, etime) VALUES (?, ?, 0)",
            [$user_id, time()]);
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
}