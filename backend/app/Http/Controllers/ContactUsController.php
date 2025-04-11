<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ContactUsService;

class ContactUsController extends Controller
{
    protected $contactUsService;

    public function __construct(ContactUsService $contactUsService) {
        $this->contactUsService = $contactUsService;
    }

    public function createContactUs(Request $request){
        try {
            $validated = $request->validate([
                'visitor_name' => 'required|string|max:225',
                'visitor_email' => 'required|email|max:225',
                'title' => 'required|string|max:225',
                'description' => 'required|string'
            ]);

            return response()->json(['new contact us id' => $this->contactUsService->createContactUs($validated)]);

        }catch (\Exception $e) {
            return response()->json([
                'message' => 'Error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getContactUsList(){
        return response()->json($this->contactUsService->getContactUsList());
    }
}
