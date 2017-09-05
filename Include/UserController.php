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
}