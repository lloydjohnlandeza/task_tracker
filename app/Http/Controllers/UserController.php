<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\TaskStatus;
class UserController extends Controller
{
  public function welcome() {
      $taskStatuses = TaskStatus::select('id', 'status')->withCount('tasks')->get();
      $labels = [];
      $data = [];
      foreach ($taskStatuses as $key => $value) {
        $labels[] = $value->status;
        $data[] = $value->tasks_count;
      }
      return Inertia::render('User/Welcome', [
        'chart_data' => [
          'labels' => $labels,
          'data' => $data
        ],
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
}
