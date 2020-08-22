# ORDENT / CACHE-URL-REDIS

AIO cache management service based on URL and Auth JWT Implementation.

## How To Use

1. Install the package via composer

```php
composer require ordent/cache-url-redis
```

2. Implement the providers in your app configurations at `config/app.php`

```php
'providers' => [
  Ordent\CacheURLRedis\Providers\URLCacheProvider::class
]
```

3. Add a constant in the start of your `bootstart/app.php` to measure the execution time of your api.

```php
define('EXEC_TIME_START', microtime(true));
```

4. Add our middleware in `App\Http\Kernel` to short circuit the computation process to redis when the URL key is found.

```php
protected $middleware = [
  Ordent\CacheURLRedis\Middleware\URLCacheMiddleware::class
]
```

5. Don't forget to set up your `CACHE` env implementation.

```php
  CACHE_DRIVER=redis
```

## Output

1. We measure and deliver the endpoint execution time via response header `X-Elapsed-Time`.
2. You need to send a Header in order to use the caching mechanism. If the header is not found on the request, the request will be computed normally. The header you need to set is `X-Cache-URL` with either value of `with-auth` or `without-auth`.
3. When the value `without-auth` is being used, the application will short circuit the computation process to Redis with finding the key of request URL.
4. However if the value `with-auth` is used, the application will use the Redis with key format : `{application-url}:{authorization-header-value}` with `authorization-header-value` is your JWT token with Bearer format removed.
5. As of now the cache will last 60 minutes and won't cache another value unless you use header `X-Cache-URL-Invalidate`, you can use it to invalidate the cache value after transaction or any other database change.
