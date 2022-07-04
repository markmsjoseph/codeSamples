<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Skill extends Model
{
    use HasFactory;
    
    protected $guarded = [];
    
    public function jobPostings(): BelongsToMany
    {
        return $this->belongsToMany(JobPosting::class);
    }
}
