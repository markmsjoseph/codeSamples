<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Resources\JobPostingResource;

class ListJobPostingsController extends Controller
{
  public function __invoke(Request $request, Company $company)
  {
    return response()->json([
      'job_postings' => JobPostingResource::collection($company->jobPostings),
    ]);
  }
}
