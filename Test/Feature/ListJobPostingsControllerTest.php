<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\JobPosting;
use App\Models\Skill;
use function Pest\Laravel\getJson;

it('returns an empty array when the company has no job postings', function () {
  $company = Company::factory()->create();

  getJson(route('api.companies.job-postings.index', [$company]))
    ->assertOk()
    ->assertExactJson([
      'job_postings' => [],
    ]);
});

it('returns only the job postings for a specified company', function () {
  $company = Company::factory()
    ->has(JobPosting::factory()->count(2))
    ->create();

  [$jobOne, $jobTwo] = $company->jobPostings()->get();

  // populate random unrelated jobs
  JobPosting::factory()->count(3);

  getJson(route('api.companies.job-postings.index', [$company]))
    ->assertOk()
    ->assertJsonCount(2, 'job_postings')
    ->assertJson([
      'job_postings' => [['id' => $jobOne->id], ['id' => $jobTwo->id]],
    ]);
});

it('includes the related skills with each job posting', function () {
  //create related skills
  $company = Company::factory()
    ->has(JobPosting::factory())
    ->create();

  $skills = Skill::factory()->count(2)->create();

  [$skillOne, $skillTwo] = $skills;


  $jobOne = $company->jobPostings()->first();

  $jobOne->skills()->attach($skills);

  //create random skills
  Skill::factory()->count(3)->create();

  getJson(route('api.companies.job-postings.index', [$company]))
    ->assertOk()

    ->assertJsonCount(1, 'job_postings')
    ->assertJson([
      'job_postings' => [
        [
          'id' => $jobOne->id,
          'title' => $jobOne->title,
          'skills' => [$skillOne->title,  $skillTwo->title],
        ]
      ]

    ]);;
});




it(
  'has skills as an empty array when the job posting has no skills attached',
  function () {
    $company = Company::factory()
      ->has(JobPosting::factory())
      ->create();

    $jobOne = $company->jobPostings()->first();

    getJson(route('api.companies.job-postings.index', [$company]))
      ->assertOk()
      ->assertJsonCount(1, 'job_postings')
      ->assertExactJson([
        'job_postings' => [
          [
            'id' => $jobOne->id,
            'title' => $jobOne->title,
            'body' => $jobOne->body,
            'skills' => [],
          ]
        ]

      ]);
  }
);
