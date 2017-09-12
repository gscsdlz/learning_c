<?php

/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/9/11
 * Time: 14:43
 */
class ResourceModel extends DB
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_resource_lists($sectionID, $page)
    {
        $pms = ResourcePageMax;
        $l = ($page - 1) * $pms;
        $r = $l + $pms;
        return parent::query_fetch_all("SELECT learning_resource.resource_id, title, learning_resource.time, tea_user.tea_name, chapter.chapter_name FROM learning_resource INNER JOIN chapter ON chapter.sec_cha_id = learning_resource.sec_cha_id INNER JOIN tea_user ON tea_user.tea_id = learning_resource.tea_id WHERE section_id = ? LIMIT $l, $r",
            [$sectionID]);
    }

    public function insert($title, $sec_cha_id, $body, $tea_id)
    {
        return parent::query("INSERT INTO learning_resource (body, sec_cha_id, tea_id, time, title) VALUES (?,?,?,?, ?)",
            [$body, $sec_cha_id, $tea_id, time(), $title,]);
    }

    public function del($res_id)
    {
        return parent::query("DELETE FROM learning_resource WHERE resource_id = ? LIMIT 1",
            [$res_id]);
    }

    public function get_resource($res_id)
    {
        return parent::query_one("SELECT resource_id, title, section_id, learning_resource.sec_cha_id FROM learning_resource INNER JOIN chapter ON chapter.sec_cha_id = learning_resource.sec_cha_id WHERE learning_resource.resource_id = ?",
            [$res_id]);
    }

    public function get_body($res_id)
    {
        return parent::query_one("SELECT body FROM learning_resource WHERE resource_id = ?",
            [$res_id]);
    }
}