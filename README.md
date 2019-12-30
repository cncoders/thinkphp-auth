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

路由使用 单独使用版本判断可以不用配置jwt

版本判断不支持路由缓存

```

<?php

use \think\facade\Route;

// 获取当前请求的版本号
$version = \think\facade\Request::header('version');

//实例化auth-api
$router = new \cncoders\auth\Router($version);

#包含的路由文件 必须放到非route目录下 可以市下面的其他目录内 会与TP内置的route冲突
$router->allows(['[<]1.0.0'])->includes(__DIR__ . '/version1/router.php');

$router->allows('[>=]1.0.1')->callback(function(){
    Route::rule('/auth', '/auth/index');
});

$router->boot('1.0.2', function(){
    Route::group(...);
});

```
auther使用

```
Auther::make()->token(); //生成TOKEN
Auther::make()->refreshToken(); //刷新TOKEN
AutherMiddleware中间件处理对鉴权的判断

```


