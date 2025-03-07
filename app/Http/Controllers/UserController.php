<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Get all users (Admin only)
   public function index(Request $request)
{
    $query = User::query();
    $requestQuery = $request->query();
    $perPage = $requestQuery['per_page'] ?? 15;

    // Search users by name or email
    if (!empty($requestQuery['search'])) {
        $search = $requestQuery['search'];
        $query->where('name', 'like', '%' . $search . '%')
              ->orWhere('email', 'like', '%' . $search . '%');
    }

    // Sort functionality
    if (!empty($requestQuery['sort_by'])) {
        $orderBy = $requestQuery['sort_by'];
        $orderDirection = $requestQuery['sort_direction'] ?? 'asc';
        $query->orderBy($orderBy, $orderDirection);
    }

    return response()->json($query->paginate($perPage));
}

    // Get a single user (Anyone authenticated)
    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($user);
    }



      public function update(Request $request)
    {
        $user = Auth::user();
         if (!$user instanceof User) {
        return response()->json(['message' => 'User not found'], 404);
    }

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:6',
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }


    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }
}
