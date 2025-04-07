<?php
namespace App\Http\Controllers;

use App\Models\User; 
use App\Services\UserService;
use App\Repositories\UserRepository;
// use App\Services\FileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $userService;
    protected $userRepository;
    // protected $fileService;

    public function __construct(UserService $userService, UserRepository $userRepository)//, FileService $fileService)
    {
        $this->userService = $userService;
        $this->userRepository = $userRepository;
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

    public function show()
    {
        $userId = Auth::id(); // Get logged-in user ID
        return response()->json($this->userService->getUserProfile($userId));
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
            'photo_path' => "https://ewsdcloud.s3.ap-southeast-1.amazonaws.com/{$photoPath}",
        ], 200);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $userId = Auth::id();

        $data = $request->validate([
            'user_name' => 'sometimes|string|max:255',
            'nickname' => 'sometimes|string|max:255',
            'gender' => 'sometimes|in:1,2',
            'date_of_birth' => 'sometimes|date',   
            'phone_number' => 'sometimes|string|max:20', 
        ]);

        // echo string($data);

        $updatedUser = $this->userService->updateUserProfile($userId, $data);

        return response()->json([
            'message' => 'Profile updated successfully',
            'updated_user' => $updatedUser
        ], 200);
    }    
    
    public function editUser(Request $request)
    {
        try {
            $data = $request->validate([
                'id'   => 'required|string|exists:users,id',
                'user_name' => 'sometimes|string|max:255',
                'nickname' => 'sometimes|string|max:255',
                'user_type_id' => 'sometimes|uuid|exists:user_types,user_type_id',
                'faculty_id'   => 'sometimes|uuid|exists:faculties,faculty_id',
                'gender' => 'sometimes|in:1,2',
                'date_of_birth' => 'sometimes|date',  
                'phone_number' => 'sometimes|string|max:20', 
            ]);

            $updatedUser = $this->userService->editUser($data);

            return response()->json([
                'message' => 'Profile updated successfully',
                'updated_user' => $updatedUser
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $userId = Auth::id();

            $data = $request->validate([
                'user_password' => 'required|string|min:8|confirmed',
            ]);

            $data['user_password'] = bcrypt($data['user_password']);
            $updatedUser = $this->userService->updateUserProfile($userId, $data);

            return response()->json([
                'message' => 'Password updated successfully',
                'user' => $updatedUser
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getActiveUserList(Request $request){
        return $this->userService->getActiveUserList($request);
    }
    
    public function getUserList()
    {
        $users = $this->userService->getAllUserList();
        return response()->json($users);
    }

    public function getUserListByType($userType)
    {
        $users = $this->userService->getUserListByType($userType);
        return response()->json($users);
    }

    public function resetPassword(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id'   => 'required|string|exists:users,id',
            ]);

            $result = $this->userService->resetPassword($validated['user_id']);

            if (!$result) {
                return response()->json(['message' => 'User not found'], 404);
            }

            return response()->json(['message' => 'Password reset successful. Email sent.']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function pageVisitInitial($pageId){
        $user = Auth::user();
        $this->userService->pageVisitInitial($user->id, $pageId);
        return response()->json([
            'message' => 'Page visit recorded successfully',
        ], 200);
    }

    public function userRegister(Request $request){
        try {
            $validated = $request->validate([
                'user_name'       => 'required|string|max:255',
                'nickname'        => 'sometimes|string|max:100',
                'user_email'      => 'required|email|max:255|unique:users,user_email',
                'user_type_id'    => 'required|exists:user_types,user_type_id',
                'faculty_id'      => 'required|uuid|exists:faculties,faculty_id',
                'gender'          => 'required|in:1,2', 
                'date_of_birth'   => 'sometimes|date|before:today',
                'phone_number'    => 'sometimes|string|max:20'
            ]);

            return response()->json(['new user id' => $this->userService->userRegister($validated)]);

        }catch (\Exception $e) {
            return response()->json([
                'message' => 'Error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function userLastLogin()
    {
        $userId = Auth::id();
        
        return response()->json(['latest login time' => $this->userService->userLastLogin($userId)]);
    }

    public function getUserById($userId)
    {
        $result = $this->userRepository->getUserById($userId);

        return response()->json($result);
    }
}