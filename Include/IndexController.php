<?php

/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/9/7
 * Time: 14:40
 */
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