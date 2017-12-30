<?php

namespace admin\controller;

use admin\model\BCommentModel;
use admin\model\ConstantModel;
use lib\Page;

class BCommentController extends BaseController
{
    public function index()
    {
        $page     = empty($this->params['page']) ? 1 : $this->params['page'];
        $count    = empty($this->params['count']) ? ConstantModel::DEFAULR_PER_PAGE_COUNT : $this->params['count'];
        $comments = BCommentModel::orderBy('created_at', 'desc');
        $total    = $comments->count();

        $page = new Page($total, $page, $count);

        $comments = $comments->skip($page->limit['start'])->take($page->limit['count'])->get();

        foreach ($comments as &$value) {
            $value['status'] = BCommentModel::$com_status[$value['status']];
        }

        // 渲染模板
        $this->renderView('bcomment/list', ['comments' => $comments, 'paginator' => $page->fpage()]);
    }

    public function delete()
    {
        $ids     = is_array($this->params['id']) ? $this->params['id'] : explode(',', trim($this->params['id'], ','));
        $deleted = BCommentModel::destroy($ids);
        if ($deleted) {
            $this->renderJson(['code' => ConstantModel::DELETE_SUCCESS]);
        } else {
            $this->renderJson(['code' => ConstantModel::DELETE_ERROR]);
        }
    }
}
