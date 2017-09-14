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


    /**
     * 控制用户注销
     */
    public function logout() {
        if (isset ( $_SESSION ['username'] )) {
            $_SESSION = array ();
            session_destroy ();
            setcookie ( 'PHPSESSID', '', time () - 3600, '/', '', 0, 0 );
        }
        echo json_encode ( array (
            'status' => true
        ) );
        header('Location:/learning_c');
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

    public function show_stu() {
        parent::display('stu_user.html');
    }
}