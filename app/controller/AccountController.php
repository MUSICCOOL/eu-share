<?php

namespace app\controller;

use app\model\ConnectModel;
use app\model\ConstantModel;
use app\model\ConsumerModel;
use app\model\DownModel;
use app\model\IncomeModel;
use app\model\OrderModel;
use app\model\ProjectModel;
use app\model\RechargeModel;
use app\model\RewardModel;
use app\model\TypeModel;
use app\model\UserModel;
use lib\Page;

class AccountController extends BaseController
{
    public function index()
    {
        $pro_num    = ProjectModel::where('user_id', $this->user->user_id)->count();
        $down_num   = DownModel::where('user_id', $this->user->user_id)->count();
        $reward_num = RewardModel::where('user_id', $this->user->user_id)->count();
        $this->renderView('account/index',
            ['pro_num' => $pro_num, 'down_num' => $down_num, 'reward_num' => $reward_num]);
    }

    public function account()
    {
        $page  = empty($this->params['page']) ? 1 : $this->params['page'];
        $count = empty($this->params['count']) ? ConstantModel::DEFAULR_PER_PAGE_COUNT : $this->params['count'];

        $project = new ProjectModel();
        $user    = new UserModel();

        // 获取收益记录列表
        $income       = new IncomeModel();
        $incomes      = $income->where('user_id', $this->user->user_id)->orderBy('created_at', 'desc');
        $total_i      = $incomes->count();
        $page_i       = new Page($total_i, $page, $count);
        $incomes      = $incomes->skip($page_i->limit['start'])->take($page_i->limit['count'])->get();
        $income_types = IncomeModel::$income_types;
        foreach ($incomes as &$value) {
            $value['pro_name']   = $project->find($value['pro_id'])->name;
            $value['u_nickname'] = $user->find($value['u_id'])->nickname;
            $value['type']       = $income_types[$value['type']];
        }
        // 获取消费记录列表
        $consumer       = new ConsumerModel();
        $consumers      = $consumer->where('user_id', $this->user->user_id)->orderBy('created_at', 'desc');
        $total_c        = $consumers->count();
        $page_c         = new Page($total_c, $page, $count);
        $consumers      = $consumers->skip($page_c->limit['start'])->take($page_c->limit['count'])->get();
        $consumer_types = ConsumerModel::$consumer_types;
        foreach ($consumers as &$value) {
            $value['pro_name'] = $project->find($value['pro_id'])->name;
            $value['type']     = $consumer_types[$value['type']];
        }

        // 获取订单记录列表
        $order   = new OrderModel();
        $orders  = $order->where('user_id', $this->user->user_id)->orderBy('created_at', 'desc');
        $total_o = $orders->count();
        $page_o  = new Page($total_o, $page, $count);
        $orders  = $orders->skip($page_o->limit['start'])->take($page_o->limit['count'])->get();
        foreach ($orders as $key => &$value) {
            $value['type']   = OrderModel::$order_type[$value['type']];
            $value['status'] = OrderModel::$order_status[$value['status']];
        }

        $this->renderView('account/account', [
            'consumers'   => $consumers,
            'incomes'     => $incomes,
            'orders'      => $orders,
            'paginator_c' => $page_c->fpage(),
            'paginator_i' => $page_i->fpage(),
            'paginator_o' => $page_o->fpage(),
        ]);
    }

    public function recharge()
    {
        $this->renderView('account/recharge');
    }

    public function profile()
    {
        $privacy               = json_decode($this->user->privacy, true);
        $this->user->pri_email = $privacy['email'];
        $this->user->pri_org   = $privacy['org'];
        $this->renderView('account/profile');
    }

    public function profileSet()
    {
        $this->user->nickname = $this->params['nickname'];
        $this->user->sex      = $this->params['sex'];
        $this->user->email    = $this->params['email'];
        $this->user->province = $this->params['province'];
        $this->user->city     = $this->params['city'];
        $this->user->org      = $this->params['org'];
        $this->user->intro    = $this->params['intro'];

        if ($this->user->save()) {
            $this->redirect('account', 'profile');
        } else {
            $this->alert(['code' => ConstantModel::UPDATE_ERROR]);
        }
    }

    public function avatarSet()
    {
        $file = $_FILES["avatar_file"];

        $allowTypes = [
            'image/jpg',
            'image/png',
            'image/jpeg',
            'image/pjpeg',
            'image/gif',
            'image/bmp',
            'image/x-png',
        ];

        $path = ConstantModel::AVATAR_UPLOAD_PATH;

        $url = $this->imgUpload($file, $allowTypes, $path);
        
        $url = $this->getShowUrls([$url]);

        $this->user->avatar = $url;
        if ($this->user->save()) {
            $this->renderJson(['result' => $url]);
        } else {
            $this->renderJson(['code' => ConstantModel::AVATAT_CHANGE_ERROR]);
        }
    }

    public function privacySet()
    {
        $email               = $this->params['pri_email'];
        $org                 = $this->params['pri_org'];
        $this->user->privacy = json_encode([
            'email' => $email,
            'org'   => $org,
        ]);
        if ($this->user->save()) {
            $this->redirect('account', 'profile');
        } else {
            $this->alert(['code' => ConstantModel::UPDATE_ERROR]);
        }
    }

    public function pwdSet()
    {
        if (md5($this->params['old_pwd']) != $this->user->password) {
            $this->alert(['code' => ConstantModel::OLD_PASSWORD_ERROR]);
        }

        if (strlen($this->params['new_pwd']) < UserModel::PAASSWORD_MIN_LENGTH) {
            $this->alert(['code' => ConstantModel::PASSWORD_LEN_ERROR]);
        }

        if ($this->params['new_pwd'] != $this->params['c_new_pwd']) {
            $this->alert(['code' => ConstantModel::PASSWORD_CONFIRMED_ERROR]);
        }
        $this->user->password = md5($this->params['new_pwd']);
        if ($this->user->save()) {
            $this->redirect('account', 'profile');
        } else {
            $this->alert(['code' => ConstantModel::PASSWORD_CHANGE_ERROR]);
        }
    }


    public function projectList()
    {
        $page  = empty($this->params['page']) ? 1 : $this->params['page'];
        $count = empty($this->params['count']) ? ConstantModel::DEFAULR_PER_PAGE_COUNT : $this->params['count'];

        $project = new ProjectModel();
        $type    = new TypeModel();

        // 审核通过项目
        $projects_ok = $project->where('user_id', $this->user->user_id)->where('status',
            ProjectModel::PRO_STATUS_SUCCESS)->orderBy('created_at', 'desc');
        $total_ok    = $projects_ok->count();
        $page_ok     = new Page($total_ok, $page, $count);
        $projects_ok = $projects_ok->skip($page_ok->limit['start'])->take($page_ok->limit['count'])->get();
        foreach ($projects_ok as &$value_ok) {
            $value_ok['type'] = $type->find($value_ok['type'])->name;
        }

        // 审核未通过项目
        $projects_no = $project->where('user_id', $this->user->user_id)->where('status',
            ProjectModel::PRO_STATUS_BANED)->orderBy('created_at', 'desc');
        $total_no    = $projects_no->count();
        $page_no     = new Page($total_no, $page, $count);
        $projects_no = $projects_no->skip($page_no->limit['start'])->take($page_no->limit['count'])->get();
        foreach ($projects_no as &$value_no) {
            $value_no['type'] = $type->find($value_no['type'])->name;
        }

        // 未审核项目
        $projects_without = $project->where('user_id', $this->user->user_id)->where('status',
            ProjectModel::PRO_STATUS_NONE)->orderBy('created_at', 'desc');
        $total_without    = $projects_without->count();
        $page_without     = new Page($total_without, $page, $count);
        $projects_without = $projects_without->skip($page_without->limit['start'])->take($page_without->limit['count'])->get();
        foreach ($projects_without as &$value_without) {
            $value_without['type'] = $type->find($value_without['type'])->name;
        }

        // 渲染模板
        $this->renderView('account/project', [
            'projects_ok'       => $projects_ok,
            'projects_no'       => $projects_no,
            'projects_without'  => $projects_without,
            'paginator_ok'      => $page_ok->fpage(),
            'paginator_no'      => $page_no->fpage(),
            'paginator_without' => $page_without->fpage(),
        ]);
    }

    public function upVip()
    {
        $this->renderView('account/upvip');
    }

    public function downList()
    {
        $page  = empty($this->params['page']) ? 1 : $this->params['page'];
        $count = empty($this->params['count']) ? ConstantModel::DEFAULR_PER_PAGE_COUNT : $this->params['count'];

        $down  = new DownModel();
        $downs = $down->where('user_id', $this->user->user_id)->orderBy('created_at', 'desc');
        $total = $downs->count();
        $page  = new Page($total, $page, $count);
        $downs = $downs->skip($page->limit['start'])->take($page->limit['count'])->get();

        $project = new ProjectModel();

        $type = new TypeModel();

        foreach ($downs as &$value) {
            $value['project']         = $project->find($value['pro_id']);
            $value['project']['type'] = $type->find($value['project']['type'])->name;
        }

        // 渲染模板
        $this->renderView('account/down', [
            'downs'     => $downs,
            'paginator' => $page->fpage(),
        ]);
    }

    public function rewardList()
    {
        $page  = empty($this->params['page']) ? 1 : $this->params['page'];
        $count = empty($this->params['count']) ? ConstantModel::DEFAULR_PER_PAGE_COUNT : $this->params['count'];

        $reward  = new RewardModel();
        $rewards = $reward->where('user_id', $this->user->user_id)->orderBy('created_at', 'desc');
        $total   = $rewards->count();
        $page    = new Page($total, $page, $count);
        $rewards = $rewards->skip($page->limit['start'])->take($page->limit['count'])->get();

        $project = new ProjectModel();

        $type = new TypeModel();

        foreach ($rewards as &$value) {
            $value['project']         = $project->find($value['pro_id']);
            $value['project']['type'] = $type->find($value['project']['type'])->name;
        }

        // 渲染模板
        $this->renderView('account/reward', [
            'rewards'   => $rewards,
            'paginator' => $page->fpage(),
        ]);
    }

    public function service()
    {
        $this->renderView('account/service');
    }

    public function doConnect()
    {
        $title   = $this->params['title'];
        $cont    = $this->params['cont'];
        $connect = new ConnectModel();

        $count = $connect->where('user_id', $this->user->user_id)->where('created_at', ">=",
            date("Y-m-d H:i:s", strtotime(date("Y-m-d", time()))))->count();
        if ($count >= 3) {
            $this->alert(['code' => ConstantModel::MESSAGE_SEND_TOO_MATCH]);
        }

        $connect->title   = $title;
        $connect->content = $cont;
        $connect->user_id = $this->user->user_id;
        $connect->ip      = $this->getIP();
        if ($connect->save()) {
            $this->redirect('account', 'service');
        }
    }
}
