<?php
/*
  Author: Xu Wenmeng
  Author Email: xuwenmeng@hotmail.com
  Description: 视图类
  Version: 1.0
*/

namespace system;

class View
{
    public $view = null;

    public function __construct()
    {
        $loader = new \Twig_Loader_Filesystem(ROOT_PATH . PROJECT . DIR_SIGN . "view" . DIR_SIGN);
        $twig   = new \Twig_Environment($loader, array(//'cache' => ROOT_PATH . PROJECT . DIR_SIGN . "cache",
        ));
        if (!empty($_SESSION['username'])) {
            $twig->addGlobal('USERNAME', $_SESSION['username']);
        }
        $twig->addGlobal('BASE_URL', BASE_URL);

        $this->view = $twig;
    }
}
