<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'notifiable_id',
        'notifiable_type',
        'notifier_id',
        'notifier_type',
        'notif_type',
        'content',
        'title',
        'read'
    ];
    public $table = "notifications";
    protected $casts = [
        'read' => 'boolean',
    ];

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function notifier()
    {
        return $this->morphTo();
    }
}
