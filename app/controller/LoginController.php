<?php

namespace app\controller;

use app\model\ConstantModel;
use app\model\UserModel;

class LoginController extends BaseController
{
    public function login()
    {
        // 渲染模板
        $this->renderView('login');
    }

    public function doLogin()
    {
        // 判断是否为邮箱登录
        if (preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $this->params['username'])) {
            $user = UserModel::where('email', $this->params['username'])->first();
        } else {
            $user = UserModel::where('username', $this->params['username'])->first();
        }

        if ($user->password == md5($this->params['password'])) {
            $_SESSION['username'] = $user->username;
            $this->renderJson();
        } else {
            $this->renderJson(['code' => ConstantModel::LOGIN_ERROR]);
        }
    }

    public function logOut()
    {
        unset($_SESSION['username']);
        $this->redirect('', '', \Init::getConfig('site_url'));
    }
}
