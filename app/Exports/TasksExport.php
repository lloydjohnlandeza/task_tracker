<?php

namespace App\Exports;

use App\Models\Task;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Auth;
class TasksExport implements FromView
{
    public function view(): View
    {
      $user_id = Auth::user()->id;
      $tasks = Task::where('parent_id', 0)
              ->where('user_id', $user_id)
              ->join('orders', 'orders.id', '=', 'tasks.order_id')
              ->orderBy('orders.order' , 'asc')
              ->with('deep_sub_tasks')
              ->get();
      $status_color = [
        'Pending' => '#FFFF8D',
        'Complete' => '#B9F6CA',
        'Cancel' => '#FF9E80',
        'Custom' => '#82B1FF'
      ];
      return view('exports.tasks', [
        'tasks' => $tasks,
        'status_color' => $status_color
      ]);
    }
}
