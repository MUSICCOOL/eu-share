<?php

namespace app\controller;

use app\model\BcommentModel;
use app\model\ConstantModel;
use app\model\UserModel;
use lib\Page;

class BcommentController extends BaseController
{
    public function sendComment()
    {
        $comment          = new BcommentModel();
        $comment->b_id    = $this->params['b_id'];
        $comment->user_id = $this->user->user_id;

        if (empty($this->params['comment'])) {
            $this->renderJson(['code' => ConstantModel::COM_SEND_ERROR]);
        }

        $comment->comment = $this->params['comment'];

        if ($comment->save()) {
            $this->renderJson();
        } else {
            $this->renderJson(['code' => ConstantModel::COM_SEND_ERROR]);
        }
    }

    public function getList()
    {
        $page     = empty($this->params['page']) ? 1 : $this->params['page'];
        $count    = empty($this->params['count']) ? ConstantModel::DEFAULR_PER_PAGE_COUNT : $this->params['count'];
        $comments = BcommentModel::where('b_id', $this->params['b_id'])->where('status',
            BcommentModel::COM_STATUS_SUCCESS)->orderBy('created_at', 'desc');
        $total    = $comments->count();

        $page = new Page($total, $page, $count);

        $comments = $comments->skip($page->limit['start'])->take($page->limit['count'])->get();

        $user_ids = [];

        foreach ($comments as &$value) {
            $user_ids[] = $value['user_id'];
        }
        $users   = UserModel::whereIn('user_id', $user_ids)->get();
        $p_users = [];
        foreach ($users as $user) {
            $p_users[$user->user_id] = $user;
        }

        foreach ($comments as &$value) {
            $value['user'] = $p_users[$value['user_id']];
        }

        // 渲染模板
        $this->renderJson(['comments' => $comments]);
    }
}