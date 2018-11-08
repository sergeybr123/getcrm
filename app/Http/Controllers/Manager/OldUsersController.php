<?php

namespace App\Http\Controllers\Manager;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

class OldUsersController extends Controller
{
    public function index()
    {
        $users = User::with('phone')->paginate(30);
        return view('manager.users.index', ['users' => $users]);
    }
}
