<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Http\Request;



class RegisterController extends Controller
{
    
    public function register(Request $request){
        /**
        * @OA\Post(path="/api/auth/register",
        *   tags={"auth"},
        *   summary="Register user",
        *   description="Register a user",
        *   operationId="registerUser",
        * @OA\RequestBody(
        *    required=true,
        *    description="User email, name, password, device_name are needed to register",
        *    @OA\JsonContent(
        *       required={"email", "name", "password"},
        *       @OA\Property(property="email", type="string", format="email", ref="#/components/schemas/User/properties/email"),
        *       @OA\Property(property="name", type="string", ref="#/components/schemas/User/properties/name"),
        *       @OA\Property(property="password", type="string", format="password", ref="#/components/schemas/User/properties/password"),
        *       @OA\Property(property="device_name", type="string", example="ios"),
        *    ),
        * ),
        *  @OA\Response(
        *    response=200,
        *    description="Success",
        *    @OA\JsonContent(
        *       @OA\Property(property="token", type="string"),
        *       @OA\Property(property="name", type="string", ref="#/components/schemas/User/properties/name"),
        *       @OA\Property(property="email", type="string", ref="#/components/schemas/User/properties/email"),
        *       @OA\Property(property="created_at", type="date-time", ref="#/components/schemas/User/properties/created_at"),
        *       
        
        *        )
        *     ),
        *   @OA\Response(
        *    response=400,
        *    description="error",
        *    @OA\JsonContent(
        *       @OA\Property(property="message", type="string", example="Veuillez remplir tous les champs ou verifier que votre mot de passe contient bien au moins 8 caractères"),
        *        )
        *     ),
        *   @OA\Response(
        *    response=409,
        *    description="error",
        *    @OA\JsonContent(
        *       @OA\Property(property="message", type="string", example="Votre compte existe déjà"),
        *        )
        *     ),
        * )
        */

        if(!$request->name || !$request->email || !$request->password || strlen($request->password) < 8){
            return response()->json([
                "success"=> false,
                "message"=> "Veuillez remplir tous les champs ou verifier que votre mot de passe contient bien au moins 8 caractères"
            ], 400);
        }

        $userExist = User::where('email', $request->email)->exists();

        if ($userExist) {
            return response()->json([
                "message" => "Votre compte existe déjà"
            ], 409);
        }

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'name' => $request->name
        ]);


        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            "token" => $token,
            "name" => $user->name,
            "email" => $user->email,
            "created_at" => $user->created_at
        ], 200);
    }
}
