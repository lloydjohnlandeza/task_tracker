<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
class UserController extends Controller
{
  public function welcome() {
      return Inertia::render('User/Welcome', [
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
