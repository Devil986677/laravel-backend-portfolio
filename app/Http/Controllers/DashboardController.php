<?php

namespace App\Http\Controllers;

use App\Models\Contacts;
use App\Models\Projects;
use App\Models\Skills;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        $userId = $request->input('id');


        $countUsingSkill = Skills::where('type', '0')->where('created_by', $userId)->count();
        $countLearningSkill = Skills::where('type', '1')->where('created_by', $userId)->count();
        $countOtherSkill = Skills::where('type', '2')->where('created_by', $userId)->count();
        $countContact = Contacts::count();
        $countActiveContact = Contacts::where('status', '1')->count();
        $countActiveProjects = Projects::where('created_by', $userId)->where('status', '1')->count();

        return response()->json([
            'activeProject' => $countActiveProjects,
            'otherSkill' => $countOtherSkill,
            'usingSkills' => $countUsingSkill,
            'activeContact' => $countActiveContact,
            'learningSkills' => $countLearningSkill,
            'contacts' => $countContact,
        ]);
    }


}
