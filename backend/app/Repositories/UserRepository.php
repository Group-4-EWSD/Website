<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;
//use Your Model

/**
 * Class UserRepository.
 */
class UserRepository extends BaseRepository
{
    /**
     * @return string
     *  Return the model
     */
    public function model()
    {
        return User::class;
    }

    public function findByEmail($email)
    {
        return $this->model()::where('user_email', $email)->first(); // laravel eloquent
        // return DB::raw("SELECT * FROM users WHERE email = $email LIMIT 1 "); // raw query 
    }

    public function getUserById($id)
    {
        return User::findOrFail($id);
    }

    public function findById(string $id): ?User
    {
        return $this->model->find($id);
    }

    public function updatePhoto(string $id, ?string $photoPath): bool
    {
        $user = $this->findById($id);
        if (!$user) {
            return false;
        }

        $user->user_photo_path = $photoPath;
        return $user->save();
    }

    public function updateUser($id, array $data)
    {
        $user = User::findOrFail($id);
        $user->update($data);
        return $user;
    }

    public function getTermsCondition()
    {
        $termsConditions = DB::table("terms_conditions")->get();
        return $termsConditions;
    }
}
