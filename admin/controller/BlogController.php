<?php

namespace admin\controller;

use admin\model\ConstantModel;
use admin\model\BlogModel;
use lib\Page;

class BlogController extends BaseController
{
    public function index()
    {
        $page  = empty($this->params['page']) ? 1 : $this->params['page'];
        $count = empty($this->params['count']) ? ConstantModel::DEFAULR_PER_PAGE_COUNT : $this->params['count'];

        $blog  = BlogModel::orderBy("created_at", "desc");
        $total = $blog->count();

        $page = new Page($total, $page, $count);

        $blogs = $blog->skip($page->limit['start'])->take($page->limit['count'])->get();

        $this->renderView('blog/list', ['blogs' => $blogs, 'paginator' => $page->fpage()]);
    }

    public function add()
    {
        $this->renderView('blog/add');
    }

    public function doAdd()
    {
        $blog           = new BlogModel();
        $blog->title    = $this->params['title'];
        $blog->keywords = $this->params['keywords'];
        $blog->intro    = $this->params['intro'];
        $blog->content  = $this->params['content'];
        if (!$blog->save()) {
            $this->alert(ConstantModel::ADD_ERROR);
        }
        $this->redirect('blog', 'add');

    }

    public function update()
    {
        $blog            = BlogModel::find($this->params['id']);
        $blog['content'] = str_replace('\"', '"', $blog['content']);
        $this->renderView('blog/update', ['blog' => $blog]);
    }

    public function doUpdate()
    {
        $blog = BlogModel::find($this->params['id']);

        $blog->title    = $this->params['title'];
        $blog->keywords = trim(preg_replace("/(\n)|(\s)+|(\t)|(\')|(')|(，)/", ',', $this->params['keywords']), ',');
        $blog->content  = str_replace('\"', '"', $this->params['content']);
        if (!$blog->save()) {
            $this->alert(ConstantModel::UPDATE_ERROR);
        }
        $this->redirect('blog', 'index');
    }

    public function delete()
    {
        $ids     = is_array($this->params['id']) ? $this->params['id'] : explode(',', trim($this->params['id'], ','));
        $deleted = BlogModel::destroy($ids);
        if ($deleted) {
            $this->renderJson(['code' => ConstantModel::DELETE_SUCCESS]);
        } else {
            $this->renderJson(['code' => ConstantModel::DELETE_ERROR]);
        }
    }
}
