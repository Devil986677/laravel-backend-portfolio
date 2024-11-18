<?php

namespace App\Http\Controllers;

use App\Http\Resources\PlanResource;
use App\Models\Plans;
use Illuminate\Http\Request;

class PlansController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $plans = Plans::where('status', 1)->get();
        return PlanResource::collection($plans);
    }

    // Store a new plan
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|max:255',
            'time_duration' => 'nullable|string',
            'support_type' => 'nullable|string',
            'price' => 'required|numeric',
        ]);

        $plan = Plans::create($request->all());
        return new PlanResource($plan);
    }

    // Show a single plan
    public function show($id)
    {
        $plan = Plans::find($id);

        if (!$plan) {
            return response()->json(['message' => 'Plan not found'], 404);
        }

        return new PlanResource($plan);
    }

    // Update a plan
    public function update(Request $request, $id)
    {
        $plan = Plans::find($id);

        if (!$plan) {
            return response()->json(['message' => 'Plan not found'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|max:255',
            'time_duration' => 'nullable|string',
            'support_type' => 'nullable|string',
            'price' => 'required|numeric',
        ]);

        $plan->update($request->all());
        return new PlanResource($plan);
    }

    // Delete a plan
    public function destroy($id)
    {
        $plan = Plans::findOrfail($id);

        if (!$plan) {
            return response()->json(['message' => 'Plan not found'], 404);
        }

        $plan->delete();
        return response()->json(['message' => 'Plan deleted successfully']);
    }

}
