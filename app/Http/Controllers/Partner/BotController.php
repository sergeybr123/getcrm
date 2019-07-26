<?php

namespace App\Http\Controllers\Partner;

use App\Models\BillingSubscribe;
use App\Models\Bot;
use App\Models\BotInput;
use App\Models\BotSavingData;
use App\Models\Company;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BotController extends Controller
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

    public function index()
    {
        $user = Auth::user();
        $companies = Company::where('user_id', $user->id)->whereHas('bots', function ($query){ $query->where('type', 'bot'); })->with('bots')->orderBy('slug')->get();
//        $subscribe = BillingSubscribe::where('user_id', $user->id)->with('plan')->first();
        $client = new Client(['headers' => ['Content-Type' => 'application/json', 'Authorization' => 'Basic ' . config('app.billing_token')]]);
        /*-----Получаем подписку-----*/
        $URI = config('app.billing_url') . '/subscribe/' . $user->id;
        $response = $client->get($URI);
        $subscribe = json_decode($response->getBody());
//        dd($subscribe);
        $plan_bot_count = 0;
        $new_bot_count = 0;
        if($subscribe){
            $plan_bot_count = $subscribe->data->plan->bot_count;
            foreach ($subscribe->data->additional as $additional) {
                $plan_bot_count += $additional->quantity;
            }
            foreach ($companies as $company) {
                foreach ($company->bots as $new_bot) {
                    if($new_bot->active == 1)
                        $new_bot_count++;
                }
            }
        }
        return view('partner.bots.index', ['user' => $user, 'subscribe' => $subscribe->data, 'companies' => $companies, 'plan_bot_count' => $plan_bot_count, 'new_bot_count' => $new_bot_count]);
    }

    public function edit_slug(Request $request)
    {
        $company = Company::findOrFail($request->company_id);
        $slug_c = Company::where('slug', $request->slug)->first();
        if(!$slug_c) {
            $company->slug = $request->slug;
            $company->save();
            return response()->json(['error' => 0]);
        } else {
            return response()->json(['error' => 1]);
        }
    }

    /*-----Показать данные авточата-----*/
    public function bot_data($bot_id)
    {
        $user = Auth::user();
        $company = Company::findOrFail($bot_id);
        $message = "";
        $data = null;
        if($company->user_id == $user->id) {
            $data = BotSavingData::where('company_id', $bot_id)->orderBy('created_at', 'desc')->get();
//            $data = json_encode($query->data);
//            dd($data);
        } else {
            $message = "Доступ к данным запрещен!!!";
        }
        return view('partner.bots.data', ['company' => $company, 'message' => $message, 'data' => $data]);
    }

    /*-----Добавить новую ссылку и авточат-----*/
    public function create_company(Request $request)
    {
        $query = Company::where('slug', $request->slug)->first();
        if(is_null($query)) {
            $user = Auth::user();

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
            return response()->json(['error' => 0]);
        } else {
            return response()->json(['error' => 1]);
        }
    }

    /*-----Добавить новый авточат к ссылке-----*/
    public function create_bot(Request $request)
    {
        try {
            $bot = new Bot();
            $bot->type = 'bot';
            $bot->botable_id = $request->company_id;
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
            return response()->json(['error' => 0]);
        }
        catch (exception $e) {
            return response()->json(['error' => 1]);
        }
    }

    /*-----Копирование авточата-----*/
    public function postCopyBot(Request $request)
    {
        $user = Auth::user();
        $email = $request->user_email;
        $link = $request->link;
        $template_id = $request->template_id;

        $get_link = Company::where('slug', $link)->first();
        if($get_link != null && $get_link->user_id == $user->id) {
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
            return response()->json(['error' => 1, 'message' => 'Вы не можете копировать на данную ссылку!']);
        }
    }

    /*-----Пометка на удаление ссылки и всех авточатов, мультилинков-----*/
    public function delete_full($id)
    {
        $company = Company::findOrFail($id);
        if($company) {
            $bots = Bot::where('botable_id', $company->id)->get();
            if($bots) {
                foreach ($bots as $bot) {
                    $bot->delete();
                }
            }
            $company->delete();
            return redirect()->route('partner::bots::index');
        } else {
            abort(404);
        }
    }

    /*пометка на удаление авточата*/
    public function delete_bot($id)
    {
        $bot = Bot::findOrFail($id);
        if($bot) {
            $bot->delete();
            return redirect()->route('partner::bots::index');
        } else {
            abort(404);
        }
    }
}
