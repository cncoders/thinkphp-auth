# thinkphp-auth
基于thinkphp6开发的一个JWT权限验证插件

# 使用方法

### 1. 安装

```
composer require cncoders/thinkphp-auth *
```

### 2. 配置文件

将安装包内的config/jwt.php复制到thinkphp配置目录

### 3. 异常处理

如果需要对异常处理成JSON接口可用的格式可以在app\ExceptionHandle.php增加相应的异常处理示例如下：

```

if ($e instanceof AutherException) {
	exit( json_encode(['code' => $e->getCode(), 'message' => $e->getMessage()]) );
}

```

### 4.使用示例：

路由使用

```

<?php

use \think\facade\Route;

// 获取当前请求的版本号
$version = \think\facade\Request::header('version');

//实例化auth-api
$api = new \cncoders\auth\Api($version);

//不需要通过JWT鉴权
$api->route('', '', function(){

    //登录 获取TOKEN
    Route::post('login', '/user/login');

    //刷新TOKEN
    Route::post('refreshToken', '/user/refreshToken');
});

//v1旧版本接口
$api->route('v1', 'content', function(){

    Route::post('show', '/content/show_v1');

});

//v2新版接口
$api->route('v2', 'content', function(){
    //...
    Route::post('show', '/content/show_v2');
});

//需要进行TOKEN校验的接口
$api->routeWithAuther(['v1', 'v2'], '', function(){

    //用户中心需要经过TOKEN鉴权校验
    Route::post('center', '/user/center');

});

```
单独使用auther

```
Auther::make()->token(); //生成TOKEN
Auther::make()->refreshToken(); //刷新TOKEN
AutherMiddleware中间件处理对鉴权的判断

```


