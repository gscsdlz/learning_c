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

        $lists = $this->chapterModel->get_all_section();
        parent::assign('navbar', 'problemAdd');
        parent::assign('sectionLists', $lists);
        parent::display('add_item.html');
    }

    public function get_chapter() {
        $id = post('sectionID');
        $res = $this->chapterModel->get_chapter($id);
        echo json_encode([
            'status' => true,
            'res' => $res
        ]);
    }

    /**
     * 新增一个题目
     */
    public function add_problem() {
        $pro=post("pro");
        $answer=post("answer");
        $options=post("options");
        $chapterID = post('chapterID');

        if (is_null($pro) || is_null($answer) || is_null($options))
            echo json_encode([
                'status' => false
            ]);
        else {
            $row = $this->problemModel->insert_problem($pro, $options, $answer, $chapterID, $_SESSION['tea_id']);
            if($row > 0) {
                echo json_encode([
                    'status' => true
                ]);
            } else {
                echo json_encode([
                    'status' => false
                ]);
            }
        }
    }

    /**
     * 删除指定题目
     */
    public function del_problem() {
        $pro_id = post('pro_id');
        $row = $this->problemModel->remove($pro_id);
        if($row > 0)
            echo json_encode([
                'status' => true
            ]);
        else
            echo json_encode([
                'status' => false
            ]);
    }

    /**
     * 修改指定题目
     */
    public function modify_problem() {
        $pro_id = (int)get('id');
        $res = $this->problemModel->get_info($pro_id);

        $lists = $this->chapterModel->get_all_section();

        parent::assign('sectionLists', $lists);
        parent::assign('option_lists', $res[1]);
        parent::assign('pro_info', $res[0][1]);
        parent::assign('pro_id', $pro_id);
        parent::assign('section_id', $res[0][2]);
        parent::assign('sec_cha_id', $res[0][3]);
        parent::assign('option', $res[0][4]);
        parent::display('problem_edit.html');
    }

    public function show() {
        $section = (int)get('pid', null);
        $page = (int)get('id', 1);
        if($page < 1)
            $page = 1;
        $res = $this->problemModel->get_problem($page, $section);
        $lists = $this->chapterModel->get_all_section();

        parent::assign('page', $page);
        parent::assign('sectionID', $section);
        parent::assign('sectionLists', $lists);
        parent::assign('lists', $res);
        parent::display('problem_list.html');
    }

    public function save(){
        $pro_id = post('pro_id');
        $pro=post("pro");
        $answer=post("answer");
        $options=post("options");
        $chapterID = post('chapterID');
        $row = $this->problemModel->update_problem($pro_id, $pro, $options, $answer, $chapterID, $_SESSION['tea_id']);
        if($row > 0) {
            echo json_encode([
                'status' => true
            ]);
        } else {
            echo json_encode([
                'status' => false
            ]);
        }
    }

    public function collect(){
        $pro_id = post('pro_id');
        $stu_id = $_SESSION['stu_id'];
        $id = $this->problemModel->collect($pro_id, $stu_id);
        if($id > 0)
            echo json_encode([
                'status' => true
            ]);
        else
            echo json_encode([
                'status' => false
            ]);
    }

    public function del_collect(){
        $pro_id = post('pro_id');

        $id = $this->problemModel->del_collect($pro_id, $_SESSION['stu_id']);
        if($id > 0)
            echo json_encode([
                'status' => true
            ]);
        else
            echo json_encode([
                'status' => false
            ]);
    }
}