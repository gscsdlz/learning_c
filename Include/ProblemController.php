<?php

class ProblemController extends Smarty
{
    private $problemModel = null;
    private $chapterModel = null;
    public function __construct()
    {
        parent::__construct();
        $this->problemModel = new ProblemModel();
        $this->chapterModel = new ChapterModel();
    }

    public function add() {

        if(!isset($_SESSION['privilege']) || $_SESSION['privilege'] == 0) {
            parent::display('login.html');
            return;
        }

        $lists = $this->chapterModel->list_all();
        parent::assign('navbar', 'problemAdd');
        parent::assign('sectionLists', $lists);
        parent::display('add_item.html');
    }

    /**
     * 新增一个题目
     */
    public function add_problem() {
        $pro=post("pro");
        $answer=post("answer");
        $options=post("options");
        $sectionID = post('sectionID');

        if (is_null($pro) || is_null($answer) || is_null($options))
            echo json_encode([
                'status' => false
            ]);
        else {
            $this->problemModel->insert_problem($pro, $options, $answer, $sectionID);
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