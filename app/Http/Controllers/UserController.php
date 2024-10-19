<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function getAllUsers(){
        $users = User::all();
        return response()->json($users);
    }

    public function createUser(Request $request){
        
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'color' => 'required',
            'max_score' => 'required',
        ]);

        if($validator->fails()){
            $response = [
                'message' => 'Error en la validación de datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            
            return response()->json($response, 400);
        }


        $user = User::create([
            'id' => Str::uuid(),
            'name' => $request->name,
            'color' => $request->color,
            'max_score' => $request->max_score,
        ]);

        if(!$user){
            $response = [
                'message' => 'Error al crear al usuario',
                'status' => 500
            ];

            return response()->json($response, 500);
        };
        
        $response = [
            'user' => $user,
            'status' => 201
        ];

        return response()->json($response, 201);

    }
}
