<?php

/**
 * Class ChapterController
 * 完全管理员控制
 */
class ChapterController extends Smarty
{

    private $chapterModel = null;

    public function __construct()
    {
        parent::__construct();
        if( !isset($_SESSION['privilege']) || $_SESSION['privilege'] == 0) {
            parent::display('privilege_error.html');
            die;
        }
        $this->chapterModel = new ChapterModel();
    }

    /**
     * 展示现有章节 支持分页
     */
    public function show()
    {
        $page = get("id");
        if ($page <= 0)
            $page = 1;
        $res = $this->chapterModel->list_all($page);
        parent::assign('page', $page);
        parent::assign('navbar', 'Chapter');
        parent::assign('lists', $res);
        parent::display('chapter_manager.html');
    }

    /**
     * 删除现有章节
     */
    public function del()
    {
        $section_id = post("section_id");
        $res = $this->chapterModel->remove($section_id);
        if ($res != 0) {
            echo json_encode([
                'status' => true
            ]);
        } else {
            echo json_encode([
                'status' => false
            ]);
        }
    }

    /**
     * 修改现有章节
     */
    public function modify()
    {
        $section_id = post('section_id');
        $title = post('title');
        $row = $this->chapterModel->update($section_id, $title);
        if ($row > 0) {
            echo json_encode([
                'status' => true
            ]);
        } else {
            echo json_encode([
                'status' => false
            ]);
        }
    }

    /**
     * 新增章节
     */
    public function add()
    {
        $name = post('name');
        if(!is_null($name) && strlen($name) > 0) {
            $row = $this->chapterModel->add($name);
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
}