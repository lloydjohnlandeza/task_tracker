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

    public function deep_parent_task() {
      return $this->belongsTo(Task::class, 'parent_id', 'id')->with('deep_parent_task');
    }

    public function deep_sub_tasks() {
      return $this->hasMany(Task::class, 'parent_id', 'id')->with(['deep_sub_tasks' => function ($q) {
        $q->join('orders', 'orders.id', '=', 'tasks.order_id');
        $q->orderBy('orders.order' , 'asc');
        $q->orderBy('tasks.updated_at' , 'desc');
        $q->select('tasks.*');
      }]);
    }

    public function deleted_deep_sub_tasks() {
      return $this->hasMany(Task::class, 'parent_id', 'id')->with(['deleted_deep_sub_tasks' => function ($query) {
          $query->withTrashed()
          ->where('deleted_at', '!=', null)
          ->orderBy('deleted_at', 'desc');
        }]);
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

    public static function boot() {
      parent::boot();
      static::deleting(function($task) {
        // delete order
        $task->order()->delete();
        // delete all subtask while parent is being deleted
        foreach($task->deep_sub_tasks as $subtask){
          $subtask->delete();
        }
      });
    }
}
