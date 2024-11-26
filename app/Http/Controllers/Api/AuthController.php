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
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Http\Requests\LoginRequest;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;


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
            ], Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Exception caught while registering a new user: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create user',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function login(LoginRequest $request)
    {
        $password = $request->password;

        $user = User::where('email', $request->email)->firstOrFail();

        if ($user && Hash::check($password, $user->password)) {
            Log::info('', [$user]);
            $token = $user->createToken('AccessToken')->accessToken;

            return response()->json([
                'status' => 'success',
                'message' => 'login successfully',
                'user' => new UserResource($user),
                'token' => $token,
            ], Response::HTTP_OK);
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
            ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => 'failed',
            'message' => 'User not found.',
        ], Response::HTTP_NOT_FOUND);
    }

    public function assignRole(User $user, Request $request)
    {

        $validator = Validator::make($request->all(), [
            'role' => 'required|string|exists:roles,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], Response::HTTP_BAD_REQUEST);
        }

        $role = Role::findByName($request->role);

        if (!$role) {
            return response()->json([
                'status'=> 'error',
                'message' => 'Role not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $user->assignRole($request->role);

        return response()->json([
            'status'=> 'success',
            'message'=> 'Role assigned successfully',
            ], Response::HTTP_OK);
    }

}
