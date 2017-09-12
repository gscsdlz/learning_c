<?php

class IndexController extends Smarty
{
    public function __construct()
    {
        parent::__construct();
    }

    public function login()
    {
        parent::display('login.html');
    }

    public function index()
    {
        $this->login();
    }
}