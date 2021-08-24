# 腾讯云即时通信IM 接口

php 简单封装 腾讯即时通讯IM 服务端，便于自己方便使用

IM版本：v4

## composer安装

```shell script
composer require wufeng\tim
```

## 使用说明

1.初始化

```php
$config=[
    'appid'=> '', //必须，腾讯即时通讯获得
    'key'=> '',  //必须，腾讯即时通讯获得
    'userid'=> '',  //必须，用户名或ID，一般应用管理员
];
$tim = new \wufeng\tim\Tim($config);
```

2.获取UserSig

```php
/**
* 获取UserSig
*/
$sign = $tim->getSign();
```

临时手动切换账号与UserSig

```php
// $sign 为已获得的UserSig
$tim->setConfig($userid,$sign);
```

3.请求命令

```php
$result = $tim->query('im_open_login_svc','account_import',[
    'Identifier'=>'test',
    'Nick'=>'test',
    'FaceUrl'=>'http://www.qq.com'
]);
print_r($result);
```

结果应答
```php
//结果示例
[
   "ActionStatus"=>"OK",
   "ErrorInfo"=>"",
   "ErrorCode"=>0
]
```

> 具体业务逻辑与返回数据以官方文档为准 更多使用方法参考官方文档
> https://cloud.tencent.com/document/product/269/1519