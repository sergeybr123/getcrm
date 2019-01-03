<?php

namespace App\Http\Controllers\Partner;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\Company;
use App\Models\PartnersCompany;
use App\User;
use App\Models\BillingPlan;
use App\Models\BillingSubscribe;

class UserController extends Controller
{
    public function index()
    {
        $partner = Auth::user();

        $users = PartnersCompany::where('partners_companies.user_id', $partner->id)
            ->join('companies', 'companies.id', 'partners_companies.company_id')
            ->join('users as users', 'users.id', 'companies.user_id')
            ->join('users as my', 'my.id', 'partners_companies.user_id')
            ->join('phones', 'phones.user_id', 'users.id')
            ->select(['users.id', 'users.email', 'users.created_at', 'phones.country_code', 'phones.phone'])
            ->distinct('partners_companies.company_id')
            ->get();
//            ->paginate(30);

//        dd($th[0]->country_code);

        return view('partner.users.index', ['users' => $users]);
    }

    public function show($id)
    {
        $user = User::find($id);
        $bots = Company::where('user_id', $id)->whereNotNull('bot')->whereNull('deleted_at')->get();
        $pages = Company::where('user_id', $id)->whereNull('bot')->whereNull('deleted_at')->get();

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



        return view('partner.users.show', ['user' => $user, 'bots' => $bots, 'pages' => $pages, 'subscribe' => $subscribe, 'plans' => $plans, 'invoices' => $invoices]);
    }


    public function createBot(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if($request->isMethod('POST')){

            $this->validate($request, [
                'slug' => 'required|string|between:5,50|unique:companies|regex:/^[a-z0-9\-\_]+$/iu',
                'email' => 'email',
            ]);

            $partner = Auth::user();
            /*----Создаем пустого бота-----*/
            $company = new Company;
            $company->user_id = $user->id;
            $company->slug = $request->slug;
            $company->bot = "{}";
            $company->save();
            /*----Создаем запись, что это партнерский бот-----*/
            $pb = new PartnersCompany;
            $pb->user_id = $partner->id;
            $pb->company_id = $company->id;
            $pb->save();
            return redirect()->route('partner::users::show', ['id' => $user->id]);
        } else {
            return view('partner.users.create_bot', ['user' => $user]);
        }

    }
}