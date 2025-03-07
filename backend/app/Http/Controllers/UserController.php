<?php
namespace App\Http\Controllers;

use App\Models\User; 
use App\Services\UserService;
// use App\Services\FileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $userService;
    // protected $fileService;

    public function __construct(UserService $userService)//, FileService $fileService)
    {
        $this->userService = $userService;
        // $this->fileService = $fileService;
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

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="User login",
     *     tags={"Authentication"},
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         required=true,
     *         description="User's email address",
     *         @OA\Schema(type="string", format="email", example="alice@example.com")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         required=true,
     *         description="User's password",
     *         @OA\Schema(type="string", format="password", example="password123")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Validation error"),
     *     @OA\Response(response=401, description="Invalid credentials")
     * )
     */
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

    public function logout(Request $request)
    {
        $request->user()->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json([
            'status' => 200,
            'message' => 'Logout successful'
        ]);
    }

    public function termsCondition(){
        return response()->json($this->userService->getTermsCondition());
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

    public function getUserPhoto(): JsonResponse
    {
        $userId = Auth::id();
        $user = $this->userService->getUserProfile($userId);

        $photoPath = $user->user_photo_path; // Get file path from database

        return response()->json([
            'message' => 'User photo path retrieved successfully',
            'file_url' => $photoPath ? "https://ewsdcloud.s3.ap-southeast-1.amazonaws.com/{$photoPath}" : null
        ], 200);
    }

    public function deleteUserPhoto(): JsonResponse
    {
        $userId = Auth::id();
        $this->userService->deleteUserPhoto($userId);

        return response()->json([
            'message' => 'User photo deleted successfully',
        ], 200);
    }

    public function updateUserPhoto(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'user_photo' => 'required|image|mimes:jpeg,png,jpg', 
        ]);

        $photoPath = $this->userService->updateUserPhoto($userId, $request->file('user_photo'));

        return response()->json([
            'message' => 'User photo updated successfully',
            'photo_path' => $photoPath,
        ], 200);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $userId = Auth::id();

        $data = $request->validate([
            'user_name' => 'sometimes|string|max:255',
            'nickname' => 'sometimes|string|max:255',
            'user_password' => 'sometimes|string|min:8|confirmed',
            'gender' => 'sometimes|in:male,female,other',
        ]);

        if (isset($data['user_password'])) {
            $data['user_password'] = bcrypt($data['user_password']);
        }

        $updatedUser = $this->userService->updateUserProfile($userId, $data);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $updatedUser
        ], 200);
    }

    public function getUserList(){
        return $updatedUser = $this->userService->getUserList();
    }
}