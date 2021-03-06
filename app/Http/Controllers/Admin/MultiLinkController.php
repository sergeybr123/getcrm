<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Company;
use App\Models\Account;
use App\Models\Bot;
use App\Models\BotAnswer;
use App\Models\BotListener;

class MultiLinkController extends Controller
{
    public function change_multilink()
    {
        $whatsapp = Account::where('company_id', '>', 35068)->where('type', 'WhatsApp')->get();

        foreach ($whatsapp as $item) {
            $company = Company::where('id', $item->company_id)->where('id', '>', 35068)->whereNull('bot')->whereNull('temp_bot')->first();

            if($company != null) {
                $bot = new Bot();
                $bot->type = "multilink";
                $bot->botable_id = $company->id;
                $bot->botable_type = "App\\Models\\Company";
                $bot->name = $company->slug;
                $bot->active = true;
                $bot->save();

                $bot_listener = new BotListener();
                $bot_listener->bot_id = $bot->id;
                $bot_listener->text = "welcome";
                $bot_listener->save();

                $bot_answer = new BotAnswer();
                $bot_answer->bot_listener_id = $bot_listener->id;
                $bot_answer->data = ['text' => "Мы рады приветствовать вас!"];
                $bot_answer->type = "GurmanAlexander\\TheBot\\Models\\Answers\\TextAnswer";
                $bot_answer->order = 1;
                $bot_answer->save();

                $bot_answer = new BotAnswer();
                $bot_answer->bot_listener_id = $bot_listener->id;
                $bot_answer->data = ['text' => "Whatsapp", 'type' => 'whatsapp', 'phone' => str_replace(['+', ' ', '-', '(', ')', '_'], '', $item->account_service_id), 'wa_text' => 'Привет'];
                $bot_answer->type = "GurmanAlexander\\TheBot\\Models\\Answers\\ActionAnswer";
                $bot_answer->order = 2;
                $bot_answer->save();
            }
        }
        try{
            return response()->json(['error' => 0, 'whatsapp' => count($whatsapp)]);
        } catch (\Error $e) {
            return response()->json(['error' => $e]);
        }
    }
}
