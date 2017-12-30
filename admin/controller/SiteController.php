<?php

namespace admin\controller;

use admin\model\ConstantModel;
use admin\model\MapModel;

class SiteController extends BaseController
{
    public function index()
    {
        $model    = new MapModel();
        $siteKeys = $model->whereIn('key_name', MapModel::$siteKey)->get();
        // 渲染模板
        $this->renderView('site/list', ['siteKeys' => $siteKeys]);
    }

    public function update()
    {
        $model    = new MapModel();
        $siteKeys = $model->whereIn('key_name', MapModel::$siteKey)->get();
        // 渲染模板
        $this->renderView('/site/update', ['siteKeys' => $siteKeys]);
    }

    public function doUpdate()
    {
        $model = new MapModel();
        try {
            foreach (MapModel::$siteKey as $key) {
                $model->where('key_name', $key)->update(['key_value' => $this->params[$key]]);
            }
        } catch (\Exception $e) {
            $this->renderJson(['code' => ConstantModel::UPDATE_ERROR, 'data' => $e]);
        }
        $this->redirect('site', 'index');
    }

    public function delete()
    {
        $ids     = is_array($this->params['id']) ? $this->params['id'] : explode(',', trim($this->params['id'], ','));
        $deleted = MapModel::destroy($ids);
        if ($deleted) {
            echo json_encode([]);
        }
    }
}
