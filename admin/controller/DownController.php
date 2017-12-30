<?php

namespace admin\controller;

use admin\model\ConstantModel;
use admin\model\DownModel;
use admin\model\IncomeModel;
use lib\Page;

class DownController extends BaseController
{
    public function index()
    {
        $page  = empty($this->params['page']) ? 1 : $this->params['page'];
        $count = empty($this->params['count']) ? ConstantModel::DEFAULR_PER_PAGE_COUNT : $this->params['count'];

        $down  = DownModel::orderBy("created_at", "desc");
        $total = $down->count();

        $page = new Page($total, $page, $count);

        $records = $down->skip($page->limit['start'])->take($page->limit['count'])->get();

        $this->renderView('down/list', ['records' => $records, 'paginator' => $page->fpage()]);
    }

    public function delete()
    {
        $ids     = is_array($this->params['id']) ? $this->params['id'] : explode(',', trim($this->params['id'], ','));
        $deleted = DownModel::destroy($ids);
        if ($deleted) {
            $this->renderJson(['code' => ConstantModel::DELETE_SUCCESS]);
        } else {
            $this->renderJson(['code' => ConstantModel::DELETE_ERROR]);
        }
    }
}
