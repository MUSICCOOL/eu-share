<?php

namespace app\controller;

use app\model\BlogModel;
use app\model\BlogReadModel;
use app\model\ConstantModel;
use app\model\UserModel;
use lib\Page;

class BlogController extends BaseController
{
    public function lists()
    {
        $page  = empty($this->params['page']) ? 1 : $this->params['page'];
        $count = empty($this->params['count']) ? ConstantModel::DEFAULR_PER_PAGE_COUNT : $this->params['count'];

        $blog  = new BlogModel();
        $blogs = $blog->orderBy('created_at', 'desc');

        $total = $blogs->count();
        $page  = new Page($total, $page, $count);

        $blogs = $blogs->skip($page->limit['start'])->take($page->limit['count'])->get();

        $hot_blogs = $blog->orderBy('read', 'desc')->orderBy('created_at', 'desc')->limit(8)->get();

        // 渲染模板
        $this->renderView('blog/list', ['blogs' => $blogs, 'hot_blogs' => $hot_blogs, 'paginator' => $page->fpage()]);
    }

    public function show()
    {
        $model = new BlogModel();
        $blog  = $model->find($this->params['id']);

        $ip        = $this->getIP();
        $blog_read = new BlogReadModel();
        $record    = $blog_read->where('b_id', $this->params['id'])->where('ip', $ip)->first();
        if (empty($record)) {
            $blog->read++;
            $blog->save();
            $blog_read->b_id = $this->params['id'];
            $blog_read->ip   = $ip;
            $blog_read->save();
        }

        $blog['content'] = str_replace('\"', '"', $blog['content']);

        $hot_blogs = $model->orderBy('read', 'desc')->orderBy('created_at', 'desc')->limit(8)->get();

        $this->renderView('blog/show', ['blog' => $blog, 'hot_blogs' => $hot_blogs]);
    }
}
