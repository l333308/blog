<?php


namespace App\Console\Commands;


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
    protected $signature = 'test:oversaleOrder';

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
            $redisKey = "testing_goods_{$goods->id}";
            for($i = 0; $i < $goods->num; $i++){
                $result = $redis->lpush($redisKey, 1);
                echo $result;
                echo PHP_EOL;
            }
        }

        // 本地、腾讯云线上 每秒最多生成35条数据 然后就报502 因此此处写每秒插入30条
        $round = 0;
        $userPerRound = 30;
        while ($round < 80){
            foreach($goodsList as $goods) {
                // 模拟高并发抢购
                $domain = env('APP_URL') .'api/test/oversale';
                $userStart = $round * $userPerRound + 1;
                $userEnd = $userStart + $userPerRound;
                foreach(range($userStart, $userEnd) as $userId) {
                    $result = RequestUtil::sendRequest($domain, 'post', ['user_id' => $userId, 'goods_id' => $goods->id]);

                    print_r($result);
                    echo PHP_EOL;
                }
            }
            $round++;
            sleep(1);
        }
    }
}