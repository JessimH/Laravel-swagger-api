<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *
 * @OA\Schema(
 * @OA\Xml(name="Task"),
 * @OA\Property(property="id", type="integer", example="1"),
 * @OA\Property(property="body", type="string", example="Test Task", description="Task"),
 * @OA\Property(property="user_id", type="int", description="User id", example="1"),
 * @OA\Property(property="completed", type="bool", description="Complete the task", example="true"),
 * @OA\Property(property="created_at", type="string", format="date-time", example="2021-12-12 12:00:00"),
 * @OA\Property(property="updated_at", type="string", format="date-time", example="2021-12-12 12:00:00"),
 * )
 *
 * Class Task
 *
 */
class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'body',
        'user_id'
    ];
    protected $hidden = [
        'id',
        'user_id',
    ];
}
