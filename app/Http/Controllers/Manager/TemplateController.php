<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\User;
use Illuminate\Http\Request;
use App\Models\Bot;
use App\Models\BotListener;
use App\Models\BotAnswer;
use App\Models\BotInput;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

use GuzzleHttp\Client;

class TemplateController extends Controller
{
    public function index()
    {

        $value = Session::all();
//        dd($value);

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

    public function copy_bot($slug, $id)
    {
        $user = Auth::user();

        $company = Company::where('slug', $slug)->first();

        if(!$company) {
            $company = new Company();
            $company->user_id = $user->id;
            $company->name = $slug;
            $company->slug = $slug;
            $company->save();
        }

//        dd($company);


        $bot = Bot::where('id', $id)/*->with('listeners')->with('listeners.answers')->with('inputs')->get();//*/->first();
//        dd($bot);

        $style = json_decode($bot->style);
        if($style->bg->image) {
            $image = "https://getchat.me".$style->bg->image;
            dd($image);
        }
        if($style->avatar) {
            $avatar = "https://getchat.me".$style->avatar;
//            dd($avatar);
        }

        $listeners = BotListener::where('bot_id', $id)->get();
//        dd($listeners);

        $arr_listeners = [];

        $arr_text = [];
        $arr_form = [];
        $arr_image = [];
        $arr_file = [];
        $arr_action = [];

        foreach ($listeners as $listener) {
            $listener['old_id'] = 1;
            array_push($arr_listeners, $listener);
            $answers = BotAnswer::where('bot_listener_id', $listener->id)->get();
            foreach ($answers as $answer) {
                if($answer->type == "GurmanAlexander\TheBot\Models\Answers\ActionAnswer") {
                    $answer['l_id'] = $listener->id;
                    array_push($arr_action, $answer);
                } elseif ($answer->type == "GurmanAlexander\TheBot\Models\Answers\TextAnswer") {
                    array_push($arr_text, $answer);
                } elseif ($answer->type == "GurmanAlexander\TheBot\Models\Answers\FormAnswer") {
                    array_push($arr_form, $answer);
                } elseif ($answer->type == "GurmanAlexander\TheBot\Models\Answers\ImageAnswer") {
                    array_push($arr_image, $answer);
                } elseif ($answer->type == "GurmanAlexander\TheBot\Models\Answers\FileAnswer") {
                    array_push($arr_file, $answer);
                }
            }
        }


        dd($arr_listeners);
        dd($arr_action);
        return view('manager.templates.copy', ['bot' => $bot, 'listeners' => $listeners]);
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

    public function postCopyBot(Request $request)
    {
        $email = $request->user_email;
        $link = $request->link;
        $template_id = $request->template_id;
        $user = User::where('email', $email)->first();



        $get_link = Company::where('slug', $link)->first();
        if($get_link != null) {
            $client = new Client();
            $url = 'https://getchat.me/create-new-bot';
            $params = [
                'query' => [
                    'link' => $link,
                    'user_email' => $email,
                    'template_id' => $template_id,
                ]
            ];
            $response = $client->get($url, $params);
            $data = json_decode($response->getBody());
//            dd($data);
            return response()->json(['error' => 0, 'message' => 'Копирование завершено!']);
        } else {
            $company = new Company();
            $company->user_id = $user->id;
            $company->slug = $link;
            $company->name = $request->name_bot;
            $company->save();

            $client = new Client();
            $url = 'https://getchat.me/create-new-bot';
            $params = [
                'query' => [
                    'link' => $link,
                    'user_email' => $email,
                    'template_id' => $template_id,
                ]
            ];
            $response = $client->get($url, $params);
            $data = json_decode($response->getBody());
//            dd($data);
            return response()->json(['error' => 0, 'message' => 'Копирование завершено!']);
        }/* else {
            return response()->json(['error' => 1, 'message' => 'Вы не можете копировать на данную ссылку!']);
        }*/



//        $client = new Client();
//        $url = 'https://getchat.me/create-new-bot';
//        $params = [
//            'query' => [
//                'link' => $link,
//                'user_email' => $email,
//                'template_id' => $template_id,
//            ]
//        ];
//        $response = $client->get($url, $params);
//        $data = json_decode($response->getBody());
//        return redirect()->route('manager.users.show', $data->user_id);
////        dd($data);
    }
}
