<?php

namespace admin\controller;

class IndexController extends BaseController
{
    public function index()
    {
        // 渲染模板
        $this->renderView('index');
    }
}
