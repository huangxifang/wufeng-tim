<?php
/**
 * Account.php
 * @author wufeng
 * @date 2021/7/2
 */

namespace wufeng\tim\Tim;

use wufeng\tim\BaseTim;
use wufeng\tim\Tim;

/**
 * 账号
 * 开发文档：https://cloud.tencent.com/document/product/269/4919
 * Class Account
 * @package Tencent\Tim
 */
class Account extends BaseTim
{
    protected $tim;

    public function __construct(Tim $tim)
    {
        $this->tim = $tim;
    }

    /**
     * Notes: 导入单个帐号
     * @param $identifier 账号
     * @param $nick 昵称
     * @param $faceUrl 头像
     * @return bool|mixed
     * @author wufeng
     * @date 2021/7/2
     */
    public function importSingleAccount($identifier, $nick, $faceUrl)
    {
        if (empty($identifier)) {
            $this->error = "参数错误";
            return false;
        }
        $data = [
            'Identifier' => $identifier
        ];
        if($nick){
            $data['Nick'] = $nick;
        }
        if($faceUrl){
            $data['FaceUrl'] = $faceUrl;
        }
        return $this->tim->query('im_open_login_svc', 'account_import', $data);
    }

    /**
     * Notes: 导入多个账号 （单次最多导入100个账号）
     * @param $accounts  账号（数组）
     * @return bool|mixed
     * @author wufeng
     * @date 2021/7/2
     */
    public function importMultipleAccounts($accounts)
    {
        if (!is_array($accounts) || empty($accounts)) {
            $this->error = "账户参数有误！";
            return false;
        }
        $data = [
            'Accounts' => $accounts
        ];
        return $this->tim->query('im_open_login_svc', 'multiaccount_import', $data);
    }

    /**
     * Notes: 删除账号
     * @param $userItemIds 删除账号（数组） 请求删除的帐号对象数组，单次请求最多支持100个帐号
     * eg. [[ "UserID" => "UserID_1"], [ "UserID" => "UserID_1"]]
     * @return bool|mixed
     * @author wufeng
     * @date 2021/7/2
     */
    public function deleteAccount($userItemIds)
    {
        if (!is_array($userItemIds) || empty($userItemIds)) {
            throw new \Exception("删除账号参数有误！");
        }
        $data = [
            "DeleteItem" => $userItemIds,
            'UserID' => $this->tim->getUserID()
        ];
        return $this->tim->query('im_open_login_svc','account_delete',$data);
    }

    /**
     * Notes: 查询账号
     * eg. [[ "UserID" => "UserID_1"], [ "UserID" => "UserID_1"]]
     * @param $userItemIds
     * @return bool|mixed
     * @author wufeng
     * @date 2021/7/2
     */
    public function accountCheck($userItemIds){
        if (!is_array($userItemIds) || empty($userItemIds)) {
            throw new \Exception("查询账号参数有误！");
        }
        $data = [
            'CheckItem'=> $userItemIds,
            'UserID'=> $this->tim->getUserID()
        ];
        return $this->tim->query('im_open_login_svc','account_check',$data);
    }

    /**
     * Notes: 失效帐号登录状态
     * 本接口适用于将 App 用户帐号的登录状态（例如 UserSig）失效。例如，开发者判断一个用户为恶意帐号后，可以调用本接口将该用户当前的登录状态失效，这样用户使用历史 UserSig 登录即时通信 IM 会失败。
     * @param $identifier
     * @return bool|mixed
     * @author wufeng
     * @date 2021/7/2
     */
    public function kickLogin($identifier){
        if (empty($identifier)) {
            $this->error = '参数有误！';
            return false;
        }

        $data = [
            'Identifier'=> $identifier
        ];
        return $this->tim->query('im_open_login_svc','kick',$data);
    }

    /**
     * Notes: 查询帐号在线状态
     * 获取用户当前的登录状态。
     * @param $userItemIds 需要查询这些 UserID 的登录状态，一次最多查询500个 UserID 的状态
     * @param bool $isNeedDetail 是否需要返回详细的登录平台信息。0表示不需要，1表示需要
     * @return bool|mixed
     * @author wufeng
     * @date 2021/7/2
     */
    public function querystate($userItemIds, $isNeedDetail = 0){
        if (empty($userItemIds)) {
            $this->error = '参数有误！';
            return false;
        }
        $data = [
            'To_Account'=> $userItemIds
        ];
        if($isNeedDetail){
            $data['IsNeedDetail'] = 1;
        }
        return $this->tim->query('openim','querystate',$data);
    }

    /**
     * 更新账户信息
     * @param $userID  账号
     * @param $profile   更新内容 eg.  [{'Tag':'Tag_Profile_IM_Nick','Value':'MyNickName'}]
     * Tag_Profile_IM_Image 头像
     * Tag_Profile_IM_Nick 昵称
     * Tag_Profile_IM_Gender 性别
     * @return bool|mixed
     * @author wufeng
     * @date 2021/7/19
     */
    public function accountProfileSet($userID, $profile){
        if (empty($userID) || empty($profile)) {
            $this->error = '参数有误！';
            return false;
        }
        $data = [
            'From_Account'=> $userID,
            'ProfileItem'=> $profile
        ];
        return $this->tim->query('profile','portrait_set',$data);
    }
}
