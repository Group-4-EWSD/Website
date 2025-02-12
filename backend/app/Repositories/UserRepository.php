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
        return $this->model()::where('email', $email)->first(); // laravel eloquent
        // return DB::raw("SELECT * FROM users WHERE email = $email LIMIT 1 "); // raw query
    }
}
