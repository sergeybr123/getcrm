<?php

namespace App\Http\Controllers\Manager;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\User;
use Mockery\Exception;

class PagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $type = $request->type;
        $text = $request->text;

//        if($type == 1 && $text != null) {
//            $pages = Company::where('slug', $text)->whereNull('bot')->whereNull('deleted_at')->orderBy('id', 'desc')->paginate(30);
//        } elseif($type == 2 && $text != null) {
//            $user = User::where('email', 'LIKE', '%' . $text . '%')->first();
//            if($user != null) {
//                $pages = Company::where('user_id', $user->id)->whereNull('bot')->whereNull('deleted_at')->orderBy('id', 'desc')->paginate(30);
//            } else {
//                $pages = [];
//            }
//        } else {
//            $pages = Company::whereNull('bot')->whereNull('deleted_at')->orderBy('id', 'desc')->paginate(30);
//        }

        $pages = Company::join('bots','bots.botable_id','=','companies.id')
            ->select('companies.id as Id', 'companies.slug as Slug', 'companies.created_at as CompanyCreated', 'companies.deleted_at as CompanyDeleted', 'bots.id as BotId', 'bots.type as BotType', 'bots.name as BotName', 'bots.active as BotActive')
            ->where('bots.type', 'multilink')
            ->whereNull('companies.deleted_at')
            ->orderBy('companies.id', 'desc')
            ->paginate(30);

//        dd($pages);

        return view('manager.pages.index', ['pages' => $pages, 'type' => $type, 'text' => $text]);
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
        $page = Company::findOrFail($id);
        $accounts = $page->accounts;
        return view('manager.pages.show', ['page' => $page, 'accounts' => $accounts]);
//        dd($accounts);
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

    public function editLink(Request $request)
    {
        $company = Company::find($request->id);
//        dd($request);
        $company->slug = $request->slug;
        $company->save();
        try{
            return response()->json(['error' => 0]);
        } catch (Exception $exception) {
            return response()->json(['error' => $exception]);
        }

    }
}
