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
            parent::assign('error', '权限不足！');
            parent::display('error.html');
            die;
        }
        $this->chapterModel = new ChapterModel();
    }

    /**
     * 展示现有章节
     */
    public function show()
    {
        $sectionID = get("id", null);
        $section = $this->chapterModel->get_all_section($sectionID);

        if (!is_null($section) && count($section) > 0) {
            if(is_null($sectionID))
                $sectionID = $section[0][0];
            $res = $this->chapterModel->get_chapter($sectionID);

            parent::assign('section', $section[0]);
            parent::assign('lists', $res);
        }
        $lists = $this->chapterModel->get_all_section();
        parent::assign('sectionLists', $lists);
        parent::assign('navbar', 'Chapter');
        parent::display('chapter_manager.html');
    }

    /**
     * 删除现有章节
     */
    public function del_chapter()
    {
        $id = post("id");
        $res = $this->chapterModel->remove_chapter($id);
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

    public function del()
    {
        $id = post("id");
        $res = $this->chapterModel->remove($id);
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
    public function modify_chapter()
    {
        $id = post('id');
        $title = post('title');
        if(!is_null($title) && strlen($title) > 0) {
            $row = $this->chapterModel->update_chapter($id, $title);
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
    }

    public function modify()
    {
        $id = post('id');
        $title = post('name');
        if(!is_null($title) && strlen($title) > 0) {
            $row = $this->chapterModel->update($id, $title);
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
    }

    /**
     * 新增章节
     */
    public function add_chapter()
    {
        $name = post('name');
        $sectionID = post("sectionID");

        if(!is_null($name) && strlen($name) > 0) {
            $row = $this->chapterModel->add_chapter($name, $sectionID);
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

    public function add()
    {
        $name = post('name');

        if(!is_null($name) && strlen($name) > 0) {
            $row = $this->chapterModel->add_section($name);
            if($row > 0) {
                echo json_encode([
                    'status' => true,
                    'section_id' => $row
                ]);
            } else {
                echo json_encode([
                    'status' => false
                ]);
            }
        }
    }
}