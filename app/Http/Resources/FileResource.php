<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'file_name' => $this->file_name,

            'size' => $this->size,


            'visibility' => $this->visibility,

            'downloads_count' => $this->downloads_count,

            'uploaded_at' => $this->created_at,

            'folder' => $this->whenLoaded('folder', function () {
                return [
                    'id' => $this->folder->id,
                    'name' => $this->folder->name
                ];
            }),

            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name
                ];
            }),
        ];
    }
}