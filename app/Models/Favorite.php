<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Favorite extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'favoritable_id',
        'favoritable_type',
        'favoriter_id',
        'favoriter_type',
    ];

    public function favoritable()
    {
        return $this->morphTo()->morphWith([
            JobList::class => ['company', 'industry']
        ]);;
    }

    public function favoriter()
    {
        return $this->morphTo();
    }
}
