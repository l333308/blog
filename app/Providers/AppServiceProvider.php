<?php

namespace App\Providers;

use App\Article;
use App\Discussion;
use App\Tools\FileManager\BaseManager;
use App\Tools\FileManager\UpyunManager;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $lang = config('app.locale') != 'zh_cn' ? config('app.locale') : 'zh';
        \Carbon\Carbon::setLocale($lang);

        Relation::morphMap([
            'discussions' => Discussion::class,
            'articles'    => Article::class,
        ]);

        Schema::defaultStringLength(191);

        DB::listen(function (QueryExecuted $query) {
            if ($query->time < 100) {
//                return;
            }
            if (app()->environment('production')) {
                if ($query->time >= 1000) {
                    // TODO:print full sql
                    Log::warning("SlowSQL:", [$query->sql, $query->bindings, $query->time]);
                }
            } else {
                if ($query->time >= 1000) {
                    // TODO:print full sql
                    Log::warning("SlowSQL:", [$query->sql, $query->bindings, $query->time]);
                } else {
                    Log::info("SQL:", [$query->sql, $query->bindings, $query->time]);
                }
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('uploader', function ($app) {
            $config = config('filesystems.default', 'public');

            if ($config == 'upyun') {
                return new UpyunManager();
            }

            return new BaseManager();
        });
    }
}
