<?php
namespace App\Http\Controllers;

use App\Models\User; // Make sure to import the User model
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get all users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }
}
