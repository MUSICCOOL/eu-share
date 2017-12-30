<?php

namespace admin\controller;

use admin\model\ConstantModel;
use admin\model\OrderModel;
use admin\model\UserModel;
use Illuminate\Database\Capsule\Manager as DB;
use lib\Log;
use lib\Page;

class OrderController extends BaseController
{
    public function index()
    {
        $page  = empty($this->params['page']) ? 1 : $this->params['page'];
        $count = empty($this->params['count']) ? ConstantModel::DEFAULR_PER_PAGE_COUNT : $this->params['count'];

        $orders = OrderModel::orderBy('created_at', 'desc');

        $total = $orders->count();

        $page = new Page($total, $page, $count);

        $orders = $orders->skip($page->limit['start'])->take($page->limit['count'])->get();
        foreach ($orders as &$value) {
            $value['type']   = OrderModel::$order_type[$value['type']];
            $value['status'] = OrderModel::$order_status[$value['status']];
        }

        // 渲染模板
        $this->renderView('order/list', ['orders' => $orders, 'paginator' => $page->fpage()]);
    }

    public function update()
    {
        $order        = OrderModel::find($this->params['id']);
        $order_types  = OrderModel::$order_type;
        $order_status = OrderModel::$order_status;
        $this->renderView('order/update',
            ['order' => $order, 'order_types' => $order_types, 'order_status' => $order_status]);
    }

    public function doUpdate()
    {
        $order            = OrderModel::find($this->params['id']);
        $order->order_num = $this->params['order_num'];
        $order->amount    = $this->params['amount'];
        $order->type      = $this->params['type'];
        $order->status    = $this->params['status'];
        $order->remark    = $this->params['remark'];

        Log::info('order do update', [$order->save()]);

        // 开启事务
        DB::beginTransaction();

        try {
            if (!$order->save()) {
                DB::rollBack();
                Log::error('order do update order save error', $this->params);
                $this->alert(ConstantModel::UPDATE_ERROR);
            }
            $user = UserModel::find($this->params['user_id']);
            if (in_array($this->params['type'], OrderModel::$order_type_vip)) {
                if ($this->params['status'] == OrderModel::ORDER_STATUS_SUC) {
                    $user->vip         = UserModel::IS_VIP;
                    $user->vip_exptime = $this->params['type'] * (24 * 3600) + time();
                } else {
                    $user->vip         = UserModel::IS_NOT_VIP;
                    $user->vip_exptime = 0;
                }
                if (!$user->save()) {
                    DB::rollBack();
                    Log::error('order do update user save error', $this->params);
                    $this->alert(ConstantModel::UPDATE_ERROR);
                }
            } elseif (in_array($this->params['type'], OrderModel::$order_type_rec)) {
                if ($this->params['status'] == OrderModel::ORDER_STATUS_SUC) {
                    $user->e_points += $this->params['amount'] * 10;
                    if (!$user->save()) {
                        DB::rollBack();
                        Log::error('order do update user save error', $this->params);
                        $this->alert(ConstantModel::UPDATE_ERROR);
                    }
                }
            } else {
                DB::rollBack();
                Log::error('order do update param error', $this->params);
                $this->alert(ConstantModel::PARAM_ERROR);
            }

            DB::commit();

            Log::info('order do update success', $this->params);

            $this->redirect('order', 'index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('order do update error', $e);
            $this->alert(ConstantModel::UPDATE_ERROR);
        }

    }

    public function delete()
    {
        $ids     = is_array($this->params['id']) ? $this->params['id'] : explode(',', trim($this->params['id'], ','));
        $deleted = OrderModel::destroy($ids);
        if ($deleted) {
            $this->renderJson(['code' => ConstantModel::DELETE_SUCCESS]);
        } else {
            $this->renderJson(['code' => ConstantModel::DELETE_ERROR]);
        }
    }
}
