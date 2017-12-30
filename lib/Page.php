<?php
/*
  Author: Xu Wenmeng
  Author Email: xuwenmeng@hotmail.com
  Description: 分页类
  Version: 1.0
*/

namespace lib;

class Page
{
    private $url_mode = 1; // 0: 原生url  1: phpinfo格式url
    private $total;        //数据表中总记录数
    private $listrows;  //每页显示行数
    private $limit;
    private $uri;        //自动获取url请求地址
    private $pageNum;    //总页数
    private $config   = array('header' => '条记录', 'prev' => '上一页', 'next' => '下一页', 'first' => '首页', 'last' => '尾页');

    const PAGE_ALL_WITH_GO    = 1;
    const PAGE_ALL_WITHOUT_GO = 2;
    const PAGE_WITHOUT_LIST   = 3;

    public function __construct($total, $page = 1, $listrows = 10, $pa = "")
    {
        $this->total    = $total;
        $this->listrows = $listrows;
        $this->uri      = $this->getUri($pa);
        $this->page     = $page;
        $this->pageNum  = ceil($this->total / $this->listrows);
        $this->limit    = $this->setLimit();
    }

    public function setUrlMode($url_mode)
    {
        $this->url_mode = $url_mode;
    }

    private function setLimit()
    {
        return ['start' => ($this->page - 1) * $this->listrows, 'count' => $this->listrows];
    }

    public function getUri($pa)
    {
        $url   = $_SERVER['REQUEST_URI'] . (strpos($_SERVER['REQUEST_URI'], '?') ? '' : "?") . $pa;
        $parse = parse_url($url);  //返回一个数组，数组有元素query和path
        if (isset($parse['query'])) {
            parse_str($parse['query'], $params); //将query中的参数解析到$params，为数组
            unset($params['page']);
            $url = $parse['path'] . '?' . http_build_query($params); //按照指定的参数生成一个请求字符串
        }
        return $url;
    }

    //limit为私有属性，通过这个魔术方法使limit变得可用
    public function __get($args)
    {
        if ($args == "limit") {
            return $this->limit;
        } else {
            return null;
        }
    }

    private function first()
    {
        $html = '';
        if ($this->page == 1) {
            $html .= '';
        } else {
            $html .= "&nbsp;<a href='{$this->uri}&page=1'>{$this->config['first']}</a>&nbsp;";
        }
        return $html;
    }

    private function prev()
    {
        $html = '';
        if ($this->page == 1) {
            $html .= '';
        } else {
            $html .= "&nbsp;<a href='{$this->uri}&page=" . ($this->page - 1) . "'>{$this->config['prev']}</a>&nbsp;";
        }
        return $html;
    }

    private function pagelist()
    {
        $linkPage = '';
        //每边显示inum个页码
        $inum = 3;
        //左边的页码
        for ($i = $inum; $i >= 1; $i--) {
            $page = $this->page - $i;
            if ($page < 1) {
                continue;
            } else {
                $linkPage .= "&nbsp;<a href='{$this->uri}&page={$page}'>{$page}</a>&nbsp;";
            }
        }
        //当前页码
        $linkPage .= "&nbsp;{$this->page}&nbsp;";
        //右边页码
        for ($i = 1; $i <= $inum; $i++) {
            $page = $this->page + $i;
            if ($page > $this->pageNum) {
                break;
            } else {
                $linkPage .= "&nbsp;<a href='{$this->uri}&page={$page}'>{$page}</a>&nbsp;";
            }
        }
        return $linkPage;
    }

    private function next()
    {
        $html = '';
        if ($this->page == $this->pageNum) {
            $html .= '';
        } else {
            $html .= "&nbsp;<a href='{$this->uri}&page=" . ($this->page + 1) . "'>{$this->config['next']}</a>&nbsp;";
        }
        return $html;
    }

    private function last()
    {
        $html = '';
        if ($this->page == $this->pageNum) {
            $html .= '';
        } else {
            $html .= "&nbsp;<a href='{$this->uri}&page={$this->pageNum}'>{$this->config['last']}</a>&nbsp;";
        }
        return $html;
    }

    private function goPage()
    {
        return '&nbsp;&nbsp;
		<input id="in" type="text" onkeydown="javascript:if(event.keyCode==13){
		  var page=(this.value>' . $this->pageNum . ')?' . $this->pageNum . ':this.value;  location=\'' . $this->uri . '&page=\'+page+\'\' }" 
		  value="' . $this->page . '" style="width:25px" >
		<input type="button" value="跳转" onclick="javascript:var	
		page=(document.getElementById(\'in\').value>' . $this->pageNum . ')?' . $this->pageNum . ':document.getElementById(\'in\').value; 
		location=\'' . $this->uri . '&page=\'+page+\'\' ">&nbsp;&nbsp;';
    }

    //*可见的成员方法
    function fpage($show_type = self::PAGE_ALL_WITHOUT_GO)
    {
        if ($this->total <= $this->listrows) {
            return '';
        }

        switch ($show_type) {
            case self::PAGE_ALL_WITH_GO:
                $arr = array(0, 1, 2, 3, 4, 5, 6, 7);
                break;
            case self::PAGE_ALL_WITHOUT_GO:
                $arr = array(0, 1, 2, 3, 4, 5, 6);
                break;
            case self::PAGE_WITHOUT_LIST:
                $arr = array(0, 1, 2, 3, 5, 6);
                break;
            default:
                $arr = array(0, 1, 2, 3, 4, 5, 6, 7);
                break;
        }

        $html[0] = "&nbsp;共有<b>{$this->total}</b>{$this->config['header']}&nbsp;";
        $html[1] = "&nbsp;{$this->page}/{$this->pageNum}页&nbsp;";
        $html[2] = $this->first();
        $html[3] = $this->prev();
        $html[4] = $this->pageList();
        $html[5] = $this->next();
        $html[6] = $this->last();
        $html[7] = $this->goPage();
        $fpage   = '';
        foreach ($arr as $index) {
            $fpage .= $html[$index];
        }

        if ($this->url_mode == 1) {
            $fpage = preg_replace("/\?&page=/i", '/page/', $fpage);
            $fpage = preg_replace("/\/page\/\d+\/page\//i", '/page/', $fpage);
        }

        return $fpage;
    }
}