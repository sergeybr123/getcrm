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

        $subscriptions = BillingSubscribe::whereNotIn('plan_id', [1, 2, 3, 7, 8])->orderBy('id', 'desc')->paginate(30);

        return view('manager.subscribes.index', [
            'subscriptions' => $subscriptions,
            'active' => $active
        ]);
    }
}
