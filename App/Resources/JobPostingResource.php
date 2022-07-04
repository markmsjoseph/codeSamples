<?php

namespace App\Http\Resources;
use App\Http\Resources\SkillsResource;

use Illuminate\Http\Resources\Json\JsonResource;

class JobPostingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
      
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'skills' =>SkillsResource::collection($this->skills),
        ];
    }
}
