<?php

class IndexController extends Smarty
{
    public function __construct()
    {
        parent::__construct();
    }

    public function login()
    {
        if(isset($_SESSION['username'])) {
            $this->index();
        } else {
            parent::display('login.html');
        }
    }

    public function index()
    {
        parent::display('index.html');
    }
}