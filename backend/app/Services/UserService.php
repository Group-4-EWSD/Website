<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Services\FileService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        return [
            'status' => 200,
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ];
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

        $photoPath = $this->fileService->uploadFile($photo, 'user_photos');

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
