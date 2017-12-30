<?php

namespace admin\controller;

use system\Controller;

class BaseController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->checkLogin();
    }

    protected function checkLogin()
    {
        if (PROJECT == 'admin' && ($this->params['c'] != 'login')) {
            if (empty($_SESSION['admin'])) {
                $url = '/admin.php/login/login';
                header("Location:$url");
            }
        }
        return true;
    }
}
