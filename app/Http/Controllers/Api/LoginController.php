<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * The index function
     *
     * @return void
     */
    public function index(Request $request)
    {

        // Catch the error from validator
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The data is invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Retrieve the user from the database filtered by email
        $user = User::where('email', $request->email)->first();

        // Check if the password matches. Display a response error when
        // the password does not match
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid',
                'errors' => [
                    'password' => 'The password does not belong to the user account.',
                ],
            ], 422);
        }

        // Return the access token if the checking is successful
        return response()->json([
            'access_token' => $user->createToken('api-token')->plainTextToken,
            'type' => 'bearer',
        ], 200);
    }
}
