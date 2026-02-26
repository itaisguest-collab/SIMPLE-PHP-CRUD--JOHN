<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Employee extends Model
{
    //
    use HasFactory;

    // disable automatic timestamps (table has no created_at/updated_at)
    public $timestamps = false;

    public $incrementing = false;
    protected $keyType = 'string';

     protected $fillable = [
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        
    ];

    protected static function boot(): void
    {
        parent::boot();

        // Generate UUID automatically
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
