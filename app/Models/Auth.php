<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class Auth extends Model
{
    //use HasFactory;

    public function __construct()
    {
        $this->AuthLogApiLogin = DB::connection('AUTH_JWT_LOG_API');
    }

    public function getMyClassName() {
        return "[" . get_class() . "]";
    }

    public function callback($userArr){
        try{
            // dd($userArr['access_token']);
            // dd("ss");
            $res = DB::table('tbm_users')
            // $res = DB::table('users_b')
                        ->where('username',$userArr['username'])
                        ->where('status','ACTIVE')
                        ->update([
                            'access_token' => $userArr['access_token'],
                            'updated_at' => Carbon::now(),
                        ]);

            if(!$res){
                throw new Exception('Can not update access token',-1004);
            }

            $udata = DB::table('tbm_users')
            // $udata = DB::table('users_b')
                        ->where('username',$userArr['username'])
                        ->where('status','ACTIVE')
                        ->get();

            return $response = [
                "responseCode" => 200,
                // "responseMessage" =>$userArr['redirect_uri'],
                "userInfo" => [
                    // 'expires_in' => auth()->factory()->getTTL() * 60,
                    'access_token' => $userArr['access_token'],
                    'register_id' => @$udata[0]->register_id,
                    'username' => @$udata[0]->username,
                    'name_th'  => @$udata[0]->name_th,
                    'name_en'  => @$udata[0]->name_en,
                    'email' => @$udata[0]->email,
                    'email_verified_at'  => @$udata[0]->email_verified_at,
                    'employee_id' => @$udata[0]->employee_id,
                    'employee_type' => @$udata[0]->employee_type,
                    'remember_token' => @$udata[0]->remember_token,
                    'mobile_phone' => @$udata[0]->mobile_phone,
                    'position'  => @$udata[0]->position,
                    'level' => @$udata[0]->level,
                    'branch_code'  => @$udata[0]->branch_code,
                    'start_date'  => @$udata[0]->start_date,
                    'status' =>  @$udata[0]->status,
                    'deleted' =>  @$udata[0]->deleted,
                ]
            ];
        }
        catch (Exception $e) {
            Log::debug($this->getMyClassName().",callback(),responseCode:".$e->getCode().",responseMessage:".$e->getMessage());
            return $response = [
                "responseCode" => $e->getCode(),
                "responseMessage" => $e->getMessage()
            ];
        }
    }

    public function saveLog($userData)
    {
        // dd($userData);
        $getTokenSQL = DB::table('tbm_users')->where('username', $userData['username'])->select('access_token')->get();
        // ($getTokenSQL[0]->access_token);

        $logSave = $this->AuthLogApiLogin->table('tbt_log_api_jwt')
                    ->insert([
                    'username'      => $userData['username'],
                    'access_token'  => $userData['access_token'],
                    'token_type'    => $userData['token_type'],
                    'device'        => $userData['device'],
                    'ref_token'     => $getTokenSQL[0]->access_token,
                    'login_time'    => Carbon::now()
                ]);
        return $logSave;
    }
}
