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
        $whatsapp = Account::where('type', 'WhatsApp')->get();

        foreach ($whatsapp as $item) {
            $company = Company::findOrFail($item->company_id);

            $bot = new Bot();
            $bot->type = "multilink";
            $bot->botable_id = $item->company_id;
            $bot->botable_type = "App\\Models\\Company";
            $bot->name = "MultiLink " . $company->slug;
            $bot->description = "MultiLink";
            $bot->save();

            $bot_listener = new BotListener();
            $bot_listener->bot_id = $bot->id;
            $bot_listener->text = "welcome";
            $bot_listener->save();

            $bot_answer = new BotAnswer();
            $bot_answer->bot_listener_id = $bot_listener->id;
            $bot_answer->data = ['text' => "Whatsapp", 'type' => 'whatsapp', 'phone' => str_replace(['+', ' ', '-', '(', ')', '_'], '', $item->account_service_id)];
            $bot_answer->type = "GurmanAlexander\\TheBot\\Models\\Answers\\ActionAnswer";
            $bot_answer->save();
        }
        try{
            return response()->json(['error' => 0, 'whatsapp' => count($whatsapp)]);
        } catch (\Error $e) {
            return response()->json(['error' => $e]);
        }
    }
}
