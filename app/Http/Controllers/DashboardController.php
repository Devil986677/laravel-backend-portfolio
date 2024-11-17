<?php

namespace App\Http\Controllers;

use App\Models\Contacts;
use App\Models\Projects;
use App\Models\Skills;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function index(){

        $countUsingSkill = Skills::where('type','0')->count();
        $countLearningSkill= Skills::where('type','1')->count();
        $countOtherSkill= Skills::where('type','2')->count();
        $countContact = Contacts::count();
        $countActiveContact = Contacts::where('status','1')->count();
        $countActiveProjects = Projects::where('status','1')->count();
        return response()->json(['activeProject'=>$countActiveProjects,
            'otherSkill'=>$countOtherSkill,'usingSkills' => $countUsingSkill,
            'activeContact'=>$countActiveContact,
            'learningSkills'=> $countLearningSkill,
            'contacts' => $countContact,
        ]);
    }

}
