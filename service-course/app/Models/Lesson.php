<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $table = 'lessons';

    protected $fillable = [
        'name', 'video', 'chapter_id'
    ];

    protected $casts = [
        'created_at' => 'datetime::d-m-Y H:m:s',
        'updated_at' => 'datetime::d-m-Y H:m:s',
        'deleted_at' => 'datetime::d-m-Y H:m:s',
    ];
}