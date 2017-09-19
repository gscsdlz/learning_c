<?php

/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/9/11
 * Time: 14:52
 */
class ExamController extends Smarty
{
    private $problemModel = null;
    private $examModel = null;
    private $chapterModel = null;

    public function __construct()
    {
        parent::__construct();
        $this->problemModel = new ProblemModel();
        $this->examModel = new ExamModel();
        $this->chapterModel = new ChapterModel();
    }

    public function show()  {
        if(!isset($_SESSION['username']))
        {
            parent::display('login.html');
            return;
        }
        $sectionID = get('id');
        $log_id = get('pid', null);
        $sig = false;
        if(is_null($log_id)) {
            $pro_ids = $this->problemModel->get_pro_id($sectionID, 0);
            $sig = true;
        } else {
            $pro_ids = $this->examModel->get_problem($log_id);
        }
        $ids = array();
        $k = 1;
        foreach ($pro_ids as $row) {
            $ids[] = (int)$row[0];
            if($k++ == 10)
                break;
        }
        unset($pro_ids);
        if ($sig == true) {
            shuffle($ids);
            $log_id = $this->examModel->init($_SESSION['stu_id'], $ids, $sectionID);
        }
        if($log_id != false) {
            $res = array();
            $k = 0;
            foreach ($ids as $row) {
                $res[$k] = $this->problemModel->get_info($row);
                shuffle($res[$k][1]);
                $k++;
            }
            if ($sig == true)
                header('Location:/learning_c/exam/show/' . $sectionID . '/' . $log_id);

            parent::assign('chapterName', $this->chapterModel->get_chapter_info($sectionID)[0]);
            parent::assign('log_id', $log_id);
            parent::assign('lists', $res);
            parent::display('problem.html');
        } else {
            parent::assign('error','暂无任何题目！');
            parent::display('error.html');
        }
    }

    public function index()
    {
        parent::assign('lists', $this->chapterModel->get_all_chapter());
        parent::display('chapter_choose.html');
    }

    public function answer()
    {
        $options = post('options');
        $pros = post('pros');
        $log_id = post('log_id');
        $res = array_combine($pros, $options);
        $this->examModel->save($log_id, $res);
        echo json_encode([
            'status' => true
        ]);
    }

    public function result() {
        $log_id = get('id');
        $res = $this->examModel->get_result($log_id);
        parent::assign('stime', $res[2][0]);
        parent::assign('etime', $res[2][1]);
        parent::assign('chapterName', $res[2][2]);
        parent::assign('pro_info', $res[0]);
        parent::assign('pro_option', $res[1]);
        parent::display('exam_result.html');
    }

    public function show_collect()
    {
        $uid = get('id');
        $pros = $this->examModel->get_collect($uid);
        parent::assign('pros', $pros);
        parent::display('pro_collect.html');
    }
}