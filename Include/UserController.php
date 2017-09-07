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
        $username = post('username');
        $password = post('password');
        $act = post('act');

        $res = $this->userModel->check_user($username, $password, $act);

        if ($res != false) {
            $_SESSION['username'] = $username;
            if($act == 0)
                $_SESSION['stu_id'] = $res[1];
            else {
                $_SESSION['tea_id'] = $res[1];
                $_SESSION['privilege'] = $res[2];
            }
            $_SESSION['timeout'] = time() + TimeOut;
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
        parent::display("CaptureManager.html");
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