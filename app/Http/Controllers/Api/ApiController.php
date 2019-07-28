<?php

namespace App\Http\Controllers\Api;

use App\Models\Bot;
use App\Models\BotInput;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Throwable;
use App\User;
use App\Models\Company;

class ApiController extends Controller
{
    // Получаем список пользователей для выборки при смене владельца
    public function getUsers(Request $request)
    {
        $users = User::where('email', 'LIKE', '%'.$request->email.'%')->select(['email'])->get();
        return response()->json($users);
    }

    // Получение формы для изменения владельца
    public function changeOwner(Request $request)
    {
        $company = Company::findOrFail($request->company_id);
        $user = User::where('email', 'LIKE', '%'.$request->user.'%')->first();
        $company->user_id = $user->id;
        $company->save();
        try{
            return response()->json(['error' => 0]);
        } catch (Throwable $th) {
            return response()->json(['error' => 1, 'message' => $th]);
        }
    }

    /*----------Получаем список шаблонов для авточата-----------*/
    public function getTemplates(Request $request)
    {
        $templates = Company::
        join('bots','bots.botable_id','=','companies.id')
            ->select('companies.id as Id', 'companies.slug as Slug', 'companies.created_at as CompanyCreated', 'companies.deleted_at as CompanyDeleted', 'bots.id as BotId', 'bots.type as BotType', 'bots.name as BotName', 'bots.active as BotActive')
            ->where('bots.type', 'bot_template')
            ->whereNull('companies.deleted_at')
            ->paginate(30);
        try{
            return response()->json(['error' => 0, 'templates' => $templates]);
        } catch (Throwable $th) {
            return response()->json(['error' => 1, 'message' => $th]);
        }
    }

    public function create_inputs_if_not_exists()
    {
        $count = 0;
        $b_count = 0;
        $bots = Bot::where('type', 'bot')->get();
        foreach ($bots as $bot) {
            $b_count++;
//            $inputs = BotInput::where('bot_id', $bot->id);
//            if(!$inputs) {
                $bi_array = [
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

                foreach ($bi_array as $i => $k) {
                    $count++;
                    $bot_input = new BotInput();
                    $bot_input->bot_id = $bot->id;
                    $bot_input->data = $k["data"];
                    $bot_input->type = "GurmanAlexander\\TheBot\\Models\\Inputs\\RegularInput";
                    $bot_input->save();
                }
//            }
        }
        return response()->json(['error' => 0, 'bot_count' => $b_count, 'inputs_count' => $count]);
    }
}
