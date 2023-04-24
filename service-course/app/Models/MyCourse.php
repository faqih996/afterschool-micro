<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyCourse extends Model
{
    protected $table = 'my_courses';

    protected $fillable = [
        'course_id', 'user_id'
    ];

    protected $casts = [
        'created_at' => 'datetime::d-m-Y H:m:s',
        'updated_at' => 'datetime::d-m-Y H:m:s',
        'deleted_at' => 'datetime::d-m-Y H:m:s',
    ];

    public function course()
    {
        return $this->belongsTo('App\Models\Course');
    }
}