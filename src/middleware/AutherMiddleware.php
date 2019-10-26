<?php
namespace cncoders\auth\middleware;


use cncoders\auth\Auther;
use think\facade\Config;

class AutherMiddleware
{
    /**
     * @param $request
     * @param \Closure $next
     */
    public function handle($request, \Closure $next)
    {
        Auther::make()->verfiyToken();

        bind('auther', function(){
            return Auther::make()->all()->data;
        });

        bind('aheader', function(){
            return Auther::make()->header();
        });

        return $next($request);
    }
}