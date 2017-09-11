<?php

class ProblemModel extends DB
{
    public function __construct()
    {
        parent::__construct();
    }//创建数据库连接

    public function insert_problem($pro, $options, $answer, $chapterID)
    {
        $pro_id = parent::query("INSERT INTO problem (pro_info, tea_id, privilege, sec_cha_id) VALUES (?,?,?,?)",
            [$pro, 1, 1, $chapterID]);//插入题目
        if ($pro_id > 0) {
            for ($i = 0; $i < count($options); $i++)
                parent::query("INSERT INTO pro_option (option_info, pro_id, answer) VALUES (?, ?, ?)",
                    [$options[$i], $pro_id, in_array($i, $answer)]);//插入选项
        }
        return 1;//返回答案：1，2，3，
    }

    public function get_problem($page, $section = null)
    {
        $pms = ProblemPageMax;
        $l = ($page - 1) * $pms;
        $r = $l + $pms;
        return parent::query_fetch_all("SELECT problem.pro_id, pro_info, problem.privilege, create_time, tea_user.tea_name, chapter_name FROM problem INNER JOIN chapter ON chapter.sec_cha_id = problem.sec_cha_id INNER JOIN tea_user ON tea_user.tea_id = problem.tea_id WHERE chapter.section_id =  ? OR ? is NULL LIMIT $l, $r",
            [$section, $section]);
    }

    public function remove($pro_id)
    {
        return parent::query("DELETE FROM problem WHERE pro_id = ?",
            [$pro_id]);
    }

    public function get_info($pro_id)
    {
        $res[] = parent::query_one("SELECT pro_info, section_id, chapter.sec_cha_id FROM `problem` INNER JOIN chapter ON chapter.sec_cha_id = problem.sec_cha_id  WHERE pro_id = ?",[
            $pro_id
        ]);
        $res[] = parent::query_fetch_all("SELECT * FROM pro_option WHERE pro_id = ?",
            [$pro_id]);
        return $res;
    }
}