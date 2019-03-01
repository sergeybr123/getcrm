<?php

namespace App\Http\Controllers\Manager;

use Illuminate\Http\Request;
use App\Models\Bot;

class TemplateController extends Controller
{
    public function index()
    {
        $templates = Bot::where('type', 'bot_template')->get();
        return view('manager.templates.index', ['templates' => $templates]);
    }
}
