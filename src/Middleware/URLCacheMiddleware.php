<?php

namespace Ordent\CacheURLRedis\Middleware;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Response;
// use Illuminate\Support\Facades\Auth;
use Closure;

class URLCacheMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        
        // if header have url cache header then process, if not then skip
        if(array_key_exists('x-cache-url', request()->header()) && !array_key_exists('x-cache-url-invalidate', request()->header())){
          $key = request()->fullUrl();
          if(request()->header()['x-cache-url'][0] === 'with-auth'){
            if(!empty(request()->header()['authorization'][0])){
              $key = $key.':'.str_replace('Bearer ', '', request()->header()['authorization'][0]);
            }
          }
          $value = Cache::tags(['url'])->get($key);
          if(!empty($value)){
            $response = response()->json(json_decode($value));
            $response->headers->set('X-Cache-URL', request()->header()['x-cache-url'][0]);
          }else{
            $response = $next($request);
          }
        }else{
          $response = $next($request);
        }
        if(defined('EXEC_TIME_START')){
          // Calculate execution time
          $executionTime = microtime(true) - EXEC_TIME_START;
          $response->headers->set('X-Elapsed-Time', $executionTime);
          // I assume you're using valid json in your responses
          // Then I manipulate them below
        }

         // Change the content of your response
        return $response;
    }
}
