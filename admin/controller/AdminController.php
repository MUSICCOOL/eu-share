<?php

namespace admin\controller;

use admin\model\ConstantModel;
use lib\Page;
use admin\model\AdminModel;

class AdminController extends BaseController
{
    public function index()
    {
        $page  = empty($this->params['page']) ? 1 : $this->params['page'];
        $count = empty($this->params['count']) ? ConstantModel::DEFAULR_PER_PAGE_COUNT : $this->params['count'];

        $admins = AdminModel::orderBy('created_at', 'desc');

        $total = $admins->count();

        $page = new Page($total, $page, $count);

        $admins = $admins->skip($page->limit['start'])->take($page->limit['count'])->get();

        // 渲染模板
        $this->renderView('admin/list', ['admins' => $admins, 'paginator' => $page->fpage()]);
    }

    public function add()
    {
        // 渲染模板
        $this->renderView('admin/add');
    }

    public function doAdd()
    {
        $admin           = new AdminModel();
        $admin->username = $this->params['username'];
        $admin->password = md5($this->params['password']);
        $admin->phone    = $this->params['phone'];
        $admin->email    = $this->params['email'];
        try {
            if ($admin->save()) {
                $this->index();
            }
        } catch (\Exception $e) {
            echo '该用户名已存在！';
            echo '<a href="' . APP_FILE . '?c=admim&a=add">返回</a>';
        }
    }

    public function delete()
    {
        $ids     = is_array($this->params['id']) ? $this->params['id'] : explode(',', trim($this->params['id'], ','));
        $deleted = AdminModel::destroy($ids);
        if ($deleted) {
            echo json_encode([]);
        }
    }
}
