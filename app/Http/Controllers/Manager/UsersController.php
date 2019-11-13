<?php

namespace App\Http\Controllers\Manager;

use App\Models\BillingInvoice;
use App\Models\BillingInvoiceType;
use App\Models\BillingService;
use App\Models\BotInput;
use App\Notifications\UserRegistered;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
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
    /*-----Возвращаем массив данных для заполнения BotInputs-----*/
    public function get_bi_array()
    {
        return $bi_array = [
            ['data' => '{"built_in": true, "desc":"Имя", "name":"name", "text":["Укажите Ваше имя"], "error":["Кажется введено некорректное имя."], "rules":["string"], "success":["☑ Записала. Очень приятно."]}'],
            ['data' => '{"built_in": true, "desc":"Фамилия", "name":"surname", "text":["Укажите Вашу фамилию"], "error":["Не пугайтесь, но Вы неверно заполнили Фамилию. Попробуйте еще раз."], "rules":["string"], "success":["☑ Спасибо, записала."]}'],
            ['data' => '{"built_in": true, "desc":"Email", "name":"email", "text":["Укажите Ваш email 📧"], "error":["Ошибка. Попробуйте еще раз, введите email."], "rules":["email"], "success":["☑ Принято."]}'],
            ['data' => '{"built_in": true, "desc":"Телефон", "name":"phone", "text":["Укажите Ваш контактный номер 📱 телефона для связи с Вами"], "error":["Хм.. проверьте, пожалуйста, правильность указанного номера"], "rules":["string"], "success":["☑ Спасибо)"]}'],
            ['data' => '{"built_in": true, "desc":"Дата рождения", "name":"birthday", "text":["Укажите дату Вашу рождения"], "error":["Произошла ошибка. Проверьте правильность указанной даты и введите еще раз."], "rules":["string"], "success":["☑ Записала"]}'],
            ['data' => '{"built_in": true, "desc":"Адрес", "name":"address", "text":["Укажите адрес"], "error":["Вы допустили ошибку. Проверьте адрес."], "rules":["string"], "success":["☑ Спасибо)"]}'],
            ['data' => '{"built_in": true, "desc":"Город", "name":"city", "text":["Укажите город"], "error":["Вы неверно указали город. Попробуйте еще раз."], "rules":["string"], "success":["☑ Записала)"]}'],
            ['data' => '{"built_in": true, "desc":"Ссылка на Instagram", "name":"url_to_instagram", "text":["Добавьте ссылку на Ваш аккаунт в Instagram"], "error":["Ваша ссылка не соответствует формату. Скопируйте ссылку и вставьте в поле ее раз."], "rules":["string"], "success":["☑ Записала, будем изучать ваш Instagram)"]}'],
            ['data' => '{"built_in": true, "desc":"Цифры", "name":"numbers", "text":["Укажите число"], "error":["Введенные данные не соответствуют формату цифр. Введите только цифры."], "rules":["string"], "success":["☑ Записала"]}'],
            ['data' => '{"built_in": true, "desc":"Комментарий", "name":"comments", "text":["Оставьте Ваш комментарий"], "error":["Что-то пошло не так! Введите текст еще раз!"], "rules":["string"], "success":["☑ Спасибо)"]}']
        ];
    }
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
        $user->notify(new UserRegistered($user, $password));
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
        $bots = Company::where('user_id', $id)->whereHas('bots')->orderBy('slug')->get();
//        dd($bots);

        $client = new Client(['headers' => ['Content-Type' => 'application/json', 'Authorization' => 'Basic ' . config('app.billing_token')]]);

        // Получаем подписку пользователя
//        $URI = config('app.billing_url') . '/subscribe/' . $user->id;
//        $response = $client->get($URI);
//        $subscribe_resp = json_decode($response->getBody());
//        $subscribe = $subscribe_resp->data;
        $subscribe = BillingSubscribe::where('user_id', $user->id)/*->with('plan')*/->first();


        // Позже переделать
//        $url_plans = config('app.billing_url') . '/plan/all';
//        $resp_plans = $client->get($url_plans);
//        $plans = json_decode($resp_plans->getBody());
//        $plans = BillingPlan::all();
//        dd($subscribe);

        // Получаем все счета пользователя
        $url_inv = config('app.billing_url') . '/user-invoice/' . $user->id;
        $resp_inv = $client->get($url_inv);
        $invoices_resp = json_decode($resp_inv->getBody());
        $invoices = $invoices_resp->data;

//        dd($invoices);

        $plan_bot_count = 0;
        $new_bot_count = 0;
        if($subscribe){
            $plan_bot_count = $subscribe->plan->bot_count;
            $new_bot_count = count($bots);
        }

        $profile = Profile::where('user_id', $user->id)->first();
        if(!$profile) {
            $profile = new Profile();
            $profile->user_id = $user->id;
            $profile->save();
        }
        $user_phone = Phone::where('user_id', $user->id)->first();
        $path = 'js/phone.json';
        $phones = json_decode(file_get_contents($path), true);

        return view('manager.users.show', [
            'user' => $user,
            'bots' => $bots,
            'subscribe' => $subscribe,
            'invoices' => $invoices,
            'plan_bot_count' => $plan_bot_count,
            'new_bot_count' => $new_bot_count,
            'profile' => $profile,
            'user_phone' => $user_phone,
            'phones' => $phones
        ]);
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
        $user = User::findOrFail($id);
        $phone = Phone::where('user_id', $id)->first();

        if(($request->email != $user->email) && ($request->phone != $phone->phone)) {
            $request->validate($request, [
                'email' => ['required', 'email', 'unique'],
                'country_code' => ['required'],
                'phone' => ['required', 'numeric', 'unique']
            ]);
        }

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

    function RandomString($length) {
        $original_string = array_merge(range(0,9), range('a','z'), range('A', 'Z'));
        $original_string = implode("", $original_string);
        return substr(str_shuffle($original_string), 0, $length);
    }

    public function create_bot(Request $request, $user_id)
    {
        $user = User::find($user_id);
        if($request->isMethod('POST')) {

            $company = new Company();
            $company->user_id = $user->id;
            $company->slug = $request->slug;
            $company->name = $request->name;
            $company->description = $request->description;
            $company->save();

            $bot = new Bot();
            $bot->type = 'bot';
            $bot->botable_id = $company->id;
            $bot->botable_type = 'App\\Models\\Company';
            $bot->name = $request->name;
            $bot->active = 0;
            $bot->save();

            $bi_array = $this->get_bi_array();

            foreach ($bi_array as $i => $k) {
                $bot_input = new BotInput();
                $bot_input->bot_id = $bot->id;
                $bot_input->data = $k["data"];
                $bot_input->type = "GurmanAlexander\\TheBot\\Models\\Inputs\\RegularInput";
                $bot_input->save();
            }

            return redirect()->route('manager.users.show', $user->id);
        } else {
            return view('manager.bots.create', ['user' => $user]);
        }
    }

    public function createBotOnExist($company_id, $type_id)
    {
        $company = Company::findOrFail($company_id);

        $bot = new Bot();
        if($type_id == 1) {
            $bot->type = 'bot';
            $bot->name = $company->name ?? 'Новый авточат';
            $bot->active = 0;
        } else if($type_id == 2) {
            $bot->type = 'multilink';
            $bot->name = $company->name ?? 'Multilink';
            $bot->active = 1;
        }
        $bot->botable_id = $company->id;
        $bot->botable_type = 'App\\Models\\Company';
        $bot->save();

        $bi_array = $this->get_bi_array();

        foreach ($bi_array as $i => $k) {
            $bot_input = new BotInput();
            $bot_input->bot_id = $bot->id;
            $bot_input->data = $k["data"];
            $bot_input->type = "GurmanAlexander\\TheBot\\Models\\Inputs\\RegularInput";
            $bot_input->save();
        }

        return response()->json(['bot_id' => $bot->id]);
    }

    public function invoice($id)
    {


//        $arr = ['manager_id' => $manager, 'user_id' => $user->id];
//        $client = new Client(['headers' => ['Content-Type' => 'application/json', 'Authorization' => 'Basic ' . config('app.billing_token')]]);
//        $URI = config('app.billing_url') . '/ref/get-ref';
//        $response = $client->post($URI, [
//            'form_params' => $arr,
//            'headers' => [
//                'Content-Type' => 'application/x-www-form-urlencoded',
//            ]
//        ]);
//        $response = $client->post($URI);
//        $ref = json_decode($response->getBody());
//        dd($ref->data);
//        if($ref) {
//
//        } else {
//
//        }
//        return view('manager.users.invoice', ['manager_id' => $manager,
//                                                    'user' => $user,
//                                                    'plans' => $plans,
//                                                    'services_service' => $services_service,
//                                                    'services_bot' => $services_bot,
//                                                    'services_bonus' => $services_bonus]);
    }

    public function new_invoice($user_id)
    {
        $manager = Auth::id();
        $user = User::findOrFail($user_id);
        $old_invoice = BillingInvoice::where('user_id', $user_id)->where('paid', 1)->whereNotNull('ref_options')->orderByDesc('id')->first();
//        dd($old_invoice);
        $plans = BillingPlan::whereIn('id', [4, 5, 6])->get();
        $services_service = BillingService::findOrFail(1);
        $services_bot = BillingService::findOrFail(2);
        $services_bonus = BillingService::findOrFail(3);

        return view('manager.users.new_invoice', ['manager_id' => $manager,
            'user' => $user,
            'plans' => $plans,
            'services_service' => $services_service,
            'services_bot' => $services_bot,
            'services_bonus' => $services_bonus]);
    }

    /*пометка на удаление ссылки и всех авточатов*/
    public function delete_full($id, $user_id, $bot_id)
    {
        $company = Company::find($id);
        if($company) {
            $bots = Bot::where('botable_id', $company->id)->get();
            if($bots) {
                foreach ($bots as $bot)
                    $bot->delete();
            }
            $company->delete();
            return redirect()->route('manager.users.show', ['id' => $user_id]);
        }
    }

    /*пометка на удаление авточата*/
    public function delete_chat($id, $user_id, $bot_id)
    {
        $company = Company::find($id);
        if($company) {
            $bot = $company->bots()->where('id', $bot_id)->first();
            if($bot) {
                $bot->delete();
            } else {
                abort(404);
            }
            return redirect()->route('manager.users.show', ['id' => $user_id]);
        }
    }

    /*Жесткое удаление всех ссылок и авточатов*/
    public function delete_force($id, $user_id, $bot_id)
    {
        $company = Company::find($id);
        if($company) {
            $bots = Bot::where('botable_id', $company->id)->get();
            if($bots) {
                foreach ($bots as $bot)
                    $bot->forceDelete();
            }
            $company->forceDelete();
            return redirect()->route('manager.users.show', ['id' => $user_id]);
        }
    }

    public function change_email(Request $request, $user_id)
    {

        $user = User::findOrFail($user_id);
        $dbl_email = User::where('email', $request->email)->first();
        if(!$dbl_email) {
            if($user->email != $request->email) {
                $user->email = $request->email;
                $user->save();
                return response()->json(['error' => 0, 'message' => 'Данные успешно сохранены']);
            } else {
                return response()->json(['error' => 0, 'message' => 'Данные не изменены']);
            }
        } else {
            if($dbl_email->id == $user->id) {
                return response()->json(['error' => 1, 'message' => 'Данный email уже существует']);
            } else {
                return response()->json(['error' => 1, 'message' => 'Данный email принадлежит другому пользователю']);
            }
        }

    }

    public function change_phone(Request $request, $user_id)
    {
        $phone_number = str_replace(['+', '-', '(', ')'. ' ', '_'], '', $request->phone);
        $user = User::findOrFail($user_id);
        $phone = Phone::where('user_id', $user_id)->first();
        $dbl_phone = Phone::where('phone', $phone_number)->first();
        if(!$dbl_phone) {
            if($phone) {
//                return response()->json(['phone' => $phone->phone]);
                if($request->phone != $phone->phone) {
                    $phone->country_code = $request->country_code;
                    $phone->phone = $phone_number;
                    $phone->save();
                    //изменяем username - там записан номер телефона без +, пробелов и прочего
                    $user->username = str_replace(['+', '-', '(', ')'. ' '], '', $request->country_code.$request->phone);
                    $user->save();
                    return response()->json(['error' => 0, 'message' => 'Данные успешно сохранены']);
                }
            } else {
                $phone = new Phone();
                $phone->country_code = $request->country_code;
                $phone->phone = $phone_number;
                $phone->save();
                //изменяем username - там записан номер телефона без +, пробелов и прочего
                $user->username = str_replace(['+', '-', '(', ')'. ' '], '', $request->country_code.$request->phone);
                $user->save();
                return response()->json(['error' => 0, 'message' => 'Данные успешно добавлены']);
            }
        } else {
            if($dbl_phone->user_id == $user->id) {
                return response()->json(['error' => 1, 'message' => 'Данный номер уже существует']);
            } else {
                return response()->json(['error' => 1, 'message' => 'Данный номер принадлежит другому пользователю']);
            }
        }
    }

    public function change_profile(Request $request, $user_id)
    {
        $profile = Profile::where('user_id', $user_id)->first();
        if($profile) {
            $profile->first_name = $request->first_name;
            $profile->last_name = $request->last_name;
            $profile->company = $request->company;
            $profile->location = $request->location;
            $profile->save();
            return response()->json(['error' => 0, 'message' => 'Данные успешно сохранены']);
        } else {
            $profile = new Profile();
            $profile->first_name = $request->first_name;
            $profile->last_name = $request->last_name;
            $profile->company = $request->company;
            $profile->location = $request->location;
            $profile->save();
            return response()->json(['error' => 0, 'message' => 'Данные успешно добавлены']);
        }
    }

    public function change_password(Request $request, $user_id)
    {
        $user = User::findOrFail($user_id);

        if($request->password == $request->confirm_password) {

            if(strlen($request->password) > 6) {
                $user->password = Hash::make($request->password);
                $user->save();
//                $user->notify(new UserRegistered($user, $user->password));
                return response()->json(['error' => 0, 'message' => 'Пароль успешно изменен']);
            } else {
                return response()->json(['error' => 1, 'message' => 'Пароль слишком короткий']);
            }
        } else {
            return response()->json(['error' => 1, 'message' => 'Значения в полях не совпадают']);
        }
    }

    public function addFreePlansAll()
    {
        $users = User::all();
        $plan = BillingPlan::where('code', 'free')->first();
        foreach ($users as $user) {
            $subscribe = BillingSubscribe::where('user_id', $user->id)->first();
            if(is_null($subscribe)) {
                $ns = new BillingSubscribe();
                $ns->user_id = $user->id;
                $ns->plan_id = $plan->id;
                $ns->interval = $plan->interval;
                $ns->start_at = Carbon::now();
                $ns->active = 1;
                $ns->save();
            }
        }
    }

}
