<?php

class ProblemController extends Smarty
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 新增一个题目
     */
    public function add_problem() {
        $pro=post("pro");
        $answer=post("answer");
        $options=post("options");
        if (is_null($pro) || is_null($answer) || is_null($options))
            echo json_encode([
                'status' => false
            ]);
        else {

        }
    }

    /**
     * 删除指定题目
     */
    public function del_problem() {

    }

    /**
     * 修改指定题目
     */
    public function modify_problem() {

    }
}