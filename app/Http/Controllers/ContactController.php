<?php

namespace App\Http\Controllers;

use App\Http\Resources\ContactResource;
use App\Models\Contacts;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Contacts::all();
        return ContactResource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:contacts,email',
            'phone' => 'required|string|max:15',
            'description' => 'required|string',
            // Add other fields and validation as necessary
        ]);

        // Create a new contact
        $contact = Contacts::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'description' => $validatedData['description'],
        ]);
        return response()->json(['message' => 'Data saved successfully'], 200);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Contacts::findOrFail($id);
        return new ContactResource($data);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
//        dd($request->all());
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:contacts,email,' . $id,
            'phone' => 'required|string|max:15',
            'description' => 'required|string',
            'status'=>'required'

        ]);

        $contact = Contacts::findOrFail($id);
        $contact->update([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'description' => $validatedData['description'],
            'status' => $validatedData['status'],
        ]);
        return response()->json(['message' => 'Data updated successfully'], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $contact = Contacts::findOrFail($id);
        $contact->delete();
        return response()->json(['message' => 'Data deleted successfully'], 200);
    }


}
