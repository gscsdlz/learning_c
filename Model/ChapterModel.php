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

        return parent::query_fetch_all("SELECT section.*, COUNT(resource_id), COUNT(pro_id) 
          FROM `section` LEFT JOIN learning_resource ON section.section_id = learning_resource.section_id 
          LEFT JOIN problem ON problem.section_id = section.section_id 
          GROUP BY section.section_id  LIMIT $l, $r");
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

    public function update($seciton_id, $name)
    {
        return parent::query("UPDATE section SET section_name = ? WHERE section_id = ? LIMIT 1",
            [$name, $seciton_id]);
    }

    public function add($name) {
        return parent::query("INSERT INTO section (section_name) VALUES (?)",
            [$name]);
    }
}