<?php
namespace Ordent\CacheURLRedis\Http;

use Illuminate\Support\Facades\Cache;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Contracts\Routing\ResponseFactory as FactoryContract;
class Response extends ResponseFactory implements FactoryContract{
  protected function morphToJson($content)
    {
      if(array_key_exists('x-cache-url', request()->header())){
        $key = request()->fullUrl();
          if(request()->header()['x-cache-url'][0] === 'with-auth'){
            if(!empty(request()->header()['authorization'][0])){
              $key = $key.':'.str_replace('Bearer ', '', request()->header()['authorization'][0]);
            }
        }
        if(array_key_exists('x-cache-url-invalidate', request()->header())){
          Cache::tags(['url'])->forget($key);
        }
        if(empty(Cache::tags(['url'])->get($key))){
          if ($content instanceof Jsonable) {
            Cache::tags(['url'])->put($key, json_encode($content->toJson()), 3600);
          } elseif ($content instanceof Arrayable) {
            Cache::tags(['url'])->put($key, json_encode($content->toArray()), 3600);
          }
        }
      }
      if ($content instanceof Jsonable) {
        return $content->toJson();
      } elseif ($content instanceof Arrayable) {
        return json_encode($content->toArray());
      }
      return json_encode($content);
    }
}