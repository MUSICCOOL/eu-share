<?php

namespace admin\controller;

use admin\model\ConstantModel;
use admin\model\ProjectModel;
use admin\model\TypeModel;
use lib\Page;

class ProjectController extends BaseController
{
    public function index()
    {
        $page  = empty($this->params['page']) ? 1 : $this->params['page'];
        $count = empty($this->params['count']) ? ConstantModel::DEFAULR_PER_PAGE_COUNT : $this->params['count'];
        $total = ProjectModel::count();

        $page = new Page($total, $page, $count);

        $projects = ProjectModel::orderBy('created_at', 'desc');
        $projects = $projects->skip($page->limit['start'])->take($page->limit['count'])->get();

        $types = new TypeModel();
        foreach ($projects as &$value) {
            $value['status'] = ProjectModel::$pro_status[$value['status']];
            $value['type']   = $types->find($value['type'])->name;
        }

        // 渲染模板
        $this->renderView('project/list', ['projects' => $projects, 'paginator' => $page->fpage()]);
    }

    public function update()
    {
        $project             = ProjectModel::find($this->params['pro_id']);
        $project['img_urls'] = implode(',', json_decode($project['imgs'], true));
        $project['imgs']     = $this->getShowUrls($project['imgs']);
        $project['desc']     = str_replace('\"', '', $project['desc']);

        $types = TypeModel::all();

        $this->renderView('project/update', ['project' => $project, 'types' => $types]);
    }

    public function doUpdate()
    {
        $project = ProjectModel::find($this->params['pro_id']);

        $project->name        = $this->params['name'];
        $project->intro       = $this->params['intro'];
        $project->type        = $this->params['type'];
        $project->e_points    = $this->params['e_points'];
        $project->need_points = $this->params['need_points'];
        $project->desc        = str_replace('\"', '"', $this->params['desc']);
        $project->down        = $this->params['down'];
        $project->like        = $this->params['like'];
        $project->reward      = $this->params['reward'];
        $project->reward_num  = $this->params['reward_num'];
        $project->status      = $this->params['status'];
        $project->remark      = $this->params['remark'];
        if (!$project->save()) {
            $this->alert(ConstantModel::UPDATE_ERROR);
        }
        $this->redirect('project', 'index');
    }

    public function delete()
    {
        $ids     = is_array($this->params['id']) ? $this->params['id'] : explode(',', trim($this->params['id'], ','));
        $deleted = ProjectModel::destroy($ids);
        if ($deleted) {
            $this->renderJson(['code' => ConstantModel::DELETE_SUCCESS]);
        } else {
            $this->renderJson(['code' => ConstantModel::DELETE_ERROR]);
        }
    }
}
