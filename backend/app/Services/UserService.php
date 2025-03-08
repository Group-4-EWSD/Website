<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Services\FileService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserService
{
    protected $userRepository;
    protected $fileService;

    public function __construct(UserRepository $userRepository, FileService $fileService)
    {
        $this->userRepository = $userRepository;
        $this->fileService = $fileService;
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
        // Insert into login history
        DB::table('login_histories')->insert([
            [
                'id' => Str::uuid(),
                'user_id' => $user->id, 
                'ip_address' => request()->ip(),
                'user_agent' => request()->header('User-Agent'),
                'logged_in_at' => now(),
            ],
        ]);
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

    // public function updateUserProfile($id, array $data)
    // {
    //     return $this->userRepository->updateUser($id, $data);
    // }

    public function getTermsCondition()
    {
        $termsConditions = $this->userRepository->getTermsCondition();        
        $termConditionsArray = array_map(fn($item) => $item->term_condition, $termsConditions);
        return $termConditionsArray;
    }

    public function updateUserPhoto(string $id, $photo): string
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            throw new \Exception('User not found', 404);
        }

        if ($user->user_photo_path) {
            $this->fileService->deleteFile($user->user_photo_path);
        }

        $photoPath = $this->fileService->uploadFile($photo)['file_path'];
        $this->userRepository->updatePhoto($id, $photoPath);

        return $photoPath;
    }

    public function getUserPhoto(string $id): ?string
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            throw new \Exception('User not found', 404);
        }

        return $user->user_photo_path;
    }

    public function deleteUserPhoto(string $id): void
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            throw new \Exception('User not found', 404);
        }

        if ($user->user_photo_path) {
            $this->fileService->deleteFile($user->user_photo_path);
            $this->userRepository->updatePhoto($id, null);
        }
    }

    public function updateUserProfile($id, array $data)
    {
        $user = $this->userRepository->getUserById($id);
        if (!$user) {
            throw new \Exception('User not found', 404);
        }
        return $this->userRepository->updateUser($id, $data);
    }

    public function getUserList(){
        return $this->userRepository->getUserList();
    }
}