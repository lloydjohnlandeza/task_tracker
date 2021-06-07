<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\TaskStatus;
use Auth;
class UserController extends Controller
{
  public function welcome() {
      $user_id = Auth::user()->id;
      $taskStatuses = TaskStatus::select('id', 'status', 'color')
                      ->withCount(['tasks' => function ($query) use ($user_id){
                        $query->where('user_id', $user_id);
                      }])
                      ->whereHas('tasks', function ( $query) use ($user_id){
                        $query->where('user_id', $user_id);
                      }, '>', 0)
                      ->orWhereIn('id', [1,2,3])
                      ->get();
      $formatted = $this->formatTaskStatus($taskStatuses);
      return Inertia::render('User/Welcome', [
        'chart_data' =>  $formatted,
        'head' => [
          'title' => 'Welcome'
        ]
      ]);
  }

  public function index() {
      return Inertia::render('User/Login', [
        'head' => [
          'title' => 'Login'
        ]
      ]);
  }

  public function register() {
      return Inertia::render('User/Register', [
        'head' => [
          'title' => 'Register'
        ]
      ]);
  }

  private function formatTaskStatus ($taskStatuses) {
    $labels = [];
    $data = [];
    $backgroundColor = [];
    foreach ($taskStatuses as $key => $value) {
      $labels[] = $value->status;
      $data[] = $value->tasks_count;
      $backgroundColor[] = $value->color;
    }
    return ['labels' => $labels, 'data' => $data, 'backgroundColor' => $backgroundColor];
  }
}
