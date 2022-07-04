<?php

use App\Models\JobPosting;
use function Pest\Laravel\putJson;
use App\Models\Skill;


it('should require a title and body to update a job', function () {
  $job = JobPosting::factory()->create();

  putJson(route('api.companies.job-postings.update', [$job->company, $job]), [
    'title' => '',
    'body' => '',
  ])
    ->assertJsonValidationErrorFor('title')
    ->assertJsonValidationErrorFor('body');
});

it('updates a jobs title and body', function () {
  $job = JobPosting::factory()->create();

  putJson(route('api.companies.job-postings.update', [$job->company, $job]), [
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

  expect($job->refresh())
    ->title->toBe('Full Stack Developer')
    ->body->toBe('Please join our team!');
});

it(
  'does not require any skills but enforces that they are an array of strings when present',
  function () {
    $job = JobPosting::factory()->create();

    putJson(route('api.companies.job-postings.update', [$job->company, $job]), [
      'title' => 'Full Stack Developer',
      'body' => 'Please join our team!',
      'skills' => 'php'
    ])
      ->assertJsonValidationErrorFor('skills');
  }
);



it(
  'creates a skill in the database if one does not exist with the same name',
  function () {
    $job = JobPosting::factory()->create();

    $this->assertDatabaseMissing('skills', ['title' => 'php']);

    putJson(route('api.companies.job-postings.update', [$job->company, $job]), [
      'title' => 'Full Stack Developer',
      'body' => 'Please join our team!',
      'skills' => ['php']
    ])->assertOk();


    $this->assertDatabaseHas('skills', ['title' => 'php']);
  }
);

it(
  'reuses a skill from the database if one with the same name already exists',
  function () {
    $job = JobPosting::factory()->create();
    $this->assertDatabaseCount('skills', 0);
    Skill::factory()->create(['title' => 'php']);

    putJson(route('api.companies.job-postings.update', [$job->company, $job]), [
      'title' => 'Full Stack Developer',
      'body' => 'Please join our team!',
      'skills' => ['php']
    ])->assertOk();

    $this->assertDatabaseHas('skills', ['title' => 'php'])
      ->assertDatabaseCount('skills', 1);
  }
);



it(
  'removes any skills that were previously on the job posting but were removed from the array in this request',
  function () {
    $job = JobPosting::factory()->create();
    $createdSkills = Skill::factory()->create(['title' => 'php']);
    $job->skills()->attach($createdSkills);

    //check that a skill is with associated job posting
    expect($job->first()->skills->pluck('title')->toArray())->toBe(['php']);

    putJson(route('api.companies.job-postings.update', [$job->company, $job]), [
      'title' => 'Full Stack Developer',
      'body' => 'Please join our team!',
      'skills' => ['php']
    ])->assertOk();

    //check that the skill is no longer associated with the posting
    expect($job->first()->skills->pluck('title')->toArray())->not->toContain(['php']);
  }
);
