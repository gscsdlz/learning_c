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

    public function get_chapter($sectionID = null)
    {
        return parent::query_fetch_all("SELECT chapter.sec_cha_id, chapter.chapter_id, chapter.chapter_name, COUNT(resource_id), COUNT(pro_id)
          FROM chapter LEFT JOIN learning_resource ON chapter.sec_cha_id = learning_resource.sec_cha_id 
          LEFT JOIN problem ON problem.sec_cha_id = chapter.sec_cha_id WHERE chapter.section_id = ? OR ? is null 
          GROUP BY chapter.sec_cha_id", [$sectionID, $sectionID]);
    }

    public function remove($section_id)
    {
        /*$res = parent::query_one("SELECT COUTN(*) FROM learning_resource WHERE section_id = ?",
            [$section_id]);
        if ($res[0] != 0)
            return 0;
        else {*/
            return parent::query("DELETE FROM section WHERE section_id = ?",
                [$section_id]);

    }

    public function remove_chapter($id) {
        return parent::query("DELETE FROM chapter WHERE sec_cha_id = ? LIMIT 1",
            [$id]);
    }

    public function update_chapter($id, $name)
    {
        return parent::query("UPDATE chapter SET chapter_name = ? WHERE sec_cha_id = ? LIMIT 1",
            [$name, $id]);
    }

    public function update($seciton_id, $name)
    {
        return parent::query("UPDATE section SET section_name = ? WHERE section_id = ? LIMIT 1",
            [$name, $seciton_id]);
    }

    public function add_chapter($name, $sectionID) {
        $res = parent::query_one("SELECT MAX(chapter_id) FROM chapter WHERE section_id = ?",
            [$sectionID]);
        $chapterID = (int)$res[0] + 1;

        return parent::query("INSERT INTO chapter (chapter_name, chapter_id, section_id) VALUES (?, ?,?)",
            [$name, $chapterID, $sectionID]);
    }

    public function add_section($name) {
        return parent::query("INSERT INTO section (section_name) VALUES (?)",
            [$name]);
    }

    public function get_all_section($sectionID = null) {
        return parent::query_fetch_all("SELECT * FROM section WHERE section_id = ? OR ? is null",
            [$sectionID, $sectionID]);
    }

    public function get_all_chapter()
    {
        $res =  parent::query("SELECT chapter.chapter_name, section.section_name, chapter.sec_cha_id FROM section LEFT JOIN chapter ON section.section_id = chapter.section_id");
        $arg = array();
        while($row = $res->fetch(PDO::FETCH_NUM)) {
            $arg[$row[1]][] = [$row[2], $row[0]];
        }
        return $arg;
    }

    public function exchange_chapter_id() {

    }

    public function get_chapter_info($chapter) {
        return parent::query_fetch_all("SELECT * FROM section LEFT JOIN chapter USING (section_id) WHERE sec_cha_id = ? ",
            [$chapter]);
    }
}