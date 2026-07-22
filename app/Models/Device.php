<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
        'token',
        'platform'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
