<?php

class ProblemModel extends DB
{
    public function __construct()
    {
        parent::__construct();
    }//创建数据库连接

    public function insert_problem($pro, $options, $answer, $sectionID)
    {
        $pro_id = parent::query("INSERT INTO problem (pro_info, tea_id, privilege, section_id) VALUES (?,?,?,?)",
            [$pro, 1, 1, $sectionID]);//插入题目
        if ($pro_id > 0) {
            for ($i = 0; $i < count($options); $i++)
                parent::query("INSERT INTO pro_option (option_info, pro_id, answer) VALUES (?, ?, ?)",
                    [$options[$i], $pro_id, in_array($i, $answer)]);//插入选项
        }
        return 1;//返回答案：1，2，3，
    }
}