<?php

/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/9/6
 * Time: 15:45
 */
class ChapterController extends Smarty
{

    private $chapterModel = null;

    public function __construct()
    {
        parent::__construct();
        $this->chapterModel = new ChapterModel();
    }

    public function show()
    {
        $page = get("p");
        if ($page <= 0)
            $page = 1;
        $res = $this->chapterModel->list_all($page);
        parent::assign('lists', $res);
        parent::display('chapter_manager.html');
    }

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
}