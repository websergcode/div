<?php

namespace App\Http\Resources\UserApplicationForm;

use App\Models\UserApplicationForm\UserApplicationForm;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserApplicationFormResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var UserApplicationForm $this */

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status,
            'message' => $this->message,
            'comment' => $this->comment,
            'created_at' => $this->created_at->format('Y-m-d H:m:i')
        ];
    }
}
