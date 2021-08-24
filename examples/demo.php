<?php
/**
 * demo.php
 * @author wufeng
 * @date 2021/7/1
 */

$config=[
    'appid'=> '', //必须，腾讯即时通讯获得
    'key'=> '',  //必须，腾讯即时通讯获得
    'userid'=> '',  //必须，用户名或ID，一般应用管理员
];
$tim = new \wufeng\tim\Tim($config);

// 获取userSig
$sign = $tim->getSign();

//临时手动切换账号与UserSig
$userid = 'user01';
$tim->setConfig($userid,$sign);


// $service 内部服务名
// $command 业务名
// $data 数组 需要传入的参数
$service = '';
$command = '';
$result = $tim->query($service,$command,$data = []);
print_r($result);

//结果示例
// [
//     "ActionStatus"=>"OK",
//     "ErrorInfo"=>"",
//     "ErrorCode"=>0
// ]

// eg.
$result = $tim->query('im_open_login_svc','account_import',[
    'Identifier'=>'test',
    'Nick'=>'test',
    'FaceUrl'=>'http://www.qq.com'
]);
print_r($result);
