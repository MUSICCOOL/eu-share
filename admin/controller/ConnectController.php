<?php

namespace admin\controller;

use admin\model\ConnectModel;
use admin\model\ConstantModel;
use lib\Page;

class ConnectController extends BaseController
{
    public function index()
    {
        $page  = empty($this->params['page']) ? 1 : $this->params['page'];
        $count = empty($this->params['count']) ? ConstantModel::DEFAULR_PER_PAGE_COUNT : $this->params['count'];

        $connect = ConnectModel::orderBy("created_at", "desc");
        $total   = $connect->count();

        $page = new Page($total, $page, $count);

        $records = $connect->skip($page->limit['start'])->take($page->limit['count'])->get();
        foreach ($records as &$value) {
            $value['status'] = ConnectModel::$connect_status[$value['status']];
        }

        $this->renderView('connect/list', ['records' => $records, 'paginator' => $page->fpage()]);
    }

    public function setRead()
    {
        $ids = is_array($this->params['id']) ? $this->params['id'] : explode(',', trim($this->params['id'], ','));

        $connect = new ConnectModel();
        foreach ($ids as $id) {
            $con         = $connect::find($id);
            $con->status = ConnectModel::IS_ALREADY_READ;
            $con->save();
        }
    }

    public function delete()
    {
        $ids     = is_array($this->params['id']) ? $this->params['id'] : explode(',', trim($this->params['id'], ','));
        $deleted = ConnectModel::destroy($ids);
        if ($deleted) {
            $this->renderJson(['code' => ConstantModel::DELETE_SUCCESS]);
        } else {
            $this->renderJson(['code' => ConstantModel::DELETE_ERROR]);
        }
    }
}
