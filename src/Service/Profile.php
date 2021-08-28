<?php
/**
 * Pofile.php
 * @author wufeng
 * @date 2021/7/19
 */

namespace wufeng\tim\Service;

use wufeng\tim\BaseTim;
use wufeng\tim\Tim;
use wufeng\tim\Tim\Account;

class Profile extends BaseTim
{
    /**
     * 更新学员头像和昵称
     * @param $userId
     * @param array $data 昵称nickname 头像avatar 性别sex
     * @return bool|mixed
     * @author wufeng
     * @date 2021/7/19
     */
    static public function updateIMProfile(Tim $tim,$userId,$data = []){
        $account = new Account($tim);
        $profile = [];
        if (isset($data['nickname'])){
            array_push($profile,[
                'Tag'=> 'Tag_Profile_IM_Nick',
                'Value'=> $data['nickname']
            ]);
        }
        if (isset($data['avatar'])){
            array_push($profile,[
                'Tag'=> 'Tag_Profile_IM_Image',
                'Value'=> $data['avatar']
            ]);
        }
        //性别
        if (isset($data['sex'])){
            switch ($data['sex']){
                case 1:
                    $sex = 'Gender_Type_Male';
                    break;
                case 2:
                    $sex = 'Gender_Type_Female';break;
                default:
                    $sex = 'Gender_Type_Unknown';
            }
            array_push($profile,[
                'Tag'=> 'Tag_Profile_IM_Gender',
                'Value'=> $sex
            ]);
        }
        return $account->accountProfileSet($userId,$profile);
    }

    /**
     * 导入单个账号
     * @param $userId
     * @param $nickname
     * @param $avatar
     * @return bool|mixed
     * @author wufeng
     * @date 2021/7/20
     */
    public static function importSingleAccountIM(Tim $tim,$userId,$nickname,$avatar)
    {
        $account = new Account($tim);
        return $account->importSingleAccount($userId,$nickname,$avatar);
    }
}
