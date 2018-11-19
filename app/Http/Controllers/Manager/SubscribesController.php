<?php

namespace App\Http\Controllers\Manager;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BillingSubscribe;

class SubscribesController extends Controller
{
    public function index()
    {
        $subscriptions = BillingSubscribe::whereNotIn('plan_id', [1, 4])->
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
}
