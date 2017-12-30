<?php

namespace admin\controller;

use admin\model\ConstantModel;
use admin\model\TypeModel;

class TypeController extends BaseController
{
    public function index()
    {
        $page  = empty($this->params['page']) ? 1 : $this->params['page'];
        $count = empty($this->params['count']) ? ConstantModel::DEFAULR_PER_PAGE_COUNT : $this->params['count'];

        $types = TypeModel::orderBy('order', 'asc')->orderBy('created_at', 'desc')->paginate($count);

        // 渲染模板
        $this->renderView('type/list', ['types' => $types]);
    }

    public function add()
    {
        $types = TypeModel::orderBy('order', 'asc')->orderBy('created_at', 'desc')->get();
        // 渲染模板
        $this->renderView('type/add', ['types' => $types]);
    }

    public function doAdd()
    {
        $type        = new TypeModel();
        $type->name  = $this->params['name'];
        $type->p_id  = $this->params['p_id'];
        $type->order = $this->params['order'];
        try {
            if ($type->save()) {
                $this->index();
            }
        } catch (\Exception $e) {
            echo '该类型已存在！';
            echo '<a href="' . APP_FILE . '?c=type&a=add">返回</a>';
        }
    }

    public function update()
    {
        $types = TypeModel::orderBy('order', 'asc')->orderBy('created_at', 'desc')->get();
        $type  = TypeModel::find($this->params['id']);
        $this->renderView('type/update', ['type' => $type, 'types' => $types]);
    }

    public function doUpdate()
    {
        $type = TypeModel::find($this->params['id']);

        $type['name']  = $this->params['name'];
        $type['p_id']  = $this->params['p_id'];
        $type['order'] = $this->params['order'];
        if (!$type->save()) {
            $this->alert(ConstantModel::UPDATE_ERROR);
        }
        $this->redirect('type', 'index');
    }

    public function delete()
    {
        $ids     = is_array($this->params['id']) ? $this->params['id'] : explode(',', trim($this->params['id'], ','));
        $deleted = TypeModel::destroy($ids);
        if ($deleted) {
            $this->renderJson(['code' => ConstantModel::DELETE_SUCCESS]);
        } else {
            $this->renderJson(['code' => ConstantModel::DELETE_ERROR]);
        }
    }
}
