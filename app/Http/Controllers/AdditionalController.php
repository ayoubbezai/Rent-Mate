<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Properties;
use App\Models\Requests;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class AdditionalController extends Controller
{
   public function statistics()
{
    return response()->json([
        'properties' => [
            'total' => Properties::count(),
            'available' => Properties::where('status', 'available')->count(),
            'rented' => Properties::where('status', 'rented')->count(),
            'sold' => Properties::where('status', 'sold')->count(),
        ],
        'requests' => [
            'total' => Requests::count(),
            'pending' => Requests::where('status', 'pending')->count(),
            'approved' => Requests::where('status', 'approved')->count(),
            'rejected' => Requests::where('status', 'rejected')->count(),
        ],
        'users' => [
            'total' => User::count(),
            'admins' => User::whereHas('role', function ($query) {
                $query->where('name', 'admin');
            })->count(),
            'landlords' => User::whereHas('role', function ($query) {
                $query->where('name', 'landlord');
            })->count(),
            'regular_users' => User::whereHas('role', function ($query) {
                $query->where('name', 'user');
            })->count(),
            'new_this_month' => User::whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->count(),
        ],
    ]);
}

public function properties(){
     $user = Auth::user();
         if (!$user instanceof User) {
        return response()->json(['message' => 'User not found'], 404);
    }
      if (!$user->hasRole('landlord')) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }
     $properties = Properties::where('user_id', $user->id)->paginate(10);

    return response()->json([
        "success"=>true,
        "data" => $properties
        
    ],200);

}
public function requests(){
     $user = Auth::user();
         if (!$user instanceof User) {
        return response()->json(['message' => 'User not found'], 404);
    }
      
     $data = Requests::where('user_id', $user->id)->with("landlord")->paginate(10);



    return response()->json([
        "success"=>true,
        "data" => $data
        
    ],200);

}

}
