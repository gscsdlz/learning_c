<?php

class ProblemModel extends DB
{
    public function __construct()
    {
        parent::__construct();
    }//创建数据库连接

    public function insert_problem($pro, $options, $answer){
        $pro_id = parent::query("INSERT INTO problem (pro_info, tea_id, privilege, section_id) VALUES (?,?,?,?,?)",
            [$pro, 1, 1, 0]);//插入题目
        $ansArr = array();//创建数组
        foreach ($options as $option) {
            $ansArr[] = parent::query("INSERT INTO pro_option (option_info, pro_id) VALUES (?, ?)",
                [$option, $pro_id]);//插入选项
        }
        foreach($answer as $ans) {
            $aff_row = parent::query("INSERT INTO  pro_answer (pro_id, option_id) VALUES (?, ?) ",
                [$pro_id,$ansArr[$ans]]);//插入答案
        }
        return $aff_row;//返回答案：1，2，3，4
    }
}