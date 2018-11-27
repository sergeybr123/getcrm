<?php

namespace App\Http\Controllers\Manager;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class MyWorkController extends Controller
{
    public function index() {
        $user = Auth::user()->id;

        $pages = Company::where('user_id', $user)->whereNull('bot')->get();
        $bot_new = [];
        $bots = Company::where('user_id', $user)->whereNull('bot')->with('bots')->get();
//        if($bots != null) {
//            foreach ($bots as $bot) {
//                print_r($bot);
////                $bot_new = array_push($bot_new, $bot);
//            }
//
//        }
//        $bot_new = Bot::where('user_id', $user)->whereNull('deleted_at')->orderBy('id', 'desc')->paginate(30);
//        dd($bots);
        $bot_old = Company::where('user_id', $user)->whereNotNull('bot')->get();

        return view('manager.mywork.index', ['pages' => $pages, 'bot_new' => $bots, 'bot_old' => $bot_old]);
    }
}
