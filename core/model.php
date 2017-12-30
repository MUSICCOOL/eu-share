<?php
/*
  Author: Xu Wenmeng
  Author Email: xuwenmeng@hotmail.com
  Description: 模型类
  Version: 1.0
*/

namespace system;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

//创建model类继承，并创建连接
class Model extends \Illuminate\Database\Eloquent\Model
{
    public function __construct()
    {
        $capsule = new Capsule;
        $capsule->addConnection(\Init::getConfig("db"));
        $capsule->setEventDispatcher(new Dispatcher(new Container));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}
