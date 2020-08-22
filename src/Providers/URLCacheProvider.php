<?php

namespace Ordent\CacheURLRedis\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Routing\ResponseFactory;
use Ordent\CacheURLRedis\Http\Response;
class URLCacheProvider extends ServiceProvider
{
  public function register(){
    // dd(app(ResponseFactory::class));
    $this->app->bind(ResponseFactory::class, Response::class);
  }
}