<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Requests;
use App\Models\Properties;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
           $data = Requests::query()->with(['user',"landlord"]);
        $request_query = $request->query();
        $perPage = $request_query['per_page'] ?? 15;

       if (!empty($request_query['status'])) {
        $data->where('status', $request_query['status']);
    }

    if (!empty($request_query['search'])) {
        $search = $request_query['search'];
        $data->whereHas('user', function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        })->orWhereHas('landlord', function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        })->orWhere('content', 'like', '%' . $search . '%');
    }
        if(!empty($request_query['sort_by'])){
            $orderBy = $request_query['sort_by'];
            $orderDirection = "asc";
            if(!empty($request_query['sort_direction'])){
                $orderDirection = $request_query['sort_direction'];
            }
                        $data->orderBy($orderBy,$orderDirection);
        }
        return response()->json($data->paginate($perPage));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
         $fields = $request->validate([
        'property_id' => 'required|exists:properties,id',
        'content' => 'required|string',
        'status' => 'required|string|in:pending,approved,rejected'
    ]);
    $user_id = Auth::id();
    $property = Properties::findOrFail($fields['property_id']);
    $landlord_id = $property->user_id;

    $request = Requests::create([
        "user_id"=> $user_id ,
        "landlord_id" => $landlord_id,
        'status' => $fields['status'],
        'content' => $fields['content']
    ]);
     return response()->json([
        "success" => true,
        "message" => "Rental request submitted successfully.",
        "request" => $request
    ], 201);
    

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $request = Requests::find($id);
        if(!$request){
            return response()->json([
                  "success" => false,
                "data" => null,
                "message" =>"request not found"
            ],404);
        }
         return response()->json([
            "success" => true,
            "data" => $request,
                
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
                $request2 = Requests::find($id);
                 $fields = $request->validate([
        'property_id' => 'required|exists:properties,id',
        'content' => 'required|string',
        'status' => 'required|string|in:pending,approved,rejected'
    ]);

     if(!$request2){
            return response()->json([
                  "success" => false,
                "data" => null,
                "message" =>"request not found"
            ],404);
        }

        $request2->update($fields);
         return response()->json([
            "success" => true,
            "data" => $request,
                
        ]);


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $request2 = Requests::find($id);
         if(!$request2){
            return response()->json([
                  "success" => false,
                "data" => null,
                "message" =>"request not found"
            ],404);
        }

        $request2->delete();
        return response(status:204);

    }
}
