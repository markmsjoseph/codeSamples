<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Skill;
use Illuminate\Http\Request;
use App\Http\Resources\JobPostingResource;

class StoreJobPostingController extends Controller
{
  public function __invoke(Request $request, Company $company)
  {

    $data = $request->validate([
      'title' => ['required', 'string'],
      'body' => ['required', 'string'],
      'skills'  => ['sometimes','required','array',],
      'skills.*'  => ['sometimes','required','string',]
    ]);
    
  
    $jobPosting = $company->jobPostings()->create([
      'title'=>$data['title'],
      'body'=>$data['body'],
    ]);


    $skills = $data['skills'] ?? [];
    
    //add any new skills to the skills table
    $skillids =collect();
    foreach($skills as $skill){
      $createdSkill =Skill::firstOrCreate(['title'=>$skill]);
      $skillids->push($createdSkill->id);
    }
    //attach new skills that were passed in 
    $jobPosting->skills()->sync($skillids);

    return response()->json(['job_posting' => new JobPostingResource($jobPosting)]);
  }
}
 