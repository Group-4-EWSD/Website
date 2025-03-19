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
        return $this->model()::select([
            'users.id',
            'users.user_name',
            'users.nickname',
            'users.user_email',
            'users.user_password',
            'users.user_type_id',
            'ut.user_type_name',
            'f.faculty_name',
            'users.gender',
            'users.date_of_birth',
            'users.phone_number',
            DB::raw("CONCAT('https://ewsdcloud.s3.ap-southeast-1.amazonaws.com/', users.user_photo_path) AS user_photo_path"),
        ])
        ->where('users.user_email', $email)
        ->join('user_types as ut', 'ut.user_type_id', '=', 'users.user_type_id')
        ->join('faculties as f', 'f.faculty_id', '=', 'users.faculty_id')
        ->first();
    }
    
    public function getUserById($id)
    {
        return User::findOrFail($id);
    }

    public function updateUser($id, array $data)
    {
        $user = User::findOrFail($id);
        $user->update($data);
        return $user;
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

    public function getGuestList(){
        return DB::table('users as u')
        ->select([
            'u.user_name',
            'u.user_email',
            'f.faculty_name',
            'u.date_of_birth',
            'u.gender',
            'u.phone_number'
        ])
        ->join('faculties as f', 'f.faculty_id', 'u.faculty_id')
        ->where('u.user_type_id','=','0');
    }
    public function getUserList(){
        $userList = "
            
        ";
    }
}
