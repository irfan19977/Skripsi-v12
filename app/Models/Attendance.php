<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $keyType = 'string'; 
    public $incrementing = false;
    protected $table = 'attendances';
    protected $fillable = [
        'student_id',
        'teacher_id',
        'subject_id',
        'date',
        'time',
        'status',
        'notes',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Define relationship with Teacher (User)
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Define relationship with Subject
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
