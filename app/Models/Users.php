<?php


namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use mysql_xdevapi\Exception;

class Users extends Model
{
    protected $table = "users";

    /**
     * 获取所有用户
     * @return \Illuminate\Support\Collection 返回用户对象
     */
    public function getAll(){
        return DB::table($this->table)->get();
    }

    /**
     * 通过openid获取用户信息
     * @param $openid 微信openid
     * @return \Illuminate\Support\Collection 返回用户对象或NULL
     */
    public function getUserById($openid){
        return DB::table($this->table)->where('id',$openid)->first();
    }

    /**
     * 通过会员运营id获取用户信息
     * @param $special_id 会员运营id
     * @return \Illuminate\Support\Collection 返回用户对象或NULL
     */
    public function getUserBySpecialId($special_id){
        $user = DB::table($this->table)->where('special_id', $special_id)->first();
    }

    /**
     * 注册用户
     * @param $openid 微信openid
     * @return int 如执行成功返回1
     */
    public function userRegistration($openid){
        return DB::table($this->table)->insert(['id' => $openid]);
    }

    /**
     * 用户信息补全更新 通过Request获取参数
     * $openid 微信openid
     * $nickname 用户填写的昵称
     * $username ～姓名
     * $alipay_id ～支付宝账号
     * @return int 如执行成功返回1
     */
    public function userUpdate(){
        $openid = Request::post("openid");
        $nickname = Request::post("nickname");
        $username = Request::post("username");
        $alipay_id = Request::post("alipay_id");
        return DB::table($this->table)
            ->where('id', $openid)
            ->update([
                'nickname' => $nickname,
                'username' => $username,
                'alipay_id' => $alipay_id
            ]);
    }

    /**
     * 更新用户的粉丝运营id
     * @param $openid 微信openid
     * @param $special_id 粉丝运营id
     * @return int 如执行成功返回1
     */
    public function updateSpecial_id($openid,$special_id){
        try {
            return DB::table($this->table)
                ->where('id', $openid)
                ->update([
                    'special_id' => $special_id
                ]);
        }catch (Exception $e){
            return 0;
        }

    }

    /**
     * 更新未结算金额
     * @param $openid
     * @param $unsettled_balance
     * @return int
     */
    public function updateUnsettled_balance($openid,$unsettled_balance){
        return DB::table($this->table)
            ->where('id', $openid)
            ->update([
                'unsettled_balance' => $unsettled_balance
            ]);

    }

    public function getAllByPaginate($openid=null){
        if($openid==null){
            return DB::table('users')->paginate(5);
        }else{
            return DB::table('users')
                ->where('id',$openid)
                ->orWhere('nickname','like',"%".$openid."%")
                ->paginate(5);
        }

    }

    public function modifyUserById($id,$rebate_ratio,$special_id){
        return DB::table($this->table)
            ->where('id', $id)
            ->update([
                'rebate_ratio' => $rebate_ratio,
                'special_id' => $special_id,
            ]);
    }
}
