<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'body',
        'data'
    ];

    protected $casts = [
        'data' => 'array'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function targets()
    {
        return $this->hasMany(NotificationTarget::class);
    }
}
