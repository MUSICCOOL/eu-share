<?php

namespace app\controller;


class IndexController extends BaseController
{
    public function index()
    {
        $this->renderView('index');
    }
}
