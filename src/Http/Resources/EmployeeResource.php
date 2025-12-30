<?php

declare(strict_types=1);

namespace Gomu\Auth\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'username' => $this->user->username,
                'email' => $this->user->email,
                'user_type' => $this->user->user_type,
            ],
            'nip' => $this->nip,
            'nik' => $this->nik,
            'full_name' => $this->full_name,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'phone_number' => $this->phone_number,
            'personal_email' => $this->personal_email,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'district' => $this->district,
            'village' => $this->village,
            'join_date' => $this->join_date,
            'termination_date' => $this->termination_date,
            'department' => $this->whenLoaded('department', function () {
                return [
                    'id' => $this->department->id,
                    'name' => $this->department->name,
                    'code' => $this->department->code,
                ];
            }),
            'job_level' => $this->whenLoaded('jobLevel', function () {
                return [
                    'id' => $this->job_level->id,
                    'name' => $this->job_level->name,
                    'level' => $this->job_level->level,
                ];
            }),
            'job_position' => $this->whenLoaded('jobPosition', function () {
                return [
                    'id' => $this->job_position->id,
                    'name' => $this->job_position->name,
                    'code' => $this->job_position->code,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}