<?php

namespace app\controller;

use app\model\ConstantModel;
use app\model\OrderModel;

class OrderController extends BaseController
{
    public function createOrder()
    {

        if (in_array($this->params['type'], OrderModel::$order_type_vip)) {
            if ($this->checkVip()) {
                $this->renderJson(['code' => ConstantModel::ALREADY_VIP]);
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
        if ($order->save()) {
            $this->renderJson(['code' => ConstantModel::CREATE_ORDER_SUCCESS]);
        } else {
            $this->renderJson(['code' => ConstantModel::CREATE_ORDER_ERROR]);
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
