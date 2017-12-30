<?php

namespace app\controller;

use app\model\ConstantModel;
use app\model\ProjectModel;
use lib\Log;
use lib\Page;
use system\Paginator;
use app\model\UserModel;

class UserController extends BaseController
{
    public function register()
    {
        $this->renderView('user/register');
    }

    public function doRegister()
    {
        $username         = $this->params['username'];
        $nickname         = $this->params['nickname'];
        $email            = $this->params['email'];
        $sex              = $this->params['sex'];
        $password         = $this->params['password'];
        $password_confirm = $this->params['password_confirm'];

        if (!$this->pwdConfirm($password, $password_confirm)) {
            Log::error('register password confirm error', ['pwd' => $password, 'pwd_cf' => $password_confirm]);
            $this->renderJson(['code' => [ConstantModel::PASSWORD_LEN_ERROR, ConstantModel::PASSWORD_CONFIRMED_ERROR]]);
        }

        if (!$this->checkUsername($username)) {
            Log::error('register username error', ['username' => $username]);
            $this->renderJson(['code' => ConstantModel::USERNAME_ALREADY_EXISTS]);
        }

        if (!$this->checkNickname($nickname)) {
            Log::error('register nickname error', ['nickname' => $nickname]);
            $this->renderJson(['code' => ConstantModel::NICKNAME_ALREADY_EXISTS]);
        }

        if (!$this->checkEmail($email)) {
            Log::error('register email error', ['email' => $email]);
            $this->renderJson(['code' => [ConstantModel::EMAIL_FORMAT_ERROR, ConstantModel::EMAIL_ALREADY_EXISTS]]);
        }

        $user                = new UserModel();
        $user->username      = $username;
        $user->nickname      = $nickname;
        $user->password      = md5($password);
        $user->email         = $email;
        $user->sex           = $sex;
        $user->privacy       = json_encode([
            'email' => '0', // 0 不可见
            'org'   => '1'  // 1 可见
        ]);
        $user->token         = md5($username . $password . time());//创建用于激活识别码
        $user->token_exptime = time() + 24 * 3600;//过期时间为24小时后
        // 设置默认头像
        $user->avatar = UserModel::DEFAULT_USER_AVATAR;

        if ($user->save()) {
            $_SESSION['username'] = $user->username;
            $address              = $user->email;
            $name                 = $user->username . '(昵称：' . $user->nickname . ')';
            $subject              = '欢迎注册-用户账号激活邮件';
            $activeLink           = \Init::getConfig('site_url') . 'user/active/token/' . $user->token;
            $body                 = "亲爱的" . $name . "：<br/>感谢您在我站注册了新帐号。<br/>请点击链接激活您的帐号。<br/>
    <a href='" . $activeLink . "' target='_blank'>" . $activeLink . "</a><br/>如果以上链接无法点击，请将它复制到你的浏览器地址栏中进入访问，该链接24小时内有效。";

            if ($this->sendMail($address, $name, $subject, $body)) {
                $this->renderJson();
            } else {
                $this->renderJson(['code' => ConstantModel::ACTIVE_MAIL_SEND_ERROR]);
            }

        } else {
            $this->renderJson(['code' => ConstantModel::FALSE]);
        }
    }

    public function active()
    {
        $token  = $this->params['token'];
        $user   = new UserModel();
        $result = $user->where('token', $token)->first();
        if (!empty($result)) {
            if (time() > $result->token_exptime) {
                $msg = '<stong class="text-warning">您的激活有效期已过，请&nbsp;&nbsp;<a href="' . \Init::getConfig('site_url') . 'user/register.html"> 重新注册</a>!</stong>';
                UserModel::destroy([$result->user_id]);
            } else {
                $nowUser            = $user->find($result->user_id);
                $nowUser->is_active = UserModel::IS_ACTIVE;
                if ($nowUser->save()) {
                    $msg = '<stong class="text-success">恭喜，您的账号已激活成功, 请&nbsp;&nbsp;<a class="btn-link" href="' . \Init::getConfig('site_url') . '">重新登录</a>' . '!</stong>';
                } else {
                    $msg = '<stong class="text-warning">抱歉，您的账号激活失败，请&nbsp;&nbsp;<a href="' . \Init::getConfig('site_url') . 'user/register.html"> 重新注册</a>!</stong>';
                    UserModel::destroy([$result->user_id]);
                }
            }
        } else {
            $msg = '<stong class="text-warning">抱歉，您尚未注册，请到注册页面注册&nbsp;&nbsp;<a href="' . \Init::getConfig('site_url') . 'user/register.html"> 注册</a></stong>';
        }
        $this->renderView('message', ['msg' => $msg]);
    }

    public function pwdReset()
    {
        $this->renderView('user/pwdreset');
    }

    public function doPwdResetEmail()
    {
        if (!$this->checkEmail($this->params['email'], false)) {
            $this->alert(['code' => ConstantModel::EMAIL_FORMAT_ERROR]);
        }

        $user = UserModel::where('email', $this->params['email'])->first();

        if (empty($user)) {
            $this->alert(ConstantModel::$codeMsg[ConstantModel::EMAIL_OR_NICKNAME_NOT_EXISTS]);
        } else {
            $address      = $user->email;
            $name         = $user->username . '(昵称：' . $user->nickname . ')';
            $subject      = '密码重置';
            $sign         = base64_encode($user->token . '-' . (time() + 60 * 60 * 24));
            $pwdResetLink = \Init::getConfig('site_url') . 'user/pwdset/sign/' . $sign;
            $body         = "亲爱的" . $name . "：<br/>感谢您使用我们的平台。<br/>请点击一下链接重置您的密码。<br/>
    <a href='" . $pwdResetLink . "' target='_blank'>" . $pwdResetLink . "</a><br/>如果以上链接无法点击，请将它复制到你的浏览器地址栏中进入访问，该链接24小时内有效。";

            if ($this->sendMail($address, $name, $subject, $body)) {
                $this->alert('密码重置邮件已发送至您的邮箱，请查收！');
            } else {
                $this->alert(['code' => ConstantModel::ACTIVE_MAIL_SEND_ERROR]);
            }
        }
    }

    public function pwdSet()
    {
        $sign          = base64_decode($this->params['sign']);
        $data          = explode('-', $sign);
        $token         = $data[0];
        $token_exptime = $data[1];

        $user   = new UserModel();
        $result = $user->where('token', $token)->first();
        if (!empty($result)) {
            if (time() > $token_exptime) {
                $msg = '<stong class="text-warning">该链接有效期已过，请&nbsp;&nbsp;<a href="' . \Init::getConfig('site_url') . 'user/pwdReset.html"> 重新申请</a>!</stong>';
            } else {
                $this->renderView('user/newpwdset', ['user_id' => $result->user_id]);
            }
        } else {
            $msg = '<stong class="text-warning">抱歉，您尚未注册，请到注册页面注册&nbsp;&nbsp;<a href="' . \Init::getConfig('site_url') . 'user/register.html"> 注册</a></stong>';
        }
        $this->renderView('message', ['msg' => $msg]);
    }

    public function doPwdSet()
    {
        $password         = $this->params['password'];
        $password_confirm = $this->params['pwd-confirm'];
        if (!$this->pwdConfirm($password, $password_confirm)) {
            $this->alert(['code' => [ConstantModel::PASSWORD_LEN_ERROR, ConstantModel::PASSWORD_CONFIRMED_ERROR]]);
        }
        $user_id        = $this->params['user_id'];
        $user           = UserModel::find($user_id);
        $user->password = md5($password);
        if ($user->save()) {
            $this->renderView('user/pwdsetover');
        } else {
            $this->alert(['code' => ConstantModel::PASSWORD_CHANGE_ERROR]);
        }

    }

    public function show()
    {
        $pro_user      = UserModel::find($this->params['user_id']);
        $pro_user->sex = UserModel::$sex_type[$pro_user->sex];
        $privacy       = json_decode($pro_user->privacy, true);
        if (isset($privacy['pri_email']) && $privacy['pri_email'] == 0) {
            $pro_user->email = '保密';
        }
        if (isset($privacy['pri_org']) && $privacy['pri_org'] == 0) {
            $pro_user->org = '保密';
        }

        $page  = empty($this->params['page']) ? 1 : $this->params['page'];
        $count = empty($this->params['count']) ? ConstantModel::DEFAULR_PER_PAGE_COUNT : $this->params['count'];

        $projects = ProjectModel::where('user_id', $this->params['user_id'])->where('status',
            ProjectModel::PRO_STATUS_SUCCESS)->orderBy('created_at', 'desc');

        $total = $projects->count();
        $page  = new Page($total, $page, $count);

        $projects = $projects->skip($page->limit['start'])->take($page->limit['count'])->get();

        // 渲染模板
        $this->renderView('user/show',
            ['pro_user' => $pro_user, 'projects' => $projects, 'paginator' => $page->fpage()]);
    }
}
