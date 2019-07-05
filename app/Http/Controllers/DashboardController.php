<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if($user->hasRole(['admin'])) {
            return view('dashboard.admin');
        } elseif($user->hasRole(['manager', 'analyst', 'accountant'])) {
            return view('dashboard.manager');
        } elseif($user->hasRole(['partner+'])) {
            return \redirect()->route('partner::index');
        } else {
            Auth::logout();
            return Redirect::route('home');
        }
    }
}
