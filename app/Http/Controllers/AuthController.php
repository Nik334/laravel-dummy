<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Repositories\AuthRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    private AuthRepository $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    /**
     * @throws Exception
     */
    public function login(Request $request): JsonResponse
    {
        $inputs = $this->getInput($request);
        return $this->authRepository->loginUser($inputs);
    }

    public function register(RegisterRequest $registerRequest): JsonResponse
    {
        $inputs = $this->getInput($registerRequest);
        return $this->authRepository->add($inputs);
    }

    private function getInput($request): array
    {
        return array_replace_recursive(
            $request->all(),
            $request->route()->parameters(),
            $request->header()
        );
    }
}
