<?php
namespace App\Http\Controllers;

use App\Models\User; // Make sure to import the User model
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
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

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ], [
            'email.required' => 'The email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'The password is required.',
            'password.min' => 'The password must be at least 6 characters.',
        ]);

        $response = $this->userService->login($validatedData);

        return response()->json($response, $response['status']);
    }

    public function show()
    {
        $userId = Auth::id(); // Get logged-in user ID
        return response()->json($this->userService->getUserProfile($userId));
    }

    public function update(Request $request)
    {
        $userId = Auth::id();
        
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $userId,
            'password' => 'sometimes|string|min:8|confirmed',
        ]);

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']); // Hash password before storing
        }

        return response()->json($this->userService->updateUserProfile($userId, $data));
    }


    public function dashboard(){

    }
}