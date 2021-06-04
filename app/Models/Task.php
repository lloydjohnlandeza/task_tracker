<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Task extends Model
{
    use HasFactory;
    use SoftDeletes;
    public $fillable = [
      'parent_id',
      'task_status_id',
      'user_id',
      'order_id',
      'task',
    ];
    protected $appends = ['status'];
    public function parent_task() {
      return $this->belongsTo(Task::class, 'parent_id', 'id');
    }
    public function sub_tasks() {
      return $this->hasMany(Task::class, 'parent_id', 'id');
    }
    public function deep_sub_tasks() {
      return $this->hasMany(Task::class, 'parent_id', 'id')->with('deep_sub_tasks');
    }
    
    public function order() {
      return $this->belongsTo(Order::class);
    }
    public function task_status() {
      return $this->hasOne(TaskStatus::class, 'id', 'task_status_id')->select('id', 'status');
    }
    public function getStatusAttribute () {
      return $this->task_status->status;
    }
}
