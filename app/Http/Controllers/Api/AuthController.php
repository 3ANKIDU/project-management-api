<?php

namespace App\Http\Controllers\Api;

use App\Actions\Fortify\CreateNewUser;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Fortify\Http\Requests\LoginRequest;

class AuthController extends Controller
{

    protected $createNewUser;

    public function __construct(CreateNewUser $createNewUser){
        $this->createNewUser = $createNewUser;
    }

    /**
     * Regiser a newly created resource in storage.
     */
    public function register(Request $request)
    {

        DB::beginTransaction();
        try {

            $user = $this->createNewUser->create($request->all());

            $token = $user->createToken('AccessToken')->accessToken;

            DB::commit();

            return response()->json([
                'status' => 'success',
                'user' => new UserResource($user),
                'token' => $token,
            ], 201);
        } catch (\InvalidArgumentException $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Exception caught while registering a new user: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create user',
            ], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        $password = $request->password;

        $user = User::where('email', $request->email)->select('id', 'email', 'password')->firstOrFail();

        if ($user && Hash::check($password, $user->password)) {
            Log::info('', [$user]);
            $token = $user->createToken('AccessToken')->accessToken;

            return response()->json([
                'status' => 'success',
                'message' => 'login successfully',
                'user' => new UserResource($user),
                'token' => $token,
            ], 200);
        }

        return response()->json(['message' => 'Password is not correct!'], 401);
    }

    public function logout(Request $request)
    {
        Log::info(request()->headers->all());
        $user = $request->user();

        if ($user) {

            $user->tokens->each->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'User logged out successfully',
            ], 200);
        }

        return response()->json([
            'status' => 'failed',
            'message' => 'User not found.',
        ], 404);
    }

}
