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
            $pro_ids = $this->problemModel->get_pro_id($sectionID, 1);
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
            $log_id = $this->examModel->init(1, $ids);
        }

        $res = array();
        $k = 0;
        foreach ($ids as $row) {
            $res[$k] = $this->problemModel->get_info($row);
            shuffle($res[$k][1]);
            $k++;
        }
        if ($sig == true)
            header('Location:/learning_c/exam/show/'.$sectionID.'/'.$log_id);

        parent::assign('lists', $res);
        parent::display('problem.html');
    }

    public function index()
    {
        parent::assign('lists', $this->chapterModel->get_all_chapter());
        parent::display('chapter_choose.html');
    }
}