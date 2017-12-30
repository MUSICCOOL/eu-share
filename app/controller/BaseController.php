<?php

namespace app\controller;

use app\model\ConstantModel;
use app\model\MapModel;
use app\model\UserModel;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use system\Controller;

class BaseController extends Controller
{
    protected $needCheckLogin = [
        'account' => [],
        'project' => [
            'add'      => 'alert',
            'download' => 'alert',
            'doLike' => 'json',
            'doReward' => 'json',
        ],
        'comment' => [
            'sendComment' => 'json',
        ],
    ];

    public function __construct()
    {
        parent::__construct();

        $this->checkLogin();
    }

    protected function checkLogin()
    {
        $c = isset($this->params["c"]) ? $this->params["c"] : '';
        $a = isset($this->params["a"]) ? $this->params["a"] : '';

        if (isset($this->needCheckLogin[$c]) && (isset($this->needCheckLogin[$c][$a]) || empty($this->needCheckLogin[$c]))) {
            if (empty($_SESSION['username'])) {
                if ($this->needCheckLogin[$c][$a] == 'json') {
                    $this->renderJson(['code' => ConstantModel::NOT_LOGIN_YET_ERROR]);
                } else {
                    $this->alert(['code' => ConstantModel::NOT_LOGIN_YET_ERROR]);
                }
            } else {
                $this->params['username'] = $_SESSION['username'];
                $user                     = new UserModel();
                $this->user               = $user->where('username', $_SESSION['username'])->first();
                if ($this->user->is_active == UserModel::IS_NOT_ACTIVE) {
                    if (isset($this->needCheckLogin[$c][$a]) && $this->needCheckLogin[$c][$a] == 'json') {
                        $this->renderJson(['code' => ConstantModel::USER_NOT_ACTIVE]);
                    } else {
                        $this->alert(['code' => ConstantModel::USER_NOT_ACTIVE]);
                    }
                }
            }
        }

        if (!empty($_SESSION['username'])) {
            $this->params['username'] = $_SESSION['username'];
            $user                     = new UserModel();
            $this->user               = $user->where('username', $_SESSION['username'])->first();
        }

        return true;
    }

    protected function renderView($template, $data = array())
    {
        $data['site_set'] = $this->getSiteSet();
        if (!empty($this->user)) {
            $data['user'] = $this->user;
        }
        // 渲染模板
        echo $this->view->render($template . '.html', $data);
        exit();
    }


    protected function getSiteSet()
    {
        $model   = new MapModel();
        $records = $model->whereIn('key_name', MapModel::$siteKey)->get();
        $site    = [];
        foreach ($records as $record) {
            $site[$record['key_name']] = $record['key_value'];
        }
        return $site;
    }

    /**
     *  判断邮箱格式以及是否已注册
     *
     * @param      $email
     * @param bool $is_reg
     * @return bool
     */
    public function checkEmail($email, $is_reg = true)
    {
        if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $email)) {
            return false;
        }
        if ($is_reg) {
            $user   = new UserModel();
            $result = $user->where('email', $email)->first();
            if (!empty($result)) {
                if ($result->is_active == UserModel::IS_NOT_ACTIVE) {
                    UserModel::destroy([$result->user_id]);
                } else {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 判断用户名是否存在
     *
     * @param $username
     */
    public function checkUsername($username)
    {
        $user   = new UserModel();
        $result = $user->where('username', $username)->first();
        if (!empty($result)) {
            if ($result->is_active == UserModel::IS_NOT_ACTIVE) {
                UserModel::destroy([$result->user_id]);
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * 确认密码是否一致
     *
     * @param $pwd
     * @param $pwd_reset
     */
    public function pwdConfirm($pwd, $pwd_confirm)
    {
        if (strlen($pwd) < UserModel::PAASSWORD_MIN_LENGTH) {
            return false;
        }

        if ($pwd != $pwd_confirm) {
            return false;
        }
        return true;
    }

    /**
     * 判断昵称是否存在
     *
     * @param $nickrname
     */
    public function checkNickname($nickname)
    {
        $user   = new UserModel();
        $result = $user->where('nickname', $nickname)->first();
        if (!empty($result)) {
            if ($result->is_active == UserModel::IS_NOT_ACTIVE) {
                UserModel::destroy([$result->user_id]);
            } else {
                return false;
            }
        }
        return true;
    }

    public function sendMail($address, $name, $subject, $body, $altbody = '')
    {
        $mail = new PHPMailer(true);
        $mailConfig = \Init::getConfig('mail');
        try {
            //服务器设置
            //是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
            $mail->SMTPDebug = $mailConfig['debug'];
            //使用smtp鉴权方式发送邮件
            $mail->isSMTP();
            //smtp需要鉴权 这个必须是true
            $mail->SMTPAuth=true;
            //链接邮箱的服务器地址
            $mail->Host = $mailConfig['host'];
            //设置使用ssl加密方式登录鉴权
            $mail->SMTPSecure = 'ssl';
            //设置ssl连接smtp服务器的远程服务器端口号
            $mail->Port = $mailConfig['port'];
            //smtp登录的账号
            $mail->Username = $mailConfig['username'];
            //smtp登录的密码 使用生成的授权码
            $mail->Password = $mailConfig['password'];
            // 设置编码
            $mail->CharSet = 'UTF-8';

            // 收件人
            $mail->setFrom($mailConfig['address'], $mailConfig['name']);
            //设置收件人邮箱地址 该方法有两个参数 第一个参数为收件人邮箱地址 第二参数为给该地址设置的昵称
            $mail->addAddress($address, $name);
            $mail->addReplyTo($mailConfig['address'], $mailConfig['name']);

            // 内容
            //邮件正文是否为html编码
            $mail->isHTML(true);
            //添加该邮件的主题
            $mail->Subject = $subject;
            //添加邮件正文 上方将isHTML设置成了true，则可以是完整的html字符串
            $mail->Body    = $body;
            $mail->AltBody = !empty($altbody) ?: '为了查看该邮件，请切换到支持 HTML 的邮件客户端';

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}