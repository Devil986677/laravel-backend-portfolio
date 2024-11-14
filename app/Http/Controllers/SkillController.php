<?php

namespace App\Http\Controllers;

use App\Http\Resources\SkillsResource;
use App\Models\Skills;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Skills::orderBY('id', 'desc')->get();
        return SkillsResource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->hasFile("images")) {
            $file = $request->file('images');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = 'uploads/images/skills/';

            $file->move(public_path($filePath), $fileName);

            $imagePath = $filePath . $fileName;
            $request->merge(['image' => $imagePath]);
        }

        $row = Skills::create($request->all());
        if ($row) {
            return response()->json([
                'success' => true,
                'message' => 'Skills Created Successfully',
                'data' => new SkillsResource($row)
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Skills Creation failed'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Skills::findOrFail($id);
        return new SkillsResource($data); // Return a single resource instead of a collection
    }


    /**
     * Update the specified resource in storage.
     */
//    public function update(Request $request, $id)
//    {
//        _dd($request->all());
//        $row = Skills::find($id);
//
//        if (!$row) {
//            return response()->json([
//                'success' => false,
//                'message' => 'Skills not found'
//            ], 404);
//        }
//
//        if ($request->hasFile("images")) {
//            $file = $request->file('images');
//            $fileName = time() . '_' . $file->getClientOriginalName();
//            $filePath = 'uploads/images/skills/';
//
//
//            $file->move(public_path($filePath), $fileName);
//
//
//            $imagePath = $filePath . $fileName;
//
//
//            $request->merge(['image' => $imagePath]);
//
//
//            if ($row->image && file_exists(public_path($row->image))) {
//                unlink(public_path($row->image));
//            }
//        }
//
//
//        _dd($request->all());
//
//        $row->update($request->all());
//
//        return response()->json([
//            'success' => true,
//            'message' => 'Skills updated successfully',
//            'data' => new SkillsResource($row)
//        ]);
//    }
    public function update(Request $request, $id)
    {
//        _dd($request->all());
        // Ensure the request handles multipart form data
        $validatedData = $request->validate([
            'name' => 'required|string',
            'type' => 'required|integer',
            'image' => 'nullable|file|image|max:1024', // Check if image is an optional file
        ]);

        $skill = Skills::findOrFail($id);
        $skill->name = $validatedData['name'];
        $skill->type = $validatedData['type'];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('skills_images', 'public'); // Store image and save path
            $skill->image = $path;
        }

        $skill->save();

        return response()->json($skill, 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Find the skill record by its ID
        $row = Skills::find($id);

        if (!$row) {
            return response()->json(['error' => 'Skill not found'], 404);
        }


        if ($row->image && file_exists(public_path($row->image))) {
            unlink(public_path($row->image)); // Delete the image file
        }

        if ($row->delete()) {
            return response()->json(['success' => 'Skill deleted successfully']);
        } else {
            return response()->json(['error' => 'Skill deletion failed'], 500);
        }
    }


}
