<?php
namespace App\Http\Controllers;

use App\Models\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

class AuthController extends Controller
{

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login']]);
        $this->auth = new Auth();
    }
    public function getMyClassName() {
        return "[" . get_class() . "]";
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){
        try {
            $validator = Validator::make($request->all(),$this->rules(),$this->messages());

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => $validator->errors()
                ], 422);
            }

             //dd($validator->validated());
            // dd(auth()->attempt($validator->validated()));
            if (! $token = $this->auth()->attempt($validator->validated())) {
                Log::debug('xxxxxxxxx');
                return response()->json([
                    'status'    => 401,
                    'message'   => 'ไม่พบผู้ใช้งาน Username หรือ Password ไม่ถูกต้อง'
                ], 401);
            } else {

                $userToken = $this->auth()->attempt($validator->validated());
                $getsign = explode('.',$userToken);
                // dd($userToken,$getsign[2]);
                $userData = [
                    'access_token'  => $getsign,
                    'username'      => $request->username,
                    'token_type'    => 'bearer',
                ];
                //dd($userData);

                if(!empty($userToken)){
                    // $response = $this->auth->saveLog($userData);
                    // dd($response);
                    // return $response;
                     return $this->createNewToken($token);
                }

            }
        } catch (Exception $e){
            Log::debug($this->getMyClassName().",login(),status:".$e->getCode().",message:".$e->getMessage());
            return $response = [
                "status" => $e->getCode(),
                "message" => $e->getMessage()
            ];
        }


    }
    /**
     * Register a User. (For Test Register)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'username' => 'required|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)]
                ));
        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        $this->auth()->logout();
        return response()->json([
            'status'    => 200,
            'message'   => 'User successfully signed out'
        ],200);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken($this->auth()->refresh());
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile(Request $request) {
        try{
            // $header = $request->hrader();
            // dd($header);
            $response = [
                'status'    => 200,
                'message'   => $this->auth()->user()
            ];
        } catch (Exception $e){
            Log::debug($this->getMyClassName().",userProfile(),status:".$e->getCode().",message:".$e->getMessage());
            $response = [
                "status" => $e->getCode(),
                "message" => $e->getMessage()
            ];
        } finally{
            return $response;
        }
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        $infoAboutLogin = [
            'status' => 200,
            'message'   => "Login Success",
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->auth()->factory()->getTTL() * 360,
            'user' => $this->auth()->user()
        ];

        return response()->json($infoAboutLogin,200);
    }


    /**
     * For Validator -------
     */
    public function rules()
    {
        $rules = [
            'username'  => 'required',
            'password'  => 'required|string'
        ];
        return $rules;
    }

    public function messages()
    {
        $messages = [
            'username.required'     => 'โปรดระบุผู้ใช้งาน',
            'password.required'     => 'โปรดระบุรหัสผ่าน'
        ];
        return $messages;
    }
}
