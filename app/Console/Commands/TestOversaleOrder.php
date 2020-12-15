<?php


namespace App\Console\Commands;


use App\Http\Controllers\Api\TestController;
use App\Support\RequestUtil;
use App\TestingGoods;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class TestOversaleOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:oversaleOrder
                            {type? : Type of creating order}
                            {redisPushList? : redis list action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an oversale order.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *  不经nginx 直接调php接口
     * type 1:redis队列 2:mysql悲观锁 3:不作处理
     *
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $type = $this->argument('type') ? :1;
        $redisPushList = $this->argument('redisPushList');
        switch ($type){
            case 1:
                $method = 'createOrderByRedisList';
                break;
            case 2:
                $method = 'createOrderByPessimisticLock';
                break;
            case 3:
                $method = 'createOrderOversale';
                break;
            default:
        }

        $goodsList = TestingGoods::all();
        $orderCreatingRequest = new Request();
        $redisKeyPrefix = 'testing_goods_';
        $orderCreatingRequest->query->set('redis_key_prefix', $redisKeyPrefix);

        if ($type == 1 && $redisPushList){
            $redis = Redis::connection();

            foreach($goodsList as $goods) {
                // 商品库存信息入redis
                $redisKey = "testing_goods_{$goods->id}";
                for($i = 0; $i < $goods->num; $i++){
                    $result = $redis->lpush($redisKey, 1);
                    echo $result;
                    echo PHP_EOL;
                }
            }
        }

        foreach($goodsList as $goods) {
            // 模拟高并发抢购
            $orderCreatingRequest->query->set('goods_id', $goods->id);
            foreach(range(1, 120000) as $userId) {
                $orderCreatingRequest->query->set('user_id', $userId);
                $result = app(TestController::class)->$method($orderCreatingRequest);

                print_r($result->original);
                echo PHP_EOL;
            }
        }

    }
}