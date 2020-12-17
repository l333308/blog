<?php


namespace App\Console\Commands;


use App\Http\Controllers\Api\TestController;
use App\Support\RequestUtil;
use App\TestingGoods;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class TestOversaleOrder2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:oversaleOrder2
                            {userCount? : count of users}';

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
     *  经nginx
     *
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $userCount = $this->argument('userCount') ? :4000;

        try{
            $goodsList = TestingGoods::all();
            $orderCreatingRequest = new Request();
            $redisKeyPrefix = 'testing_goods_';
            $orderCreatingRequest->query->set('redis_key_prefix', $redisKeyPrefix);

            $userList = range(1, $userCount);
            foreach($goodsList as $goods) {
                // 模拟高并发抢购
                $domain = env('APP_URL') .'api/test/create_order_by_redis_list';
                foreach($userList as $userId) {
                    $result = RequestUtil::sendRequest($domain, 'post', ['user_id' => $userId, 'goods_id' => $goods->id]);

                    print_r($result);
                    echo PHP_EOL;
                }
            }
        }catch (\Exception $e) {
            throw $e;
        }

    }
}