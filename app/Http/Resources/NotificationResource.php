<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
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
            'notification for user' => $this->notifiable_id,
            'data' => [
                'blog title' => $this->data["blog_title"],
                'author name' => $this->data["author_name"],
                'author email' => $this->data["author_email"],
                'blog link' => $this->data["blog_link"],
            ],
            'created at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
