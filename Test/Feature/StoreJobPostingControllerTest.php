<?php

use App\Models\Company;
use App\Models\JobPosting;
use App\Models\Skill;

use function Pest\Laravel\postJson;

it('should require a title and body to create a job', function () {
  $company = Company::factory()->create();

  postJson(route('api.companies.job-postings.store', [$company]), [
    'title' => '',
    'body' => '',
  ])
    ->assertJsonValidationErrorFor('title')
    ->assertJsonValidationErrorFor('body');
});

it('creates a job with only a title and body', function () {
  $company = Company::factory()->create();

  postJson(route('api.companies.job-postings.store', [$company]), [
    'title' => 'Full Stack Developer',
    'body' => 'Please join our team!',
  ])
    ->assertOk()
    ->assertJson([
      'job_posting' => [
        'title' => 'Full Stack Developer',
        'body' => 'Please join our team!',
      ],
    ]);

  expect($company->jobPostings)->toHaveCount(1);
});

it('does not require any skills but enforces that they are an array of strings when present', function () {
  $company = Company::factory()->create();

  postJson(route('api.companies.job-postings.store', [$company]), [
    'title' => 'Full Stack Developer',
    'body' => 'Please join our team!',
    'skills' => 'php'
  ])
    ->assertJsonValidationErrorFor('skills');
});



it(  'creates a skill in the database if one does not exist with the same name',
  function () {
    $company = Company::factory()->create();

    $this->assertDatabaseMissing('skills', ['title' => 'php']);

    postJson(route('api.companies.job-postings.store', [$company]), [
      'title' => 'Full Stack Developer',
      'body' => 'Please join our team!',
      'skills' => ['php']
    ])->assertOk();


    $this->assertDatabaseHas('skills', ['title' => 'php']);
  }
);

it(  'reuses a skill from the database if one with the same name already exists',
  function () {
    $company = Company::factory()->create();
    $this->assertDatabaseCount('skills', 0);
    Skill::factory()->create(['title' => 'php']);

    postJson(route('api.companies.job-postings.store', [$company]), [
      'title' => 'Full Stack Developer',
      'body' => 'Please join our team!',
      'skills' => ['php']
    ])->assertOk();

    $this->assertDatabaseHas('skills', ['title' => 'php'])
      ->assertDatabaseCount('skills', 1);
  }
);


it(
  'associates only the skills passed in the request with the job posting',
  function () {
    $company = Company::factory()->create();
    Skill::factory()->create(['title' => 'php']);

    postJson(route('api.companies.job-postings.store', [$company]), [
      'title' => 'Full Stack Developer',
      'body' => 'Please join our team!',
      'skills' => ['php']
    ])->assertOk();

    expect($company->jobPostings->first()->skills->pluck('title')->toArray())->toBe(['php']);
  }
);
