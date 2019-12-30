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
$router = new \cncoders\auth\Router($version);

$router->allows(['[<]1.0.0'])->includes(__DIR__ . '/version1/router.php');

$router->allows('[>=]1.0.1')->callback(function(){
    Route::rule('/auth', '/auth/index');
});

$router->boot('1.0.2', function(){
    Route::group(...);
});

```
单独使用auther

```
Auther::make()->token(); //生成TOKEN
Auther::make()->refreshToken(); //刷新TOKEN
AutherMiddleware中间件处理对鉴权的判断

```


