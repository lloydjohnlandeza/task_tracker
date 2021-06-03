<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use URL;
class UserController extends Controller
{
  public function index()
  {
      return Inertia::render('User/Login');
  }
}
