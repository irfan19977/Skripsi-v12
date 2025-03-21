<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $table = 'class';
    protected $fillable = [
        'prodi',
        'name',
        'grade'
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

    public function studentClasses()
    {
        return $this->hasMany(StudentClass::class, 'class_id');
    }
}
