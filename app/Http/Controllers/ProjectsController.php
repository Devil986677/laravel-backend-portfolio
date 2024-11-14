<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectResource;
use App\Models\Projects;
use Illuminate\Http\Request;

class ProjectsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Projects::orderBY('id', 'desc')->get();
        return ProjectResource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

//        dump($request->all());
//        _dd($request->all());
//        dd($request->all());
        if ($request->hasFile("images")) {
            $file = $request->file('images');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = 'uploads/images/projects/';

            $file->move(public_path($filePath), $fileName);

            $imagePath = $filePath . $fileName;
            $request->merge(['image' => $imagePath]);
        }

        $row = Projects::create($request->all());
        if ($row) {
            return response()->json([
                'success' => true,
                'message' => 'Project Created Successfully',
                'data' => new ProjectResource($row)
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Project Creation failed'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
        public function update(Request $request, $id)
    {
//        _dd($request->all());
        $row = Projects::find($id);

        if (!$row) {
            return response()->json([
                'success' => false,
                'message' => 'Skills not found'
            ], 404);
        }

        if ($request->hasFile("images")) {
            $file = $request->file('images');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = 'uploads/images/projects/';


            $file->move(public_path($filePath), $fileName);


            $imagePath = $filePath . $fileName;


            $request->merge(['image' => $imagePath]);


            if ($row->image && file_exists(public_path($row->image))) {
                unlink(public_path($row->image));
            }
        }


//        _dd($request->all());

        $row->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Skills updated successfully',
            'data' => new ProjectResource($row)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Find the skill record by its ID
        $row = Projects::find($id);

        if (!$row) {
            return response()->json(['error' => 'Project not found'], 404);
        }


        if ($row->image && file_exists(public_path($row->image))) {
            unlink(public_path($row->image)); // Delete the image file
        }

        if ($row->delete()) {
            return response()->json(['success' => 'Project deleted successfully']);
        } else {
            return response()->json(['error' => 'Project deletion failed'], 500);
        }
    }

}
