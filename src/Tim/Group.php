<?php
/**
 * Group.php
 * @author wufeng
 * @date 2021/7/2
 */

namespace wufeng\tim\Tim;


use wufeng\tim\BaseTim;
use wufeng\tim\Tim;

class Group extends BaseTim
{
    protected $tim;

    protected $service = 'group_open_http_svc'; //群组管理

    public function __construct(Tim $tim)
    {
        $this->tim = $tim;
    }

    /**
     * Notes: 获取 App 中的所有群组
     * https://cloud.tencent.com/document/product/269/1614
     * @param int $limit Limit 限制回包中 GroupIdList 中群组的个数，不得超过10000。
     * @param int $next Next 控制分页。对于分页请求，第一次填0，后面的请求填上一次返回的 Next 字段，当返回的 Next 为0，代表所有的群都已拉取到。
     * @param string $groupType 群组形态包括 Public（公开群），Private（私密群），ChatRoom（聊天室），AVChatRoom（音视频聊天室）和 BChatRoom（在线成员广播大群）
     * @return bool|mixed
     * @author wufeng
     * @date 2021/7/2
     */
    public function get_appid_group_list($limit=0,$next=0,$groupType='')
    {
        $data = [];
        if($limit){
            $data['Limit'] = $limit;
        }
        if($next){
            $data['Next'] = $next;
        }
        if ($groupType){
            // Public，Private，ChatRoom、AVChatRoom和BChatRoom。
            $data['GroupType'] = $groupType;
        }
        return $this->tim->query('group_open_http_svc','get_appid_group_list',$data);
    }

    /**
     * Notes: 创建群组
     * 文档：https://cloud.tencent.com/document/product/269/1615
     * @param $params 请求包参数
     * @return bool|mixed
     * @author wufeng
     * @date 2021/7/2
     */
    public function create_group($params)
    {
        if(!isset($params['Type']) || empty($params['Type']))
            throw new \Exception("Type参数不能为空");
        if(!isset($params['Name']) || empty($params['Name']))
            throw new \Exception("Name参数不能为空");
        return $this->tim->query('group_open_http_svc','create_group',$params);
    }

    /**
     * Notes:获取群详细资料
     * https://cloud.tencent.com/document/product/269/1616
     * @param $groupList  群ID的列表
     * @param array $ext 选填参数参数
     * @return bool|mixed
     * @author wufeng
     * @date 2021/7/2
     */
    public function get_group_info($groupList,$ext=[])
    {
        if (empty($groupList))
            throw new \Exception("GroupIdList参数不能为空");
        $data = ['GroupIdList' => $groupList];
        if (!empty($ext))
            $data = array_merge($data, $ext);
        return $this->tim->query('group_open_http_svc', 'get_group_info', $data);
    }

    /**
     * Notes: 修改群基础资料
     * @param $groupID 群ID
     * @param array $info 群组基础信息
     * @param array $appDefinedData  群自定义信息
     * @return bool|mixed
     * @author wufeng
     * @date 2021/7/8
     */
    public function modify_group_base_info($groupID,$info=[],$appDefinedData = [])
    {
        $data = ['GroupId'=>$groupID];
        $data = array_merge($data,$info);
        !empty($appDefinedData) and $data['AppDefinedData'] = $appDefinedData;
        return $this->tim->query($this->service,'modify_group_base_info',$data);
    }

    /**
     * Notes: 获取群成员详细资料
     * 文档：https://cloud.tencent.com/document/product/269/1617
     * @param $groupId
     * @param array $extend
     * @return bool|mixed
     * @author wufeng
     * @date 2021/7/7
     */
    public function get_group_member_info($groupId, $extend = [])
    {
        $data = ['GroupId'=>$groupId];
        if(!empty($extend)){
            $data = array_merge($data,$extend);
        }
        return $this->tim->query('group_open_http_svc','get_group_member_info',$data);
    }


    /**
     * Notes: 撤回指定用户发送的消息
     * @param $groupId 要撤回消息的群 ID
     * @param $senderAccount 被撤回消息的发送者ID
     * @author wufeng
     * @date 2021/7/6
     */
    public function delete_group_msg_by_sender($groupId,$senderAccount)
    {
        if(empty($groupId) || empty($senderAccount)){
            return false;
        }
        $data = [
            'GroupId' => $groupId,
            'Sender_Account' => $senderAccount
        ];
        return $this->tim->query('group_open_http_svc','delete_group_msg_by_sender',$data);
    }

    /**
     * Notes: 撤销群消息
     * @param $groupId 操作的群 ID
     * @param $msgSeqList 被撤回的消息 seq 列表，一次请求最多可以撤回10条消息 seq
     * @return bool|mixed
     * @author wufeng
     * @date 2021/7/7
     */
    public function group_msg_recall($groupId,$msgSeqList)
    {
        if(empty($groupId) || empty($msgSeqList)){
            return false;
        }
        $data = ['GroupId'=>$groupId,'MsgSeqList'=>$msgSeqList];
        return  $this->tim->query('group_open_http_svc','group_msg_recall',$data);
    }

    /**
     * Notes: 添加群成员
     * @param $groupId  群ID
     * @param array $memberLists  待添加的群成员数组
     * @param int $silence 是否静默加人。0：非静默加人；1：静默加人。不填该字段默认为0
     * @return bool|mixed
     * @author wufeng
     * @date 2021/7/8
     */
    public function add_group_member($groupId,$memberLists,$silence = 0)
    {
        $data = [
            'GroupId'=>$groupId,
            'Silence'=> $silence,
            'MemberList'=> $memberLists
        ];
        return $this->tim->query('group_open_http_svc','add_group_member',$data);
    }

    /**
     * Notes: 批量禁言和取消禁言
     * @param $groupId  群ID
     * @param int $shutUpTime 禁言时间，单位为秒，为0时表示取消禁言
     * @param array $memberAccount 需要禁言的用户帐号，最多支持500个帐号
     * @return bool|mixed
     * @author wufeng
     * @date 2021/7/8
     */
    public function forbid_send_msg($groupId,$shutUpTime = 60,$memberAccount=[])
    {
        if(empty($groupId) || empty($memberAccount)){
            throw new \Exception("参数错误");
        }
        $data = [
            'GroupId'=>$groupId,
            'ShutUpTime'=>$shutUpTime
        ];
        $data['Members_Account'] = $memberAccount;
        return $this->tim->query($this->service,'forbid_send_msg',$data);
    }
}
