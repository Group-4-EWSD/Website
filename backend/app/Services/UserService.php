<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login($credentials)
    {
        $user = $this->userRepository->findByEmail($credentials['email']);
        if (!$user || !Hash::check($credentials['password'], $user->user_password)) {
            return [
                'status' => 401,
                'message' => 'Invalid email or password'
            ];
        }
        $token = $user->createToken('authToken')->plainTextToken;
        return [
            'status' => 200,
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ];
    }

    public function getUserProfile($id)
    {
        return $this->userRepository->getUserById($id);
    }

    public function updateUserProfile($id, array $data)
    {
        return $this->userRepository->updateUser($id, $data);
    }

    public function getTermsCondition()
    {
        return $this->userRepository->getTermsCondition();
    }
}
