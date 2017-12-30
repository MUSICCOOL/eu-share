<?php

namespace admin\controller;

use admin\model\ConstantModel;
use admin\model\ConsumerModel;
use lib\Page;

class ConsumerController extends BaseController
{
    public function index()
    {
        $page  = empty($this->params['page']) ? 1 : $this->params['page'];
        $count = empty($this->params['count']) ? ConstantModel::DEFAULR_PER_PAGE_COUNT : $this->params['count'];

        $consumer = ConsumerModel::orderBy("created_at", "desc");
        $total    = $consumer->count();

        $page = new Page($total, $page, $count);

        $records = $consumer->skip($page->limit['start'])->take($page->limit['count'])->get();

        $types = ConsumerModel::$consumer_types;
        foreach ($records as &$value) {
            $value['type'] = $types[$value['type']];
        }

        $this->renderView('consumer/list', ['records' => $records, 'paginator' => $page->fpage()]);
    }

    public function delete()
    {
        $ids     = is_array($this->params['id']) ? $this->params['id'] : explode(',', trim($this->params['id'], ','));
        $deleted = ConsumerModel::destroy($ids);
        if ($deleted) {
            $this->renderJson(['code' => ConstantModel::DELETE_SUCCESS]);
        } else {
            $this->renderJson(['code' => ConstantModel::DELETE_ERROR]);
        }
    }
}
