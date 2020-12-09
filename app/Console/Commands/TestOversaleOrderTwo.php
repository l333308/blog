<?php


namespace App\Console\Commands;


use App\Http\Controllers\Api\TestController;
use App\Support\RequestUtil;
use App\TestingGoods;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class TestOversaleOrderTwo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:oversaleOrder2';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an oversale order 2.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *  不经nginx 直接调php接口
     *
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $redis = Redis::connection();

        $goodsList = TestingGoods::all();
        foreach($goodsList as $goods) {
            // 商品库存信息入redis
            $redisKey = "2_testing_goods_{$goods->id}";
            for($i = 0; $i < $goods->num; $i++){
                $result = $redis->lpush($redisKey, 1);
                echo $result;
                echo PHP_EOL;
            }
        }

        $redisKeyPrefix = '2_testing_goods_';
        $orderCreatingRequest = new Request();
        $orderCreatingRequest->query->set('redis_key_prefix', $redisKeyPrefix);
        foreach($goodsList as $goods) {
            // 模拟高并发抢购
            $orderCreatingRequest->query->set('goods_id', $goods->id);
            foreach(range(1, 4000) as $userId) {
                $orderCreatingRequest->query->set('user_id', $userId);
                $result = app(TestController::class)->oversaleCreateOrder($orderCreatingRequest);

                print_r($result->original);
                echo PHP_EOL;
            }
        }

    }
}