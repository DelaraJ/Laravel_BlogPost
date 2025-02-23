<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class Notification extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'data' => 'array',
        ];
    }

    public static function index(): JsonResponse
    {
        $notifications = Notification::where('notifiable_id', '=', Auth::user()->id)->paginate(5);
        return response()->json([
            'data' => NotificationResource::collection($notifications),
            'meta' => [  
                'current_page' => $notifications->currentPage(),  
                'last_page' => $notifications->lastPage(),  
                'per_page' => $notifications->perPage(),  
                'total' => $notifications->total(),  
            ]
        ]);
    }
}
