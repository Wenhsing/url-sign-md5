# 简单URL签名MD5版

## 关于

`url-sign-md5` 包是一个简单的URL参数签名验证，主要在Laravel中使用。

## 安装和卸载

添加 `wenhsing/url-sign-md5` 到你的 `composer.json` 文件并更新对应的依赖：

```sh
composer require wenhsing/url-sign-md5
```

如果你不想使用或者遇到了其他问题，可以通过下面的方式卸载，然后再进行一次安装:

```sh
composer remove wenhsing/url-sign-md5
```

## 使用

打开 `app/Http/Kernel.php` 文件，将 `UrlSignMiddleware` 中间件添加到 `$middlewareGroups` 的 `url` 字段下:

```php
protected $middlewareGroups = [
    // 其他分组

    'api' => [
        \Wenhsing\UrlSign\Laravel\Middleware\UrlSignMiddleware::class,
        // 其他中间件
    ]
];
```

当然，你可以将 `UrlSignMiddleware` 中间件添加到 `$middleware` 属性中，在全局进行签名验证：

```php
protected $middleware = [
    \Wenhsing\UrlSign\Laravel\Middleware\UrlSignMiddleware::class,
    // 其他中间件
];
```

## 配置

如果你的 `config` 目录下不存在 `url-sign.php` 配置文件的话，可以通过这种方式生成文件:

```sh
php artisan vendor:publish --tag="url-sign"
```

然后修改 `url-sign.php` 配置文件即可:

```php
<?php
return [
    // 需要排除的路径，条目可以参考如下写法
    'except'     => [
        '/',
        'a/test',
        'b/test2/*',
    ],

    // 签名加签密钥
    'secret_key' => '',

    // 允许签名请求时间与当前时间的误差值
    'time_error' => 300,
];
```
