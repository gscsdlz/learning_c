<?php

class UserController extends Smarty
{
    private $userModel = null;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
    }

    public function login() {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $res = $this->userModel->check_user($username, $password);
        if ($res == true) {
            echo json_encode([
               'status' => true
            ]);
        } else {
            echo json_encode([
                'status' => false
            ]);
        }
    }

    public function test() {
        parent::display("login.html");
    }

    /**
     * 控制用户注销
     */
    public function logout() {

    }

    /**
     * 用户注册
     */
    public function register() {
        $username = post("username");
        $realname = post("realname");
        $password = post("password");
        $act = post("act");
        if(AllowRegister == false) {
            echo json_encode([
               'status' => false,
                'info' => '本系统已经关闭注册功能'
            ]);
            return;
        }

        if ($act == 0) { //代表学生
            $res = $this->userModel->create_stu($username, $realname, $password);
        } else if($act == 1) {  //代表教师
            $res = $this->userModel->create_tea($username, $realname, $password);
        }

        if($res == -1) {
            echo json_encode([
                'status' => false,
                'info' => '用户名重复'
            ]);
        } else {
            echo json_encode([
               'status' => true
            ]);
        }

    }

    /**
     *
     */
    public function update_info() {

    }
}