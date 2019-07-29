<?php

namespace App\Http\Controllers\Manager;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BillingSubscribe;

class SubscribesController extends Controller
{
    public function index(Request $request)
    {
        $active = $request->active;

//        if($active == null) {
            $subscriptions = BillingSubscribe::whereNotIn('plan_id', [1, 8])->
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
//        } elseif($active == 1) {
//            $subscriptions = BillingSubscribe::whereNotIn('plan_id', [1, 4])->where('subscribes.active', $active)->
//            join('billing.plans', 'subscribes.plan_id', '=', 'plans.id')->
//            join('getchat.users', 'subscribes.user_id', '=', 'users.id')->
//            join('getchat.phones', 'subscribes.user_id', '=', 'phones.user_id')->
//            select('subscribes.id as SubscribeId',
//                'users.id as UserId',
//                'users.email',
//                'plans.name as PlanName',
//                'phones.country_code',
//                'phones.phone',
//                'subscribes.start_at as Start',
//                'subscribes.end_at as End',
//                'subscribes.active as active')->
//            orderBy('subscribes.id', 'desc')->
//            paginate(30);
//        } elseif($active == 2) {
//            $subscriptions = BillingSubscribe::whereNotIn('plan_id', [1, 4])->where('subscribes.active', 0)->
//            join('billing.plans', 'subscribes.plan_id', '=', 'plans.id')->
//            join('getchat.users', 'subscribes.user_id', '=', 'users.id')->
//            join('getchat.phones', 'subscribes.user_id', '=', 'phones.user_id')->
//            select('subscribes.id as SubscribeId',
//                'users.id as UserId',
//                'users.email',
//                'plans.name as PlanName',
//                'phones.country_code',
//                'phones.phone',
//                'subscribes.start_at as Start',
//                'subscribes.end_at as End',
//                'subscribes.active as active')->
//            orderBy('subscribes.id', 'desc')->
//            paginate(30);
//        }

        return view('manager.subscribes.index', [
            'subscriptions' => $subscriptions,
            'active' => $active
        ]);
    }
}
