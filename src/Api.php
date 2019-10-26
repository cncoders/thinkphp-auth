<?php
namespace cncoders\auth;

use think\facade\Route;

/**
 * 用于处理API的版本
 *
 * Class Api
 * @package cncoders\auth
 */
class Api
{
    /**
     * @var string 记录当前接口访问的版本
     */
    protected $version = '';

    /**
     * @var null
     */
    protected $middleware = null;

    /**
     * Api constructor.
     */
    public function __construct($version = '')
    {
        $this->version = $version;
    }

    /**
     * 判断版本是否允许走指定路由
     *
     * @param array $versions
     * @param string $rule
     * @param null $route
     * @return \think\route\RuleGroup
     */
    public function route($versions = [], $rule = '', $route = null)
    {
        if (!empty($versions)) {
            if ( is_string($versions) && $versions == $this->version) {
                return Route::group($rule, $route)->middleware($this->middleware);
            }

            if ( is_array($versions) && in_array($this->version ,$versions)) {
                return Route::group($rule, $route)->middleware($this->middleware);
            }
        } else {
            return Route::group($rule, $route)->middleware($this->middleware);
        }
    }

    /**
     * 判断版本并且用JWT校验来源合法性
     *
     * @param array $versions
     * @param string $rule
     * @param null $route
     * @return \think\route\RuleGroup
     */
    public function routeWithAuther($versions = [], $rule = '', $route = null)
    {
        $this->middleware = [\cncoders\auth\middleware\AutherMiddleware::class];
        return $this->route($versions, $rule, $route);
    }
}