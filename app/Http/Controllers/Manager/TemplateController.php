<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\Bot;
use Illuminate\Support\Facades\Session;

class TemplateController extends Controller
{
    public function index()
    {

        $value = Session::all();
        dd($value);

        $templates = Company::
        join('bots','bots.botable_id','=','companies.id')
            ->select('companies.id as Id', 'companies.slug as Slug', 'companies.created_at as CompanyCreated', 'companies.deleted_at as CompanyDeleted', 'bots.id as BotId', 'bots.type as BotType', 'bots.name as BotName', 'bots.active as BotActive')
            ->where('bots.type', 'bot_template')
            ->whereNull('companies.deleted_at')
            ->paginate(30);
//        dd($templates);

        return view('manager.templates.index', ['templates' => $templates]);
    }

    function RandomString($length) {
        $original_string = array_merge(range(0,9), range('a','z'), range('A', 'Z'));
        $original_string = implode("", $original_string);
        return substr(str_shuffle($original_string), 0, $length);
    }

    public function copyBot($id)
    {
        $bot = Bot::findOrFail($id);

        /*
         *
         * $pic = $request->file('picture');
                        $extension = $pic->getClientOriginalExtension();
                        $pic_name = $this->RandomString(32);
                        Storage::disk('public')->put('covers/' . $pic_name.'.'.$extension,  File::get($pic));
                        $url_pic = '/storage/covers/'.$pic_name.'.'.$extension;
                        $style = array_merge($style, ['bg' => ['image' => $url_pic]]);
         *
         * */

    }
}
