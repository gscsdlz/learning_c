<?php

/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/9/6
 * Time: 15:46
 */
class ChapterModel extends DB
{
    public function __construct()
    {
        parent::__construct();
    }

    public function list_all($page = 1)
    {
        $l = ($page - 1) * SectionPageMax;
        $r = $l + SectionPageMax;

        return parent::query_fetch_all("SELECT section_id, chapter_name,section_name , title_name FROM section LIMIT $l, $r");
    }

    public function remove($section_id)
    {
        $res = parent::query_one("SELECT COUTN(*) FROM learning_resource WHERE section_id = ?",
            [$section_id]);
        if ($res[0] != 0)
            return 0;
        else {
            return parent::query("DELETE FROM section WHERE section_id = ?",
                [$section_id]);
        }

    }
}