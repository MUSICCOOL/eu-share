<?php

namespace admin\controller;

use admin\model\ConstantModel;
use admin\model\IncomeModel;
use lib\Page;

class IncomeController extends BaseController
{
    public function index()
    {
        $page  = empty($this->params['page']) ? 1 : $this->params['page'];
        $count = empty($this->params['count']) ? ConstantModel::DEFAULR_PER_PAGE_COUNT : $this->params['count'];

        $consumer = IncomeModel::orderBy("created_at", "desc");
        $total    = $consumer->count();

        $page = new Page($total, $page, $count);

        $records = $consumer->skip($page->limit['start'])->take($page->limit['count'])->get();

        $types = IncomeModel::$income_types;
        foreach ($records as &$value) {
            $value['type'] = $types[$value['type']];
        }

        $this->renderView('income/list', ['records' => $records, 'paginator' => $page->fpage()]);
    }

    public function delete()
    {
        $ids     = is_array($this->params['id']) ? $this->params['id'] : explode(',', trim($this->params['id'], ','));
        $deleted = IncomeModel::destroy($ids);
        if ($deleted) {
            $this->renderJson(['code' => ConstantModel::DELETE_SUCCESS]);
        } else {
            $this->renderJson(['code' => ConstantModel::DELETE_ERROR]);
        }
    }
}
