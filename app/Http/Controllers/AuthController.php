<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register() {
        $validator = Validator::make(request()->all(), [
            'name' => 'required|unique:users,name',  
            'password' => 'required',
        ]);
      
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
      
        $existingUser = User::where('name', request()->name)->first();
        if ($existingUser) {
            return response()->json(['error' => 'The name is already taken.'], 400); 
        }
    
        $user = new User;
        $user->id = Str::uuid();
        $user->name = request()->name;
        $user->password = bcrypt(request()->password);
        $user->save();
      
        $token = auth()->attempt(request(['name', 'password'])); 
      
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user_name' => $user->name,
            'max_score' => $user->max_score, 
        ], 201);
    }

    public function login()
    {
        $credentials = request(['name', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
  
        return $this->respondWithToken($token);
    }


    public function updateMaxScore()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validator = Validator::make(request()->all(), [
            'max_score' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user->max_score = request()->max_score;
        $user->save();

        return response()->json([
            'message' => 'Max score updated successfully',
            'user' => $user
        ], 200);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user_name' => auth()->user()->name,
            'max_score' => auth()->user()->max_score
        ]);
    }



    
}
