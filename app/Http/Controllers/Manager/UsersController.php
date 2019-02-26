<?php

namespace App\Http\Controllers\Manager;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;
use Throwable;

use App\User;
use App\Models\Company;
use App\Models\Phone;
use App\Models\Bot;
use App\Models\BotListener;
use App\Models\BotAnswer;
use App\Models\Account;
use App\Models\Profile;
use App\Models\BillingPlan;
use App\Models\BillingSubscribe;

class UsersController extends Controller
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
        if($text != null) {
            if($type == 1) {
                $users = User::where('email', 'LIKE', '%' . $text . '%')->with('phone')->orderBy('id', 'desc')->paginate(30);
            }
            if($type == 2) {
                $phone = substr($text, -10, 10);
                $query = Phone::where('phone', $phone)->pluck('user_id');
                $users = User::whereIn('id', $query)->with('phone')->orderBy('id', 'desc')->paginate(30);
            }
        } else {
            $users = User::with('phone')->orderBy('id', 'desc')->paginate(30);
        }
        return view('manager.users.index', ['users' => $users, 'type' => $type, 'text' => $text]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $path = 'js/phone.json';
        $content = json_decode(file_get_contents($path), true);
//        dd($content);
        return view('manager.users.create', ['phones' => $content]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $path = 'js/phone.json';
        $content = json_decode(file_get_contents($path), true);

        $user_phone = [];
        foreach($content as $item) {
            if($item['code'] == $request->code) {
                $user_phone = $item;
            }
        }
//        dd($user_phone);


        $password = $this->generatePassword();
        $user = User::create([
            'name' => $request->name,
            'username' => str_replace('+', '', $user_phone['dial_code']) . $request->phone,
            'email' => $request->email,
            'password' => bcrypt($password),
        ]);
        $user->phones()->save(new Phone([
            'country_code' => str_replace('+', '', $user_phone['dial_code']),
            'cca2' => $user_phone['code'],
            'phone' => $request->phone
        ]));
        return redirect()->route('manager.users.show', $user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        $bots = Company::where('user_id', $id)
            ->where(function($result) {
                $result->whereNotNull('bot')
                    ->orWhere('temp_bot', '!=', null);
            })->get();


        //->whereNotNull('bot')->orWhere('temp_bot', '!=', null)->whereNull('deleted_at')->get();
//        $pages = Company::where('user_id', $id)->whereNull('bot')->whereNull('deleted_at')->get();

        $new_bots = Company::where('user_id', $user->id)
            ->join('bots','bots.botable_id','=','companies.id')
            ->select('companies.id as Id', 'companies.slug as Slug', 'companies.created_at as CompanyCreated', 'bots.id as BotId', 'bots.type as BotType', 'bots.name as BotName', 'bots.active as BotActive')
            ->where('bots.type', 'bot')
            ->get();

        $pages = Company::where('user_id', $user->id)
            ->join('bots','bots.botable_id','=','companies.id')
            ->select('companies.id as Id', 'companies.slug as Slug', 'companies.created_at as CompanyCreated', 'bots.id as BotId', 'bots.type as BotType', 'bots.name as BotName', 'bots.active as BotActive')
            ->where('bots.type', 'multilink')
            ->get();

        $client = new Client(['headers' => ['Content-Type' => 'application/json', 'Authorization' => 'Basic ' . config('app.billing_token')]]);

        // Получаем подписку пользователя
//        $URI = config('app.billing_url') . '/subscribe/' . $user->id;
//        $response = $client->get($URI);
//        $subscribe_resp = json_decode($response->getBody());
//        $subscribe = $subscribe_resp->data;
        $subscribe = BillingSubscribe::where('user_id', $id)->first();


        // Позже переделать
//        $url_plans = config('app.billing_url') . '/plan/all';
//        $resp_plans = $client->get($url_plans);
//        $plans = json_decode($resp_plans->getBody());
        $plans = BillingPlan::all();

        // Получаем все счета пользователя
        $url_inv = config('app.billing_url') . '/user-invoice/' . $user->id;
        $resp_inv = $client->get($url_inv);
        $invoices_resp = json_decode($resp_inv->getBody());
        $invoices = $invoices_resp->data;

//        dd($invoices);

        return view('manager.users.show', ['user' => $user, 'bots' => $bots, 'new_bots' => $new_bots, 'pages' => $pages, 'subscribe' => $subscribe, 'plans' => $plans, 'invoices' => $invoices]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        $path = 'js/phone.json';
        $phones = json_decode(file_get_contents($path), true);

        return view('manager.users.edit', ['user' => $user, 'phones' => $phones]);
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
        $this->validate($request, [
            'email' => ['required', 'email', 'unique'],
            'country_code' => ['required'],
            'phone' => ['required', 'numeric', 'unique']
        ]);

        $user = User::findOrFail($id);
        $phone = Phone::where('user_id', $id)->first();

        $user->email = $request->email;

        $path = 'js/phone.json';
        $content = json_decode(file_get_contents($path), true);

        $user_phone = [];
        foreach($content as $item) {
            if($item['code'] == $request->country_code) {
                $user_phone = $item;
            }
        }

        $phone->country_code = str_replace('+', '', $user_phone['dial_code']);
        $phone->cca2 = $user_phone['code'];
        $phone->phone = $request->phone;

        if (
            $request->has('first_name') ||
            $request->has('last_name') ||
            $request->has('company') ||
            $request->has('location')
        ) {
            $user->profile()->save(Profile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name' => $request->input('first_name'),
                    'last_name' => $request->input('last_name'),
                    'company' => $request->input('company'),
                    'location' => $request->input('location'),
                ]
            ));
        }

        $phone->save();
        $user->save();

        return back()->with(['success' => 'Данные сохранились']);
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

    public function generatePassword($length = 8)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = mb_strlen($chars);

        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }

        return $result;
    }

    /*Подписка на бесплатный пакет пользователей не имеющих авточат*/
    public function selectNotSubscribed()
    {
//        set_time_limit(0);
        /*Раскомментировать рабочий код, не удалять*/
        $users = Company::whereNull('bot')->whereNull('deleted_at')->select('user_id')->distinct()->get();
        foreach ($users as $user) {
            if(Bot::where('botable_id', $user->user_id)->doesntExist())
            {
                $client = new Client(['headers' => ['Content-Type' => 'application/json', 'Authorization' => 'Basic ' . config('app.billing_token')]]);
                $URI = config('app.billing_url') . '/subscribe/free/' . $user->id;
                $response = $client->get($URI);
                $plan = json_decode($response->getBody());
            }
        }
    }


    public function payActivate(Request $request)
    {
//        dd($request);
        $client = new Client(['headers' => ['Content-Type' => 'application/json', 'Authorization' => 'Basic ' . config('app.billing_token')]]);
        $URI = config('app.billing_url') . '/activate';
        $response = $client->post($URI, [
            'body' => $request,
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ]
        ]);
        $resp = json_decode($response->getBody());
        if($resp->error == 0) {
//            dd($resp);
            return redirect()->route('manager.users.show', ['id' => $request->user_id]);
        } else {
            dd($resp);
        }
    }

    public function change_plan(Request $request)
    {
//        return response()->json([$request->user_id]);
        $user = $request->user_id;
        $subscribe = BillingSubscribe::where('user_id', $user)->first();
        $subscribe->plan_id = $request->plan_id;
        $subscribe->save();
//        return response()->json(['error' => 0, 'message' => 'План изменен']);
        try{
            return response()->json(['error' => 0, 'message' => 'План изменен']);
        }
        catch (Throwable $t){
            return response()->json(['error' => 1, 'message' => $t]);
        }
    }

    public function createBot(Request $request)
    {
//        return response()->json([$request->user_id]);
        $company = new Company;
        $company->user_id = $request->user_id;
        $company->slug = $request->link;
        // {"bot": [], "menu": {"text": null, "actions": []}, "profile": {"name": null, "avatar": null, "company": null, "background": {"value": null}}, "welcome": []}
        $company->save();
        try{
            return response()->json(['error' => 0, 'company' => $company]);
        } catch (Throwable $th){
            return response()->json(['error' => 1, 'message' => $th]);
        }
    }

    public function create_multilink(Request $request, $user_id)
    {
        $user = User::find($user_id);
        if($request->isMethod('POST')) {
            $company = new Company();
            $company->user_id = $user->id;
            $company->slug = $request->link;
            $company->name = 'Multilink';
            $company->description = $request->description;
            $company->save();

            $bot = new Bot();
            $bot->type = 'multilink';
            $bot->botable_id = $company->id;
            $bot->botable_type = 'App\\Models\\Company';
            $bot->name = 'Multilink';
            $bot->active = 1;
            $bot->save();

            $bot_listener = new BotListener();
            $bot_listener->bot_id = $bot->id;
            $bot_listener->text = 'welcome';
            $bot_listener->save();

            if($request->welcome_text != null) {
                $bot_answer = new BotAnswer();
                $bot_answer->bot_listener_id = $bot_listener->id;
                $bot_answer->data = ['text' => $request->welcome_text];
                $bot_answer->type = 'GurmanAlexander\\TheBot\\Models\\Answers\\TextAnswer';
                $bot_answer->order = 1;
                $bot_answer->save();
            }
            if($request->whatsapp != null) {
                $bot_answer = new BotAnswer();
                $bot_answer->bot_listener_id = $bot_listener->id;
                if($request->whatsapp_message != null) {
                    $bot_answer->data = ['text' => 'Whatsapp', 'type' => 'whatsapp', 'phone' => str_replace(['+', ' ', '-', '(', ')', '_'], '', $request->whatsapp), 'wa_text' => $request->whatsapp_message];
                } else {
                    $bot_answer->data = ['text' => 'Whatsapp', 'type' => 'whatsapp', 'phone' => str_replace(['+', ' ', '-', '(', ')', '_'], '', $request->whatsapp), 'wa_text' => 'Приветствуем'];
                }
                $bot_answer->type = 'GurmanAlexander\\TheBot\\Models\\Answers\\ActionAnswer';
                $bot_answer->order = 2;
                $bot_answer->save();
            }
            if($request->telegram != null) {
                $bot_answer = new BotAnswer();
                $bot_answer->bot_listener_id = $bot_listener->id;
                $bot_answer->data = ['text' => 'Telegram', 'type' => 'telegram', 'name' => str_replace('@', '', $request->telegram)];
                $bot_answer->type = 'GurmanAlexander\\TheBot\\Models\\Answers\\ActionAnswer';
                $bot_answer->order = 3;
                $bot_answer->save();
            }
            if($request->site != null) {
                $bot_answer = new BotAnswer();
                $bot_answer->bot_listener_id = $bot_listener->id;
                $bot_answer->data = ['text' => 'Ссылка на сайт', 'type' => 'link', 'url' => $request->site];
                $bot_answer->type = 'GurmanAlexander\\TheBot\\Models\\Answers\\ActionAnswer';
                $bot_answer->order = 4;
                $bot_answer->save();
            }
            if($request->phone != null) {
                $bot_answer = new BotAnswer();
                $bot_answer->bot_listener_id = $bot_listener->id;
                $bot_answer->data = ['text' => 'Телефон', 'type' => 'link', 'url' => 'tel:' . $request->phone];
                $bot_answer->type = 'GurmanAlexander\\TheBot\\Models\\Answers\\ActionAnswer';
                $bot_answer->order = 5;
                $bot_answer->save();
            }
            if($request->mail != null) {
                $bot_answer = new BotAnswer();
                $bot_answer->bot_listener_id = $bot_listener->id;
                $bot_answer->data = ['text' => 'Электронная почта', 'type' => 'link', 'url' => 'mailto:' . $request->mail];
                $bot_answer->type = 'GurmanAlexander\\TheBot\\Models\\Answers\\ActionAnswer';
                $bot_answer->order = 6;
                $bot_answer->save();
            }
            return redirect()->route('manager.users.show', $user->id);
        } else {
            return view('manager.multilink.create', ['user' => $user]);
        }
    }

    public function edit_multilink(Request $request, $id)
    {

    }

    public function create_bot(Request $request, $user_id)
    {
        $user = User::find($user_id);
        if($request->isMethod('POST')) {
            $company = new Company();
            $company->user_id = $user->id;
            $company->slug = $request->link;
            $company->name = $request->name;
            $company->description = $request->description;
            $company->save();

            $bot = new Bot();
            $bot->type = 'bot';
            $bot->botable_id = $company->id;
            $bot->botable_type = 'App\\Models\\Company';
            $bot->name = $request->name;
            $bot->active = 1;
            $bot->save();

            return redirect()->route('manager.users.show', $user->id);
        } else {
            return view('manager.bots.create', ['user' => $user]);
        }
    }

    public function createBotOnExist($company_id)
    {
        $company = Company::findOrFail($company_id);

        $bot = new Bot();
        $bot->type = 'bot';
        $bot->botable_id = $company->id;
        $bot->botable_type = 'App\\Models\\Company';
        $bot->name = $company->name;
        $bot->active = 1;
        $bot->save();

        return response()->json(['bot_id' => $bot->id]);
//        return Redirect::to('https://getchat.me/constructor2/'.$bot->id);
    }

    public function invoice($id)
    {
        $user = User::findOrFail($id);
        return view('manager.users.invoice', ['user' => $user]);
    }

    public function delete_chat($id, $user_id)
    {
//        dd($user_id);
        $company = Company::find($id);
//        dd($company);
        if($company) {
            $bot = Bot::where('botable_id', $company->id)->first();
//            dd($bot);
            if($bot) {
                $bot->delete();
            }
            $company->delete();
//            return response()->json(['message' => 'Deleted'], 200);
            return redirect()->route('manager.users.show', ['id' => $user_id]);
        } /*else {
            return redirect()->route('manager.users.show', ['id' => $user_id]);
        }*/
    }
}
