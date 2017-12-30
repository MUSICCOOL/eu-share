<?php

namespace admin\controller;

use admin\model\AdminModel;

class LoginController extends BaseController
{
    public function login()
    {
        // 渲染模板
        $this->renderView('login');
    }

    public function doLogin()
    {
        $admin = AdminModel::where('username', $this->params['username'])->first();
        if ($admin->password == md5($this->params['password'])) {
            $_SESSION['admin'] = $this->params['username'];
            $this->redirect('index', 'index');
        } else {
            $this->alert('用户名或密码错误！');
            $this->renderView('login');
        }
    }

    public function loginOut()
    {
        unset($_SESSION['admin']);
        $this->redirect('login', 'login');
    }
}
