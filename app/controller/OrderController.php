<?php

namespace app\controller;

use app\model\ConstantModel;
use app\model\OrderModel;
use app\model\UserModel;

class OrderController extends BaseController
{
    public function createOrder()
    {
        $user = new UserModel();
        $r_user = $user->find($this->user->user_id);

        $order_type = '';
        if (in_array($this->params['type'], OrderModel::$order_type_vip)) {
            $order_type = 'upvip';
        }elseif (in_array($this->params['type'], OrderModel::$order_type_rec)) {
            $order_type = 'recharge';
        }
        if ($order_type == 'upvip') {
            if ($this->checkVip()) {
                $this->renderJson(['code' => ConstantModel::ALREADY_VIP]);
            }
            $r_user->vip         = UserModel::IS_VIP;
            $r_user->vip_exptime = $this->params['type'] * (24 * 3600) + time();
            if (!$r_user->save()) {
                Log::error('upvip error', $this->params);
                $this->renderJson(['code' => ConstantModel::UPVIP_ERROR]);
            }
        }elseif ($order_type == 'recharge') {
            $r_user->e_points += $this->params['amount'] * 10;
            if (!$r_user->save()) {
                Log::error('recharge error', $this->params);
                $this->renderJson(['code' => ConstantModel::RECHARGE_FAIL]);
            }
        }

        $order = new OrderModel();

        $record = $order->where('user_id', $this->user->user_id)->where('status', OrderModel::ORDER_STATUS_NO)->first();
        if (!empty($record)) {
            $this->renderJson(['code' => ConstantModel::ALREADY_ORDER_WAITING]);
        }

        $order->user_id   = $this->user->user_id;
        $order->type      = $this->params['type'];
        $order->order_num = $this->params['order_num'];
        $order->amount    = $this->params['amount'];
        $order->ip        = $this->getIP();
        if ($order_type == 'upvip') {
            if ($order->save()) {
                $this->renderJson(['code' => ConstantModel::UPVIP_SUCCESS]);
            } else {
                $this->renderJson(['code' => ConstantModel::UPVIP_ERROR]);
            }
        } else {
            if ($order->save()) {
                $this->renderJson(['code' => ConstantModel::RECHARGE_SUCCESS]);
            } else {
                $this->renderJson(['code' => ConstantModel::REWARD_ERROR]);
            }
        }

    }

    public function checkVip()
    {
        if (!empty($this->user->vip_exptime) && $this->user->vip_exptime > time()) {
            return true;
        } else {
            return false;
        }
    }
}
