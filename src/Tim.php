<?php
/**
 * Tim.php
 * @author wufeng
 * @date 2021/7/1
 */

namespace wufeng\tim;

use GuzzleHttp\Client;

class Tim
{
    private $appid = '';
    private $key = '';
    private $userid = '';
    private $sign = '';
    private $service = [
        'im_open_login_svc',
        'openim',
        'all_member_push',
        'profile',
        'sns',
        'group_open_http_svc',
        'openconfigsvr',
        'open_msg_svc',
        'ConfigSvc'
    ];


    private $url = 'https://console.tim.qq.com/v4/';

    public function __construct($config, $userid = '')
    {

        $_userid = $userid ? $userid : $config['userid'];

        if (!$config['appid'] || !$config['key'] || !$_userid) {
            throw new \Exception('appid、key、userid 不能为空');
        }

        $this->appid = $config['appid'];
        $this->key = $config['key'];
        $this->userid = $_userid;
    }

    public function getAppID()
    {
        return $this->appid;
    }

    /**
     * Notes: 设置UserSig
     * @param $sign
     * @author wufeng
     * @date 2021/7/2
     */
    public function setSign($sign){
        $this->sign = $sign;
    }

    /**
     * Notes: 设置管理账号
     * @param $userid
     * @author wufeng
     * @date 2021/7/2
     */
    public function setUserID($userid){
        $this->userid = $userid;
    }

    /**
     * Notes: 获取userID
     * @return mixed|string
     * @author wufeng
     * @date 2021/7/2
     */
    public function getUserID(){
        return $this->userid;
    }

    /**
     * 获取UserSig
     */
    public function getSign()
    {
        //自动缓存
        $expire_time = 86400;
        $sign = new Sign($this->appid, $this->key);
        $_sign = $sign->genUserSig($this->userid, $expire_time);
        $this->sign = $_sign;
        // 是否进行缓存处理
        // $cacheKey = 'tim_sign_' . $this->userid;
        // if (!$_sign) {
        //     Cache::store('redis')->set($cacheKey, $_sign, $expire_time - 400);
        // }
        return $_sign;
    }

    /**
     * 请求
     */
    public function query($service, $command, $data = [])
    {
        if (!$service) {
            throw new \Exception('服务名不能为空');
        }
        if (!$command) {
            throw new \Exception('业务名不能为空');
        }
        if (!in_array($service, $this->service)) {
            throw new \Exception("业务[$service]不存在");
        }

        return $this->post($service, $command, $data);
    }

    /**
     * 手动配置UserSig
     * @param userid
     * @param sign
     */
    public function setConfig($userid, $sign)
    {
        $this->userid = $userid;
        $this->sign = $sign;
        return $this;
    }

    /**
     * Post提交数据
     */
    public function post($servicename, $command, $data)
    {
        $rand1 = rand(10000, 55555);
        $rand2 = rand(10000, 55555);
        $random = $rand1 . $rand2;

        $client = new Client([
            'base_uri' => $this->url,
            'timeout' => 5.0,
        ]);

        $query['sdkappid'] = $this->appid;
        $query['identifier'] = $this->userid;
        $query['usersig'] = $this->getSign();
        $query['random'] = $random;
        $query['contenttype'] = 'json';

        $response = $client->post("$servicename/$command", [
            'query' => $query,
            'json' => $data
        ]);

        $body = (string)$response->getBody();
        if (!$body) {
            return false;
        }
        return json_decode($body, true);
    }
}
