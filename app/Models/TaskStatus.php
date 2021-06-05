<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskStatus extends Model
{
    use HasFactory;
    public $fillable = [
      'status',
    ];

    public function tasks () {
      return $this->hasMany(Task::class, 'task_status_id', 'id');
    }
    
}
