<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Properties;
use Illuminate\Support\Facades\Auth;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = Properties::query();
        $request_query = $request->query();
        $perPage = $request_query['per_page'] ?? 15;

        if(!empty($request_query['search'])){
            $search = $request_query['search'];
            $data->where(function($query)use($search){
                $query->where('title','like','%'.$search.'%')
                ->orWhere('title','like','%'.$search.'%')
                ->orWhere('description','like','%'.$search.'%')
                ->orWhere('location','like','%'.$search.'%');
            
           });
        };

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
    $fields = $request->validate([
        'type' => 'required|string|max:255',
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'status' => 'required|string|in:available,sold,rented', // Example statuses
        'location' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'start_date' => 'required|date|after_or_equal:today',
        'end_date' => 'required|date|after:start date',
    ]);

    // Create the property
$fields['user_id'] = Auth::id();

    $property = Properties::create($fields);

    return response()->json($property, 201);
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
         $property = Properties::find($id);
        if(!$property){
            return response()->json([
                "success" => false,
                "data" => null,
                "message" =>"property not found"
            ],404);
        }
        return response()->json([
            "success" => true,
            "data" => $property,
                
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
            $property = Properties::find($id);
        $fields = $request->validate([
        'type' => 'required|string|max:255',
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'status' => 'required|string|in:available,sold,rented', // Example statuses
        'location' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'start_date' => 'required|date|after_or_equal:today',
        'end_date' => 'required|date|after:start_date',
    ]);

    $property->update($fields);

     if(!$property){
            return response()->json([
                "success" => false,
                "data" => null,
                "message" =>"property not found"
            ],404);
        }
        return response()->json([
            "success" => true,
            "data" => $property,
                
        ]);


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $property = Properties::find($id);
        if(!$property){
            return response()->json([
                "success" => false,
                "data" => null,
                "message" =>"property not found"
            ],404);
        }
         $property->delete();
        return response(status:204);
    }
}
