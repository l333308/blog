<?php


namespace App\Http\Controllers\Api;




use App\TestingGoods;
use App\TestingOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class TestController extends ApiController
{
    public function createOrderByRedisList(Request $request)
    {
        DB::beginTransaction();
        try{
            $userId = $request->input('user_id');
            $goodsId = $request->input('goods_id');
            $keyPrefix = $request->input('redis_key_prefix');

            $redis = Redis::connection();
            // key逻辑取自Command/TestOversaleOrder
            $redisKey = $keyPrefix ."{$goodsId}";

            $goods = TestingGoods::find($goodsId);
            $result = $redis->lpop($redisKey);
            if ($result){
                // 商品库存
                $goods->num--;
                $goods->version++;
                $goods->save();

                // 订单入库
                $order = new TestingOrder();
                $order->user_id = $userId;
                $order->goods_id = $goodsId;
                $order->goods_num = 1;
                $order->save();

                DB::commit();
                return $this->response->json(['status' => 'success', 'msg' => '下单成功', 'result' => $result]);
            }else{
                DB::rollBack();
                return $this->response->json(['status' => 'error', 'msg' => '下单失败']);
            }
        }catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function createOrderByPessimisticLock(Request $request)
    {
        DB::beginTransaction();
        try{
            $userId = $request->input('user_id');
            $goodsId = $request->input('goods_id');

            // 商品
            $goods = TestingGoods::where('id', $goodsId)->lockForUpdate()->first();
            //print_r($goods);
            if ($goods->num < 1){
                DB::rollBack();
                return $this->response->json(['status' => 'error', 'msg' => '库存不足,下单失败']);
            }
            $tableName = TestingGoods::$fullTableName;
            TestingGoods::whereId($goodsId)->whereVersion($goods->version)->update([
                'num' => $goods->num - 1,
                'version' => $goods->version + 1
            ]);

            // 订单入库
            $order = new TestingOrder();
            $order->user_id = $userId;
            $order->goods_id = $goodsId;
            $order->goods_num = 1;
            $order->save();

            DB::commit();

            return $this->response->json(['status' => 'success', 'msg' => '下单成功']);
        }catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function createOrderOversale(Request $request)
    {
        DB::beginTransaction();
        try{
            $userId = $request->input('user_id');
            $goodsId = $request->input('goods_id');

            // 商品数量--
            $goods = TestingGoods::find($goodsId);
            if ($goods->num < 1){
                DB::rollBack();
                return $this->response->json(['status' => 'error', 'msg' => '库存不足,下单失败']);
                exit();
            }
            $goods->num--;
            $goods->version++;
            $goods->save();

            // 订单入库
            $order = new TestingOrder();
            $order->user_id = $userId;
            $order->goods_id = $goodsId;
            $order->goods_num = 1;
            $order->save();

            DB::commit();

            return $this->response->json(['status' => 'success', 'msg' => '下单成功']);
        }catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}