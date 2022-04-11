<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\frontend\MemberLoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\api\MemberRequest;
use App\Http\Requests\api\LoginRequest;
use Illuminate\Http\JsonResponse;
use Intervention\Image\Facades\Image as Image;
use App\Http\Requests\api\UpdateProfileRequest;

use function GuzzleHttp\json_encode;
use App\Traits\ThrottleLogins;

class MemberController extends Controller
{
    use ThrottleLogins;
    public $successStatus = 200;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $getAllMember = User::find(Auth::id())->toArray();
        // dd($getAllMember);
    }

    /**
     * Display a member.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $getMember = User::find(Auth::id())->toArray();
        return view('frontend.member.profile', compact('getMember'));
    }
    /**
     * Show login form
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLogin()
    {
        return view('frontend.member.login');
    }

    /**
     * Do login
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function login(LoginRequest $request)
    {
        if($this->hasTooManyAttempts($request))
        {
            return $this->sendLockoutResponse($request);
        }
        $login = [
            'email' => $request->email,
            'password' => $request->password,
            'level' => 0
        ];
        $remember = false;
        if ($request->remember_me) {
            $remember = true;
        }

        if ($this->doLogin($login, $remember)) {
            $this->clearAttempts($request);
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')->accessToken;

            return response()->json([
                    'response' => 'success',
                    'success' => $success,
                    'Auth' => Auth::user()
                ],
                $this->successStatus
            );

            // $token = Auth::attempt($login);
            // return response()->json([
            //     'response' => 'success',
            //     'result' => [
            //         'token' => $token,
            //     ],
            //     'Auth' => Auth::user()
            // ], JsonResponse::HTTP_OK);

        } else {
            $this->incrementAttempts($request);
            return response()->json([
                    'response' => 'error',
                    'errors' => ['errors' => 'invalid email or password'],
                ],
                $this->successStatus);
            // return response()->json([
            //     'response' => 'error',
            //     'errors' => ['errors' => 'invalid email or password'],
            // ], JsonResponse::HTTP_OK);
        }
    }

    public function details()
    {
        $user = Auth::user();
        return response()->json(['success' => $user], $this->successStatus);
    }

    /**
     * Do login
     *
     * @param $attempt
     * @param $remember
     * @return bool
     */
    protected function doLogin($attempt, $remember)
    {

        if (Auth::attempt($attempt, $remember)) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * Logout
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    public function register(MemberRequest $request)
    {
        $user = User::All()->toArray();
        $data = $request->all();

        $file = $request->get('avatar');
        if($file) {
           $image = $file;
           $name = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
           $data['avatar'] = $name;
        }

        $data['password'] = bcrypt($data['password']);
        if ($getUser = User::create($data)) {
            if($file){
                Image::make($file)->save(public_path('upload/user/avatar/').$data['avatar']);
            }
            return response()->json([
                'message' => 'success',
                $getUser
            ], JsonResponse::HTTP_OK);
        } else {
            return response()->json(['errors' => 'error sever'], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function update(UpdateProfileRequest $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->all();

        $getEmail = User::All()
            ->where('email', $data['email'])
            ->where('id', '<>', $id)
            ->first();

        if($getEmail) {
            $getEmail->toArray();
            return response()->json([
                'errors' => ['errors' => 'Email da ton tai'],
                'email' => $getEmail['email']
            ], JsonResponse::HTTP_OK);
        }

        $file = $request->avatar;
        if($file) {
           $image = $file;
           if(strpos($image, ';')){
                $name = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
                $data['avatar'] = $name;
           }
        } else {
            $data['avatar'] = $user->avatar;
        }

        if ($data['password']) {
            $data['password'] = bcrypt($data['password']);
        }else{
            $data['password'] = $user->password;
        }


        if ($getUser = $user->update($data)) {
            if(strpos($file, ';')){
                Image::make($file)->save(public_path('upload/user/avatar/').$data['avatar']);
            }
            $userAuth = Auth::user();

            $data['id'] = $id;
            $success['token'] =  $userAuth->createToken('MyApp')->accessToken;
            return response()->json([
                    'response' => 'success',
                    'success' => $success,
                    'Auth' => $data
                ],
                $this->successStatus
            );
        } else {
            return response()->json([
                'errors' => 'error update',
            ],
            $this->successStatus);
            // return response()->json(['errors' => 'error sever'], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

    }
}
