<?php

/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/9/11
 * Time: 14:41
 */
class ResourceController extends Smarty
{
    private $resourceModel = null;
    private $chapterModel = null;
    public function __construct()
    {
        parent::__construct();
        $this->resourceModel = new ResourceModel();
        $this->chapterModel = new ChapterModel();
    }

    public function show()
    {
        $page = (int)get('id');
        $sectionID = (int)get('pid');
        if($page < 1)
            $page = 1;
        $res = $this->resourceModel->get_resource_lists($sectionID, $page);
        parent::assign('page', $page);
        parent::assign('sectionID', $sectionID);
        parent::assign('sectionLists', $this->chapterModel->get_all_section());
        parent::assign('res_lists', $res);
        parent::display('resource_list.html');

    }

    public function add_res()
    {
        parent::assign('sectionLists', $this->chapterModel->get_all_section());
        parent::display('resource.html');
    }

    public function do_add()
    {
        $title = post('title');
        $sec_cha_id = post('sec_cha_id');
        $body = post('body');
        $tea_id = $_SESSION['tea_id'];
        $row = $this->resourceModel->insert($title, $sec_cha_id, $body, $tea_id);
        if($row > 0)
            echo json_encode([
                'status' => true
            ]);
        else
            echo json_encode([
                'status' => false
            ]);
    }
    public function do_del()
    {
        $res_id = post('res_id');
        $aff = $this->resourceModel->del($res_id);
        if ($aff > 0)
            echo json_encode([
                'status' => true
            ]);
        else
            echo json_encode([
                'status' => false
            ]);
    }

    public function edit() {
        $resid = get('id');
        $res = $this->resourceModel->get_resource($resid);

        parent::assign('sectionID', $res[2]);
        parent::assign('title', $res[1]);
        parent::assign('resID', $res[0]);
        parent::assign('sec_cha_id', $res[3]);
        parent::assign('sectionLists', $this->chapterModel->get_all_section());

        parent::display('resource_edit.html');
    }

    public function get_body()
    {
        $resid = post('resid');
        $body = $this->resourceModel->get_body($resid);
        echo json_encode([
            'body' => $body[0]
        ]);
    }

    public function do_edit()
    {
        $title = post('title');
        $sec_cha_id = post('sec_cha_id');
        $body = post('body');
        $res_id = post('resid');
        $tea_id = $_SESSION['tea_id'];
        $row = $this->resourceModel->update($res_id, $title, $sec_cha_id, $body, $tea_id);
        if($row > 0)
            echo json_encode([
                'status' => true
            ]);
        else
            echo json_encode([
                'status' => false
            ]);
    }


    public function learning()
    {
        $resid = get('id');

        parent::assign('body', $this->resourceModel->get_body($resid));
        parent::assign('lists', $this->resourceModel->get_all_resource());
        parent::display('learning.html');
    }

}