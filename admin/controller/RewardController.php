<?php

namespace admin\controller;

use admin\model\ConstantModel;
use admin\model\RewardModel;
use lib\Page;

class RewardController extends BaseController
{
    public function index()
    {
        $page  = empty($this->params['page']) ? 1 : $this->params['page'];
        $count = empty($this->params['count']) ? ConstantModel::DEFAULR_PER_PAGE_COUNT : $this->params['count'];

        $reward = RewardModel::orderBy("created_at", "desc");
        $total  = $reward->count();

        $page = new Page($total, $page, $count);

        $records = $reward->skip($page->limit['start'])->take($page->limit['count'])->get();

        $this->renderView('reward/list', ['records' => $records, 'paginator' => $page->fpage()]);
    }

    public function delete()
    {
        $ids     = is_array($this->params['id']) ? $this->params['id'] : explode(',', trim($this->params['id'], ','));
        $deleted = RewardModel::destroy($ids);
        if ($deleted) {
            $this->renderJson(['code' => ConstantModel::DELETE_SUCCESS]);
        } else {
            $this->renderJson(['code' => ConstantModel::DELETE_ERROR]);
        }
    }
}
