<?php

namespace App\Http\Controllers\Admin;

use App\Models\BillingPlan;
use App\Models\Bot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\BillingSubscribe;
use App\User;

class SubscribeController extends Controller
{
    public function index()
    {
        $subscriptions = BillingSubscribe::whereNotIn('plan_id', [1])->
            join('billing.plans', 'subscribes.plan_id', '=', 'plans.id')->
            join('getchat.users', 'subscribes.user_id', '=', 'users.id')->
            join('getchat.phones', 'subscribes.user_id', '=', 'phones.user_id')->
            select('subscribes.id as SubscribeId',
            'users.id as UserId',
            'users.email',
            'plans.name as PlanName',
            'phones.country_code',
            'phones.phone',
            'subscribes.start_at as Start',
            'subscribes.end_at as End',
            'subscribes.active as active')->
            orderBy('subscribes.id', 'desc')->
            paginate(30);
        return view('admin.subscribes.index', ['subscriptions' => $subscriptions]);
    }

    /*-----Всем кто не имеет подписки поставить бесплатную подписку-----*/
    public function setAllFree()
    {
        $plan = BillingPlan::where('code', 'free')->first();
        $users = User::all();
        $count = 0;
        foreach ($users as $user) {
            $subsc = BillingSubscribe::where('user_id', $user->id)->first();
            if(is_null($subsc)) {
                $subscribe = new BillingSubscribe();
                $subscribe->user_id = $user->id;
                $subscribe->plan_id = $plan->id;
                $subscribe->interval = $plan->interval;
                $subscribe->start_at = Carbon::now();
                $subscribe->active = 1;
                $subscribe->save();
                $count++;
            }
        }
        return response()->json(['error' => 0, 'count' => $count]);
    }

    /*-----Изменяем все мультилинки на ботов-----*/
    public function changeMultiOnBot()
    {
        $count = 0;
        $bots = Bot::where('type', 'multilink')->get();
        foreach ($bots as $bot) {
            $bot->type = 'bot';
            $bot->save();
            $count++;
        }
        return response()->json(['error' => 0, 'count' => $count]);
    }
}
