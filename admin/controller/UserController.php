<?php

namespace admin\controller;

use admin\model\ConstantModel;
use admin\model\ConsumerModel;
use admin\model\UserModel;
use lib\Page;

class UserController extends BaseController
{
    public function index()
    {
        $page  = empty($this->params['page']) ? 1 : $this->params['page'];
        $count = empty($this->params['count']) ? ConstantModel::DEFAULR_PER_PAGE_COUNT : $this->params['count'];

        $users = UserModel::orderBy('created_at', 'desc');

        $total = $users->count();

        $page = new Page($total, $page, $count);

        $users = $users->skip($page->limit['start'])->take($page->limit['count'])->get();
        foreach ($users as &$value) {
            $value['sex']       = UserModel::$sex[$value['sex']];
            $value['is_active'] = UserModel::$is_active[$value['is_active']];
        }

        // 渲染模板
        $this->renderView('user/list', ['users' => $users, 'paginator' => $page->fpage()]);
    }

    public function update()
    {
        $user            = UserModel::find($this->params['user_id']);
        $privacy         = json_decode($user->privacy, true);
        $user->pri_email = $privacy['email'];
        $user->pri_org   = $privacy['org'];
        $this->renderView('user/update', ['user' => $user]);
    }

    public function doUpdate()
    {
        $user = UserModel::find($this->params['user_id']);

        $user->nickname  = $this->params['nickname'];
        $user->sex       = $this->params['sex'];
        $user->email     = $this->params['email'];
        $user->province  = $this->params['province'];
        $user->city      = $this->params['city'];
        $user->org       = $this->params['org'];
        $user->e_points  = $this->params['e_points'];
        $user->intro     = $this->params['intro'];
        $user->is_active = $this->params['is_active'];

        $email         = $this->params['pri_email'];
        $org           = $this->params['pri_org'];
        $user->privacy = json_encode([
            'email' => $email,
            'org'   => $org,
        ]);

        if (!$user->save()) {
            $this->alert(ConstantModel::UPDATE_ERROR);
        }
        $this->redirect('user', 'index');
    }

    public function avatar()
    {
        $user = UserModel::find($this->params['user_id']);
        $this->renderView('user/avatar', ['user' => $user]);
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

        $user = UserModel::find($this->params['user_id']);

        $user->avatar = $url;
        if ($user->save()) {
            $this->renderJson(['result' => $url]);
        } else {
            $this->renderJson(['code' => ConstantModel::AVATAT_CHANGE_ERROR]);
        }
    }

    public function delete()
    {
        $ids     = is_array($this->params['id']) ? $this->params['id'] : explode(',', trim($this->params['id'], ','));
        $deleted = UserModel::destroy($ids);
        if ($deleted) {
            $this->renderJson(['code' => ConstantModel::DELETE_SUCCESS]);
        } else {
            $this->renderJson(['code' => ConstantModel::DELETE_ERROR]);
        }
    }
}
