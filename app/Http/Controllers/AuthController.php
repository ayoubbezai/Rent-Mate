<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class AuthController extends Controller
{
public function register(Request $request){
    $data = $request->validate([
        "name" => "required|string",
        "email" => "required|string|email|unique:users",
        "password" => "required|min:6",
        "role_name" => "nullable|string|exists:roles,name",
    ]);

    // Prevent users from registering as admin
    if ($request->role_name === "admin") {
        return response()->json([
            "message" => "You cannot register as an admin."
        ], 403);
    }

    // Assign role (default to 'user' if not provided)
    $role_name = $request->role_name ?? "user";
    $role = Role::where("name", $role_name)->first();

    if (!$role) {
        return response()->json([
            "message" => "Invalid role provided."
        ], 400);
    }

    $user = User::create([
        "name" => $data["name"],
        "email" => $data["email"],
        "password" => Hash::make($data["password"]),
        "role_id" => $role->id,
    ]);

    $token = $user->createToken("auth_token")->plainTextToken;

    return response()->json([
        "user" => $user,
        "token" => $token,
        "user_role" => $role_name
    ], 201);
}

public function login(Request $request){
    $data = $request->validate([
            "email" => "required|email|exists:users",
            "password" => "required|min:6",

    ]);

$user = User::where("email", $data["email"])->first();

    if(!$user || !Hash::check($data["password"], $user->password)){
            return response(["success" => false, "message" => "Invalid credentials"], 401);
    }
            $token = $user->createToken("auth_token")->plainTextToken;
            $role_name = $user->role->name;
             return response()->json(["user" => $user, "token" => $token]);


}
public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        "message" => "Logged out successfully"
    ], 200);
}
    
}

