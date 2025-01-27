<?php

namespace App\Services;

use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data)
    {
        $user = $this->userRepository->create($data);
        $token = Auth::login($user);

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function login(array $credentials)
    {
        if (!$token = Auth::attempt($credentials)) {
            return false;
        }

        return [
            'user' => Auth::user(),
            'token' => $token
        ];
    }

    public function logout()
    {
        Auth::logout();
        return true;
    }
}
