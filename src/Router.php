<?php
namespace cncoders\auth;

use cncoders\helper\Helper;

class Router
{
    /**
     * 当前访问的版本
     * @var bool
     */
    protected $version = false;
    protected $isCom = false;

    /**
     * 允许访问的版本
     * @var array
     */
    protected $allowVersions = [];

    /**
     * Router constructor.
     * @param $version
     */
    public function __construct($version, $is_com = false)
    {
        $this->version = $version;
        $this->isCom = $is_com;
    }

    /**
     * @param string $allowVersion
     * @param null $callback
     * @return bool|mixed
     */
    public function boot($allowVersion = '', $callback = null)
    {
        if ( is_string($allowVersion) ) $allowVersion = [$allowVersion];
        $total = count($allowVersion);
        for( $i = 0; $i < $total; $i++ ) {
            if ( false === $this->compareVersions($allowVersion[$i], $callback)) {
                continue;
            } else {
                return $this->compareVersions($allowVersion[$i], $callback);
            }
        }
        unset($total,$i,$allowVersion);
        return false;
    }

    /**
     * @param string $allowVersion
     * @param null $callback
     * @return bool|mixed
     */
    private function compareVersions($allowVersion = '', $callback = null)
    {
        if ( empty($allowVersion) || $this->isCom === true ) {
            return call_user_func($callback);
        }
        $capare = false;
        if ( stripos($allowVersion, '[<]') !== false ) {
            $capare = true;
            if (Helper::compareVersion($this->version , $allowVersion) !== -1) {
                return false;
            }
        }
        if ( stripos($allowVersion, '[<=]') !== false ) {
            $capare = true;
            if (Helper::compareVersion($this->version , $allowVersion) === 1) {
                return false;
            }
        }
        if ( stripos($allowVersion, '[>]') !== false ) {
            $capare = true;
            if (Helper::compareVersion($this->version, $allowVersion) !== 1) {
                return false;
            }
        }
        if ( stripos($allowVersion, '[>=]') !== false ) {
            $capare = true;
            if (Helper::compareVersion($this->version, $allowVersion) === -1) {
                return false;
            }
        }
        if ($capare === false && Helper::compareVersion($this->version, $allowVersion) !== 0) {
            return false;
        }
        return call_user_func($callback);
    }

    /**
     * 设置允许访问的版本
     * @param $allowVersions
     * @return $this
     */
    public function allows($allowVersions)
    {
        $this->allowVersions = is_string($allowVersions) ? [$allowVersions] : $allowVersions;
        return $this;
    }

    /**
     * 加载路由文件
     * @param $file
     */
    public function includes($file)
    {
        if ( !file_exists($file) ) {
            return false;
        }
        return $this->boot($this->allowVersions, function() use ($file){
            include_once $file;
        });
    }

    /**
     * 以回调函数执行
     * @param null $callback
     * @return bool
     */
    public function callback($callback = null)
    {
        return $this->boot($this->allowVersions, $callback);
    }
}