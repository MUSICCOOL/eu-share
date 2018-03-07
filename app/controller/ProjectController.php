<?php

namespace app\controller;

use app\model\ConstantModel;
use app\model\ConsumerModel;
use app\model\DownModel;
use app\model\IncomeModel;
use app\model\LikeModel;
use app\model\ProjectModel;
use app\model\RewardModel;
use app\model\TypeModel;
use app\model\UserModel;
use Illuminate\Database\Capsule\Manager as DB;
use lib\Log;
use lib\Page;

class ProjectController extends BaseController
{
    public function lists()
    {
        $page  = empty($this->params['page']) ? 1 : $this->params['page'];
        $count = empty($this->params['count']) ? ConstantModel::DEFAULR_PER_PAGE_COUNT : $this->params['count'];

        $projects = ProjectModel::where('status', ProjectModel::PRO_STATUS_SUCCESS)->orderBy('created_at', 'desc');

        $total = $projects->count();
        $page  = new Page($total, $page, $count);

        $projects = $projects->skip($page->limit['start'])->take($page->limit['count'])->get();

        $user_ids = [];

        $type = new TypeModel();

        foreach ($projects as &$value) {
            $value['status'] = ProjectModel::$pro_status[$value['status']];
            $value['imgs']   = json_decode($value['imgs'], true);
            $value['type']   = $type->find($value['type'])->name;
            $user_ids[]      = $value['user_id'];
        }
        $users   = UserModel::whereIn('user_id', $user_ids)->get();
        $p_users = [];
        foreach ($users as $user) {
            $p_users[$user->user_id] = $user;
        }

        foreach ($projects as &$value) {
            $value['user'] = $p_users[$value['user_id']];
        }

        $types = $type->orderBy('order', 'asc')->orderBy('created_at', 'desc')->get();

        // 渲染模板
        $this->renderView('project/list', ['projects' => $projects, 'types' => $types, 'paginator' => $page->fpage()]);
    }

    public function add()
    {
        $types = TypeModel::orderBy('order', 'asc')->orderBy('created_at', 'desc')->get();
        $allowTypes = \Init::getConfig('upload_type')['files'];
        $this->renderView('project/add', ['types' => $types, 'allowtypes' => $allowTypes]);
    }

    public function doAdd()
    {
        $img_urls = trim($this->params['imgs_urls'], ',');

        $project              = new ProjectModel();

        if (!empty($img_urls)) {
            $img_urls = explode(',', $img_urls);
            $project->imgs        = empty($img_urls) ? '' : json_encode($img_urls);
        }

        $project->name        = $this->params['name'];
        $project->user_id     = $this->user->user_id;
        $project->intro       = $this->params['intro'];
        $project->type        = $this->params['type'];
        $project->need_points = $this->params['need_points'];
        $project->desc        = str_replace('\"', '"', $this->params['desc']);
        $project->file        = $this->params['file_url'];

        if (!$project->save()) {
            $this->renderJson(['code' => ConstantModel::PRO_ADD_ERROR]);
        } else {
            $this->renderJson();
        }
    }

    public function doLike()
    {
        $pro_id = $this->params['pro_id'];
        $like   = new LikeModel();

        $is_already = $like->where('pro_id', $pro_id)->where('user_id', $this->user->user_id)->first();

        if (!empty($is_already)) {
            $this->renderJson(['code' => ConstantModel::POINT_LIKE_ALREADY]);
        }

        $like->pro_id  = $pro_id;
        $like->user_id = $this->user->user_id;

        $project = ProjectModel::find($pro_id);
        $project->like++;

        if ($like->save() && $project->save()) {
            $this->renderJson();
        } else {
            $this->renderJson(['code' => ConstantModel::POINT_LIKE_ERROR]);
        }
    }

    public function update()
    {
        $project             = ProjectModel::find($this->params['pro_id']);
        $project['img_urls'] = implode(',', json_decode($project['imgs'], true));
        $project['imgs']     = $this->getShowUrls($project['imgs'], true);
        $project['desc']     = str_replace('\"', '', $project['desc']);

        $types = TypeModel::orderBy('order', 'asc')->orderBy('created_at', 'desc')->get();
        $this->renderView('project/update', ['project' => $project, 'types' => $types]);
    }

    public function doUpdate()
    {
        $img_urls = trim($this->params['imgs_urls'], ',');
        $project = ProjectModel::find($this->params['pro_id']);

        if (!empty($img_urls)) {
            $img_urls = explode(',', $img_urls);
            $num      = count($img_urls);
            if ($num > ProjectModel::PRO_IMG_LIMIT) {
                $img_urls = array_slice($img_urls, $num - ProjectModel::PRO_IMG_LIMIT, ProjectModel::PRO_IMG_LIMIT);
            }
            $project->imgs        = empty($img_urls) ? '' : json_encode($img_urls);
        }

        $project->name        = $this->params['name'];
        $project->intro       = $this->params['intro'];
        $project->type        = $this->params['type'];
        $project->need_points = $this->params['need_points'];
        $project->desc        = str_replace('\"', '"', $this->params['desc']);
        $project->file        = $this->params['file_url'];
        $project->status      = ProjectModel::PRO_STATUS_NONE;
        if (!$project->save()) {
            $this->renderJson(ConstantModel::PRO_UPDATE_ERROR);
        }
        $this->renderJson();
    }

    public function delete()
    {
        $ids     = is_array($this->params['id']) ? $this->params['id'] : [$this->params['id']];
        $deleted = ProjectModel::destroy($ids);
        if ($deleted) {
            $this->renderJson(['code' => ConstantModel::DELETE_SUCCESS]);
        } else {
            $this->renderJson(['code' => ConstantModel::DELETE_ERROR]);
        }
    }

    public function fileUpload()
    {
        $file = $_FILES["pro_file"];

        $allowTypes = [
            'text/css',
            'text/xml',
            'text/html',
            'text/plain',
            'text/javascript',
            'application/xml',
            'application/zip',
            'application/xhtml+xml',
        ];

        $path = ConstantModel::FILE_UPLOAD_PATH;

        $this->fileUp($file, $allowTypes, $path);
    }

    public function imgsUpload()
    {
        $imgData = $this->params['imgs_data'];
        // base 图片上传
        $img_urls = $this->upBase64Image($imgData);
        $this->renderJson(['img_urls' => $img_urls]);
    }

    public function show()
    {
        $project             = ProjectModel::find($this->params['pro_id']);
        $project['img_urls'] = implode(',', json_decode($project['imgs'], true));
        $project['imgs']     = $this->getShowUrls($project['imgs'], true);
        $project['desc']     = str_replace('\"', '', $project->desc);
        $project['type']     = TypeModel::find($project->type)->name;
        $project['user']     = UserModel::find($project->user_id);

        $this->renderView('project/show', ['project' => $project]);
    }

    public function download()
    {
        $project = ProjectModel::find($this->params['pro_id']);
        if ($this->user->e_points >= $project->need_points) {
            $project['user'] = UserModel::find($project->user_id);
            $this->renderView('project/download', ['project' => $project]);
        } else {
            $this->alert(['code' => ConstantModel::E_POINTS_NOT_ENOUGH]);
        }
    }

    public function doDown()
    {
        $pro_id  = $this->params['pro_id'];
        $user_id = $this->user->user_id;

        $project = ProjectModel::find($pro_id);
        $down    = new DownModel();

        $file_url = $this->getShowUrls([$project->file]);

        // 检测是否已经下载过该项目，如已下载，无需再次付费和下载计数
        $down_records = $down->where("pro_id", $pro_id)->where("user_id", $user_id)->first();

        if (!empty($down_records)) {
            $this->renderJson(['file' => $file_url]);
        }

        // 下载自己发布的项目无需付费且不添加相关记录
        if ($this->user->user_id == $project->user_id) {
            $this->renderJson(['file' => $file_url]);
        }

        $context = [
            'user_id' => $this->user->user_id,
            'pro_id'  => $pro_id,
        ];

        Log::info('down project start', $context);
        // 开启事务
        DB::beginTransaction();

        try {
            $project  = ProjectModel::find($pro_id);
            $user     = new UserModel();
            $down     = new DownModel();
            $income   = new IncomeModel();
            $consumer = new ConsumerModel();

            // 更新项目下载次数以获取e点币数
            $project->down = $project->down + 1;
            if (!$project->save()) {
                DB::rollBack();
                Log::error('project save error', $context);
                $this->renderJson(['code' => ConstantModel::DOWN_ERROR]);
            }

            // 更新项目下载记录表
            $down->pro_id  = $pro_id;
            $down->user_id = $user_id;
            $down->ip      = $this->getIP();
            if (!$down->save()) {
                DB::rollBack();
                Log::error('add down record error', $context);
                $this->renderJson(['code' => ConstantModel::DOWN_ERROR]);
            }

            // 下载人账户余额消费
            $down_user           = $user->find($user_id);
            $down_user->e_points = $down_user->e_points - $project->need_points;
            if (!$down_user->save()) {
                DB::rollBack();
                Log::error('down user save error', $context);
                $this->renderJson(['code' => ConstantModel::DOWN_ERROR]);
            }

            // 增加下载人消费记录
            $consumer->user_id  = $user_id;
            $consumer->pro_id   = $pro_id;
            $consumer->e_points = $project->need_points;
            $consumer->type     = ConsumerModel::CONSUMER_TYPE_DOEN;
            if (!$consumer->save()) {
                DB::rollBack();
                Log::error('add user consume record error', $context);
                $this->renderJson(['code' => ConstantModel::DOWN_ERROR]);
            }

            // 项目发布人余额收益
            $pro_user           = $user->find($project->user_id);
            $pro_user->e_points = $pro_user->e_points + $project->need_points;
            if (!$pro_user->save()) {
                DB::rollBack();
                Log::error('project user save error', $context);
                $this->renderJson(['code' => ConstantModel::DOWN_ERROR]);
            }

            // 增加发布人收益记录
            $income->user_id  = $project->user_id;
            $income->pro_id   = $pro_id;
            $income->u_id     = $user_id;
            $income->e_points = $project->need_points;
            $income->type     = IncomeModel::INCOME_TYPE_DOEN;
            if (!$income->save()) {
                DB::rollBack();
                Log::error('add user income record error', $context);
                $this->renderJson(['code' => ConstantModel::DOWN_ERROR]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('down project error', $context);
            $this->renderJson(['code' => ConstantModel::DOWN_ERROR]);
        }

        DB::commit();

        Log::info('down project success', $context);

        $this->renderJson(['file' => $file_url]);
    }

    public function doReward()
    {
        $pro_id   = $this->params['pro_id'];
        $u_id     = $this->params['u_id'];
        $e_points = $this->params['e_points'];

        if (empty($pro_id) || empty($u_id) || empty($e_points)) {
            Log::error('reward param error', $this->params);
            $this->renderJson(['code' => ConstantModel::PARAM_ERROR]);
        }

        if ($this->user->user_id == $u_id) {
            Log::error('can not reward self', ['u_id' => $u_id, 'user_id' => $this->user->user_id]);
            $this->renderJson(['code' => ConstantModel::REWARD_SELF_ERROR]);
        }

        if ($this->user->e_points < $e_points) {
            $this->renderJson(['code' => ConstantModel::E_POINTS_NOT_ENOUGH]);
        }

        $context = [
            'user_id'  => $this->user->user_id,
            'pro_id'   => $pro_id,
            'u_id'     => $u_id,
            'e_points' => $e_points,
        ];

        Log::info('reward start', $context);

        // 开启事务
        DB::beginTransaction();

        try {
            $user = new UserModel();

            // 打赏人账户余额减少
            $r_user           = $user->find($this->user->user_id);
            $r_user->e_points -= $e_points;
            if (!$r_user->save()) {
                DB::rollBack();
                Log::error('reward user save error', $context);
                $this->renderJson(['code' => ConstantModel::REWARD_ERROR]);
            }
            // 被打赏项目总e点币增加,被打赏总金额增加
            $project             = ProjectModel::find($pro_id);
            $project->e_points   += $e_points;
            $project->reward     += 1;
            $project->reward_num += $e_points;
            if (!$project->save()) {
                DB::rollBack();
                Log::error('be rewarded project save error', $context);
                $this->renderJson(['code' => ConstantModel::REWARD_ERROR]);
            }
            // 被打赏人余额增加
            $br_user           = $user->find($u_id);
            $br_user->e_points += $e_points;
            if (!$br_user->save()) {
                DB::rollBack();
                Log::error('be rewarded user save error', $context);
                $this->renderJson(['code' => ConstantModel::REWARD_ERROR]);
            }
            // 增加打赏记录
            $reward             = new RewardModel();
            $reward->pro_id     = $pro_id;
            $reward->user_id    = $this->user->user_id;
            $reward->reward_num = $e_points;
            if (!$reward->save()) {
                DB::rollBack();
                Log::error('add reward record error', $context);
                $this->renderJson(['code' => ConstantModel::REWARD_ERROR]);
            }

            // 增加消费记录
            $consumer           = new ConsumerModel();
            $consumer->user_id  = $this->user->user_id;
            $consumer->pro_id   = $pro_id;
            $consumer->e_points = $e_points;
            $consumer->type     = ConsumerModel::CONSUMER_TYPE_REWARD;
            if (!$consumer->save()) {
                DB::rollBack();
                Log::error('add user consume record error', $context);
                $this->renderJson(['code' => ConstantModel::REWARD_ERROR]);
            }

            // 增加收益记录
            $income           = new IncomeModel();
            $income->user_id  = $this->user->user_id;
            $income->pro_id   = $pro_id;
            $income->u_id     = $u_id;
            $income->e_points = $e_points;
            $income->type     = IncomeModel::INCOME_TYPE_REWARD;
            if (!$income->save()) {
                DB::rollBack();
                Log::error('add user income record error', $context);
                $this->renderJson(['code' => ConstantModel::REWARD_ERROR]);
            }

            DB::commit();

            Log::info('reward success', $context);

            $this->renderJson(['code' => ConstantModel::REWARD_SUCCESS]);
        } catch (\Exception $e) {
            DB::rollBack();
            $context['e'] = $e;
            Log::error('reward error', $context);
            $this->renderJson(['code' => ConstantModel::REWARD_ERROR]);
        }
    }


    public function search()
    {
        $page  = empty($this->params['page']) ? 1 : $this->params['page'];
        $count = empty($this->params['count']) ? ConstantModel::DEFAULR_PER_PAGE_COUNT : $this->params['count'];

        $projects = ProjectModel::where('status', ProjectModel::PRO_STATUS_SUCCESS)->where('name', 'like',
            '%' . $this->params['search-text'] . '%')->orderBy('created_at', 'desc');

        $total    = $projects->count();
        $page     = new Page($total, $page, $count);
        $projects = $projects->skip($page->limit['start'])->take($page->limit['count'])->get();

        $user_ids = [];

        $type = new TypeModel();

        foreach ($projects as &$value) {
            $value['status'] = ProjectModel::$pro_status[$value['status']];
            $value['imgs']   = json_decode($value['imgs']);
            $value['type']   = $type->find($value['type'])->name;
            $user_ids[]      = $value['user_id'];
        }
        $users   = UserModel::whereIn('user_id', $user_ids)->get();
        $p_users = [];
        foreach ($users as $user) {
            $p_users[$user->user_id] = $user;
        }

        foreach ($projects as &$value) {
            $value['user'] = $p_users[$value['user_id']];
        }

        $types = $type->orderBy('order', 'asc')->orderBy('created_at', 'desc')->get();

        // 渲染模板
        $this->renderView('project/list', ['projects' => $projects, 'types' => $types, 'paginator' => $page->fpage()]);
    }
}
