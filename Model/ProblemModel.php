<?php

class ProblemModel extends DB
{
    public function __construct()
    {
        parent::__construct();
    }//创建数据库连接

    public function insert_problem($pro, $options, $answer, $chapterID, $tea_id)
    {
        $pro_id = parent::query("INSERT INTO problem (pro_info, tea_id, sec_cha_id, option_id) VALUES (?,?,?, 0)",
            [$pro, $tea_id, $chapterID]);//插入题目
        if ($pro_id > 0) {
            for ($i = 0; $i < count($options); $i++)
                $res[] = parent::query("INSERT INTO pro_option (option_info, pro_id) VALUES (?, ?)",
                    [$options[$i], $pro_id]);//插入选项

            parent::query("UPDATE problem SET option_id = ? WHERE pro_id = ?",
                [$res[$answer[0]], $pro_id]);
        }
        return 1;//返回答案：1，2，3
    }

    public function update_problem($pro_id, $pro, $options, $answer, $chapterID, $tea_id)
    {
        $row = parent::query("UPDATE problem SET pro_info = ?, tea_id = ?, sec_cha_id = ? WHERE pro_id = ?",
            [$pro,$tea_id, $chapterID, $pro_id]);
        parent::query("DELETE FROM pro_option WHERE pro_id = ?",
            [$pro_id]);
        for ($i = 0; $i < count($options); $i++)
            $res[] = parent::query("INSERT INTO pro_option (option_info, pro_id) VALUES (?, ?)",
                [$options[$i], $pro_id]);//插入选项

        parent::query("UPDATE problem SET option_id = ? WHERE pro_id = ?",
            [$res[$answer[0]], $pro_id]);
        return 1;

    }

    public function get_problem($page, $section = null)
    {
        $pms = ProblemPageMax;
        $l = ($page - 1) * $pms;
        $r = $l + $pms;
        return parent::query_fetch_all("SELECT problem.pro_id, pro_info, create_time, tea_user.tea_name, chapter_name FROM problem INNER JOIN chapter ON chapter.sec_cha_id = problem.sec_cha_id INNER JOIN tea_user ON tea_user.tea_id = problem.tea_id WHERE chapter.section_id =  ? OR ? is NULL LIMIT $l, $r",
            [$section, $section]);
    }

    public function remove($pro_id)
    {
        return parent::query("DELETE FROM problem WHERE pro_id = ?",
            [$pro_id]);
    }

    public function get_info($pro_id)
    {
        $res[] = parent::query_one("SELECT pro_id, pro_info, section_id, chapter.sec_cha_id, option_id FROM `problem` INNER JOIN chapter ON chapter.sec_cha_id = problem.sec_cha_id  WHERE pro_id = ?",[
            $pro_id
        ]);
        $res[] = parent::query_fetch_all("SELECT * FROM pro_option WHERE pro_id = ?",
            [$pro_id]);
        return $res;
    }

    public function get_pro_id($sectionID, $type = 0) {
        if($type == 0)
            return parent::query_fetch_all("SELECT pro_id FROM problem WHERE sec_cha_id = ?",
                [$sectionID]);
        else
            return parent::query_fetch_all("SELECT pro_id FROM problem INNER JOIN chapter ON chapter.sec_cha_id = problem.sec_cha_id WHERE section_id = ?",
                [$sectionID]);
    }

    /////////////////////////
    ///
    public function collect($pro_id, $user_id) {

        return parent::query("INSERT INTO stu_collected (stu_id, pro_id) VALUES (?, ?)",
            [$user_id, $pro_id]);
    }

    public function del_collect($pro_id, $user_id) {
        return parent::query("DELETE FROM stu_collected WHERE stu_id = ? AND pro_id = ? LIMIT 1",
            [$user_id, $pro_id]);
    }

    public function count_info() {
        $res = parent::query_fetch_all("SELECT pro_id, pro_info, option_id FROM problem");

        foreach ($res as &$r) {
            $res1 = parent::query_fetch_all("SELECT pro_option.option_id, option_info, COUNT(pro_result.option_id) FROM pro_option LEFT JOIN pro_result USING (option_id) WHERE pro_option.pro_id = ? GROUP BY pro_option.option_id",
                [$r[0]]);
            $r[] = $res1;
        }
        unset($r);
        foreach ($res as &$r) {
            $k = 0;
            $arr = [];
            foreach ($r[3] as $q) {
                $k += $q[2];
                $arr[] = $q;
            }
            if($k == 0)
                $k = 1;

            foreach ($arr as &$q) {
                $q[2] = (int)($q[2] / $k * 100);
            }
            unset($q);
            $r[3] = $arr;
        }

        unset($r);
        return $res;
    }
}