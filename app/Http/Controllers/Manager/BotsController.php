<?php

namespace App\Http\Controllers\Manager;

use App\Models\Company;
use App\Models\Bot;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BotsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bots_old = Company::whereNotNull('bot')->whereNull('deleted_at')->orderBy('id', 'desc')->get();
//        $bots_new = Bot::whereNull('deleted_at')->orderBy('id', 'desc')->get();
        return view('manager.bots.index_old', ['bots_old' => $bots_old/*, 'bots_new' => $bots_new*/]);
    }

    public function bot_old(Request $request)
    {
        $type = $request->type;
        $text = $request->text;

        if($type == 1 && $text != null) {
            $bots_old = Company::where('slug', $text)->whereNotNull('bot')->whereNull('deleted_at')->orderBy('id', 'desc')->paginate(30);
        } elseif($type == 2 && $text != null) {
            $user = User::where('email', 'LIKE', '%' . $text . '%')->first();
            if($user != null) {
                $bots_old = Company::where('user_id', $user->id)->whereNotNull('bot')->whereNull('deleted_at')->orderBy('id', 'desc')->paginate(30);
            } else {
                $bots_old = [];
            }
        } else {
            $bots_old = Company::whereNotNull('bot')->whereNull('deleted_at')->orderBy('id', 'desc')->paginate(30);
        }
//        return view('manager.pages.index', ['pages' => $pages, 'type' => $type, 'text' => $text]);
//        $bots_old = Company::whereNotNull('bot')->whereNull('deleted_at')->orderBy('id', 'desc')->paginate(30);
        return view('manager.bots.index_old', ['bots_old' => $bots_old, 'type' => $type, 'text' => $text]);
    }
    public function bot_new()
    {
        $bots_new = Bot::whereNull('deleted_at')->orderBy('id', 'desc')->paginate(30);
        return view('manager.bots.index_new', ['bots_new' => $bots_new]);
    }




    public function confirm(Request $request)
    {
        $company = Company::findOrFail($request->id);
        $company->bot = $company->temp_bot;
        $company->temp_bot = NULL;
        $company->save();
        if($company != null){
            return response()->json(['error' => 0, 'message' => 'Подтверждено успешно!']);
        } else {
            return response()->json(['error' => 1, 'message' => 'Произошла ошибка!']);
        }
//        return back();
    }
    public function reset(Request $request)
    {
        $company = Company::findOrFail($request->id);
        $company->temp_bot = NULL;
        $company->save();
        return back();
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
