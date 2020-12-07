<?php


namespace App\Http\Controllers\Api;




use App\TestingOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class TestController extends ApiController
{
    public function oversaleCreateOrder(Request $request)
    {
        try{
            $userId = $request->input('user_id');
            $goodsId = $request->input('goods_id');
            $keyPrefix = $request->input('redis_key_prefix');

            $redis = Redis::connection();
            // key逻辑取自Command/TestOversaleOrder
            $redisKey = $keyPrefix ."{$goodsId}";

            $result = $redis->lpop($redisKey);
            if ($result){
                // 订单入库
                $order = new TestingOrder();
                $order->user_id = $userId;
                $order->goods_id = $goodsId;
                $order->goods_num = 1;
                $order->save();

                return $this->response->json(['status' => 'success', 'msg' => '下单成功']);
            }else{
                return $this->response->json(['status' => 'error', 'msg' => '下单失败']);

            }
        }catch (\Exception $e) {
            throw $e;
        }
    }
}