<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\JobPosting;
use App\Models\Skill;
use Illuminate\Http\Request;
use App\Http\Resources\JobPostingResource;

class UpdateJobPostingController extends Controller
{
  public function __invoke(
    Request $request,
    Company $company,
    JobPosting $jobPosting
  ) {
    $data = $request->validate([
      'title' => ['required', 'string'],
      'body' => ['required', 'string'],
      'skills'  => ['sometimes','required','array',],
      'skills.*'  => ['sometimes','required','string',]
    ]);
    
    $jobPosting->fill([
      'title'=>$data['title'],
      'body'=>$data['body'],
    ])->save();
  

    $skills = $data['skills'] ?? [];

    $skillids =collect();
    //add any new skills to the skills table
    foreach($skills as $skill){
      $createdSkill =Skill::firstOrCreate(['title'=>$skill]);
      $skillids->push($createdSkill->id);
    }

    //attach new skills that were passed in 
    $jobPosting->skills()->sync($skillids);
   
     return response()->json(['job_posting' => new JobPostingResource($jobPosting)]);
  }
}
