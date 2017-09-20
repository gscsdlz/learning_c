<?php

class UserController extends Smarty
{
    private $userModel = null;
    private $examModel = null;
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
        $this->examModel = new ExamModel();
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

            $_SESSION['username'] = $username;
            if($act == 0)
                $_SESSION['stu_id'] = $res;
            else {
                $_SESSION['tea_id'] = $res;
                $_SESSION['privilege'] = 0;
            }
            $_SESSION['timeout'] = time() + TimeOut;

            echo json_encode([
               'status' => true
            ]);
        }

    }

    /**
     *
     */
    public function update() {
        $stu_name = post('stu_name');
        $stu_number = post('stu_number');
        $class = post('class_name');
        $grade = post('grade');
        $pass1 = post('pass1');
        $pass2 = post('pass2');
        if(strlen($pass1) > 0 && $pass1 == $pass2) {
            $this->userModel->update_pass($pass1, $pass2, $_SESSION['stu_id']);
        }
        $res= $this->userModel->update($_SESSION['stu_id'], $stu_name, $stu_number, $class, $grade);
        if($res > 0)
            echo json_encode([
                'status' => true
            ]);
        else
            echo json_encode([
                'status' => false
            ]);
    }

    public function show_stu() {
        if(isset($_SESSION['stu_id'])) {
            $id = get('id');
            $info = $this->userModel->get_stu_info($id);
            $res = $this->examModel->get_all_exam($id);
            $counts = $this->examModel->count_collect($id);
            $nums = $this->examModel->count_pro($id);
            foreach ($res as &$row) {
                $k = 0;
                $pros = $this->examModel->get_result($row[0])[0];
                foreach ($pros as $pro)
                    if($pro[2] == $pro[4])
                        $k++;
                $row[] = $k;
            }
            unset($row);
            parent::assign('acs', $nums[0]);
            parent::assign('mount', $nums[1]);
            parent::assign('collect', $counts[0]);
            parent::assign('exams', $res);
            parent::assign($info);
            parent::display('stu_user.html');
        } else {
            parent::display('login.html');
        }
    }

    public function bind_user() {
        $id = get('id',  1);
        $tealists = $this->userModel->get_teacher();
        $classList = $this->userModel->get_class($id);
        parent::assign('tea_id', $id);
        parent::assign('classLists', $classList);
        parent::assign('tealists', $tealists);
        parent::display('tea_stu_bind.html');
    }

    public function query_class()
    {
        $classname = post('classname');
        $res = $this->userModel->get_all_user_by_class($classname);
        echo json_encode(
            ['res' => $res]
        );
    }

    public function add_class() {
        $classname = post('classname');
        $tea_id =post('tea_id');
        $row = $this->userModel->bind_class($classname, $tea_id);
        if($row > 0) {
            echo json_encode([
                'status' => true
            ]);
        } else {
            echo json_encode([
                'status' => false
            ]);
        }
    }

    public function get_user() {
        $classname = post('classname');
        $res = $this->userModel->get_all_user_by_class($classname);
        echo json_encode($res);
    }

    public function del_class() {
        $classname = post('classname');
        $row= $this->userModel->unbind_class($classname);
        if($row > 0) {
            echo json_encode([
                'status' => true
            ]);
        } else {
            echo json_encode([
                'status' => false
            ]);
        }
    }

    public function tea_manager() {
        $res = $this->userModel->get_tea();
        parent::assign('teaLists', $res);
        parent::display('teacher_manager.html');
    }

    public function update_teacher() {
        $row = $this->userModel->update_tea($_POST);
        if($row > 0) {
            echo json_encode([
                'status' => true
            ]);
        } else {
            echo json_encode([
                'status' => false
            ]);
        }
    }

    public function del_tea() {
        $row = $this->userModel->del_tea($_POST);
        if($row > 0) {
            echo json_encode([
                'status' => true
            ]);
        } else {
            echo json_encode([
                'status' => false
            ]);
        }
    }

    public function stu_manager() {
        $page = (int)get('id', 1);
        $res = $this->userModel->get_stu($page);
        parent::assign('page', $page);
        parent::assign('stuLists', $res);
        parent::display('student_manager.html');
    }

    public function update_student() {
        $row = $this->userModel->update_stu($_POST);
        if($row > 0) {
            echo json_encode([
                'status' => true
            ]);
        } else {
            echo json_encode([
                'status' => false
            ]);
        }
    }

    public function del_stu() {
        $row = $this->userModel->del_stu($_POST);
        if($row > 0) {
            echo json_encode([
                'status' => true
            ]);
        } else {
            echo json_encode([
                'status' => false
            ]);
        }
    }

    public function show_tea()
    {
        parent::display('tea_user.html');
    }
}