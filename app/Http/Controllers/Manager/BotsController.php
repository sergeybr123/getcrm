<?php

namespace App\Http\Controllers\Manager;

use App\Models\Company;
use App\Models\Bot;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Throwable;

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
//        $bots_new = Bot::whereNull('deleted_at')->orderBy('id', 'desc')->paginate(30);

        $bots_new = Company::/*where('user_id', $user->id)
            ->*/
        join('bots','bots.botable_id','=','companies.id')
            ->join('users', 'users.id', '=', 'companies.user_id')
            ->select(
                'companies.id as Id',
                'users.id as UserId',
                'users.email as UserEmail',
                'companies.slug as Slug',
                'companies.created_at as CompanyCreated',
                'companies.deleted_at as CompanyDeleted',
                'bots.id as BotId',
                'bots.type as BotType',
                'bots.name as BotName',
                'bots.active as BotActive'
            )
            ->whereNull('companies.deleted_at')
            ->where('bots.type', 'bot')
            ->orderBy('companies.id', 'desc')
            ->paginate(30);

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
        if($company != null){
            return response()->json(['error' => 0, 'message' => 'Сброшено успешно!']);
        } else {
            return response()->json(['error' => 1, 'message' => 'Произошла ошибка!']);
        }
//        return back();
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

    public function activate($user_id, $bot_id)
    {
        $bot = Bot::findOrFail($bot_id);

        $count = Bot::where('id', '!=', $bot_id)->where('botable_id', $bot->botable_id)->where('type', 'bot')->where('active', 1)->count();

        if($count < 1) {
            try{
                if($bot->active == 1) {
                    $bot->active = 0;
                } else {
                    $bot->active = 1;
                }
                $bot->save();
                return response()->json(['error' => 0]); //redirect()->route('manager.users.show', $user_id);
            } catch (Exception $e) {
                return response()->json(['error' => 1, 'message' => $e->getMessage()]);
            }

        } else {
            return response()->json(['error' => 2]);
        }
    }

    /*Смена владельца авточата*/
    public function change_owner(Request $request, $user_id, $id)
    {
        $bot = Company::findOrFail($id);
        $old_owner = User::findOrFail($user_id);
        if($request->isMethod('POST')) {
            $owner = User::where('email', $request->new_owner)->first();
            $bot->user_id = $owner->id;
            $bot->save();
            return redirect()->route('manager.users.show', ['id' => $user_id]);
        } else {
            return view('manager.bots.change_owner', ['bot' => $bot, 'old_owner' => $old_owner]);
        }
    }
}
