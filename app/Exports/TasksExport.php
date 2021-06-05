<?php

namespace App\Exports;

use App\Models\Task;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Auth;
class TasksExport implements FromView
{
    // public function headings(): array
    // {
    //     return [
    //         'Task',
    //         'Status'
    //     ];
    // }
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
        'pending' => '#FFFF8D',
        'complete' => '#B9F6CA',
        'cancel' => '#FF9E80',
        'custom' => '#82B1FF'
      ];
      return view('exports.tasks', [
        'tasks' => $tasks,
        'status_color' => $status_color
      ]);
    }
    // public function query()
    // {
    //   $user_id = Auth::user()->id;
    //     return Task::query()->where('parent_id', 0)
    //           ->where('user_id', $user_id)
    //           ->join('orders', 'orders.id', '=', 'tasks.order_id')
    //           ->orderBy('orders.order' , 'asc')
    //           ->with('deep_sub_tasks');
    // }
    // /**
    // * @var Task $task
    // */
    // public function map($task): array
    // {
    //     return [
    //       $task->task,
    //       $task->deep_sub_tasks,
    //       $task->status
    //     ];
    // }
}
