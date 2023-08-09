<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobType extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'job_list_id',
        'type_id',
    ];

    public function type(): HasOne{
        return $this->hasOne(Type::class, 'id', 'type_id');
    }
}
