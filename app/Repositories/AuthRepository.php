<?php

namespace App\Repositories;

use App\Http\StatusCodes;
use App\Models\Token;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthRepository
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @throws Exception
     */
    public function loginUser($request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $email = $request['email'];
            $password = $request['password'];
            $user = $this->user::where('email', $email)
                ->where('status', 'ACTIVE')
                ->first();

            if ($email && $password) {
                if ($user && Hash::check($password, $user->password)) {
                    $accessToken = $user->createToken('authToken')->accessToken;
                    if ($accessToken) {
                        $authToken = $this->saveToken($accessToken, $user->id);
                        if ($authToken) {
                            $response = response()->json([
                                "status" => "success",
                                "message" => "User logged in Successfully",
                                "data" => [
                                    'id' => $user->id,
                                    'name' => $user->name,
                                    'email' => $user->email,
                                    'token' => $accessToken,
                                ],
                            ]);
                        } else {
                            $response = response()->json([
                                "error" => "Invalid credentials",
                                "errorDetails" => "Invalid credentials",
                                "type" => "INVALID_CREDENTIALS"
                            ], 401);
                        }
                    }
                } else {
                    $response = response()->json([
                        "error" => "User not found",
                        "errorDetails" => "User not found",
                        "type" => "RESOURCE_NOT_FOUND"
                    ], 404);
                }
            } else {
                $response = response()->json([
                    "error" => "Invalid Data",
                    "errorDetails" => "Invalid Data",
                    "type" => "RESOURCE_NOT_FOUND"
                ], 500);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
        return $response;
    }

    public function saveToken($accessToken, $id)
    {
        $token = new Token();
        $token->user_id = $id;
        $token->token = $accessToken;
        $genToken = $token->save();

        if ($genToken) {
            return $accessToken;
        } else {
            return false;
        }
    }

    public function add($request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $email = $request['email'];
            if ($this->user::orWhere('email', $email)->first()) {
                return response()->json([
                    "error" => "User already registered",
                    "errorDetails" => "User registration failed, user already registered with us",
                    "type" => "RESOURCE_CREATION_FAILED"
                ], StatusCodes::$CONFLICT);
            }
            $userDetail = $this->user::create($request);
            DB::commit();
            return response()->json([
                "status" => "success",
                "message" => "User registered successfully",
                "data" => [
                    'id' => $userDetail->id,
                    'name' => $userDetail->name,
                    'email' => $userDetail->email,
                ],
            ], StatusCodes::$SUCCESS);
        } catch (Exception $e) {
            return response()->json([
                "status" => "failed",
                "message" => $e,
                "data" => null,
            ], StatusCodes::$CONFLICT);
        } finally {
            DB::rollback();
        }
    }

    public function self(Request $request): JsonResponse
    {
        $token = $this->getBearerToken($request);
        $token = Token::where('token', $token)->first();
        if ($token && $token->id) {
            $userData = $this->user::where('id', $token->user_id)
                ->where('status', 'ACTIVE')
                ->first();
//            $userData = $this->user::with('files')->where('id', $token->user_id)->where('status', 'ACTIVE')->first();
            if ($userData->id) {
                $response = response()->json([
                    "status" => "success",
                    "message" => "User logged in",
                    "data" => [
                        'id' => $userData->id,
                        'name' => $userData->name,
                        'username' => $userData->username,
                        'email' => $userData->email,
                        'contact' => $userData->contact,
                    ],
                ], 200);
            } else {
                $response = response()->json([
                    "error" => "User not found",
                    "errorDetails" => "User not found",
                    "type" => "RESOURCE_NOT_FOUND"
                ], 404);
            }
        } else {
            $response = response()->json([
                "error" => "Token not found",
                "errorDetails" => "Token not found",
                "type" => "RESOURCE_NOT_FOUND"
            ], 401);
        }

        return $response;
    }

    public function getBearerToken($request): bool|string
    {
        $header = $request->header('Authorization', '');
        return (Str::startsWith($header, 'Bearer ')) ? Str::substr($header, 7) : false;
    }
}
