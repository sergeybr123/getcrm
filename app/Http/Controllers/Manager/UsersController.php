<?php

namespace App\Http\Controllers\Manager;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;

use App\User;
use App\Models\Company;
use App\Models\Phone;
use App\Models\Bot;
use App\Models\Account;
use App\Models\Profile;
use PragmaRX\Countries\Package\Countries;
use Carbon\Carbon;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $type = $request->type;
        $text = $request->text;
//        dd($search);
        if($text != null) {
            if($type == 1) {
                $users = User::where('email', 'LIKE', '%' . $text . '%')->with('phone')->orderBy('id', 'desc')->paginate(30);
            }
            if($type == 2) {
                $phone = substr($text, -10, 10);
//                dd($phone);
                $query = Phone::where('phone', $phone)->pluck('user_id');
                $users = User::whereIn('id', $query)->with('phone')->orderBy('id', 'desc')->paginate(30);
            }
//            if($type == 3) {
//                $users = User::where('created_at', 'LIKE', '%' . Carbon::parse($text)->format('Y-m-d') . '%')->with('phone')->orderBy('id', 'desc')->paginate(30);
//            }

//            dd($users);
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
        return view('manager.users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $password = $this->generatePassword();
        $user = User::create([
            'name' => $request->name,
            'username' => $request->phone,
            'email' => $request->email,
            'password' => bcrypt($password),
        ]);
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
        $bots = Company::where('user_id', $id)->whereNotNull('bot')->whereNull('deleted_at')->get();
        $pages = Company::where('user_id', $id)->whereNull('bot')->whereNull('deleted_at')->get();

        $client = new Client(['headers' => ['Content-Type' => 'application/json', 'Authorization' => 'Basic ' . config('app.billing_token')]]);
        $URI = config('app.billing_url') . '/subscribe/' . $user->id;
        $response = $client->get($URI);
        $plan = json_decode($response->getBody());

//        dd($plan);

        $client1 = new Client(['headers' => ['Content-Type' => 'application/json', 'Authorization' => 'Basic ' . config('app.billing_token')]]);
        $url_inv = config('app.billing_url') . '/user-invoice/' . $user->id;
        $resp_inv = $client1->get($url_inv);
        $invoices = json_decode($resp_inv->getBody());

//        dd($invoices);
        return view('manager.users.show', ['user' => $user, 'bots' => $bots, 'pages' => $pages, 'plan' => $plan->data, 'invoices' => $invoices]);
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
        return view('manager.users.edit', ['user' => $user]);
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
        $this->validate($request, [
            'id' => 'required|numeric',
            'email' => [
                'required',
                'email',
//                Rule::unique('users')->ignore($request->id)
            ],
            'country_code' => 'required',
            'phone' => [
                'required',
                'numeric',
//                Rule::unique('phones')->ignore($request->id, 'user_id')
            ]
        ]);

        $user = User::findOrFail($id);

        $user->email = $request->email;

        $country_code = explode('-', $request->country_code);
        $cca2 = $country_code[0];
        $code = $country_code[0];

        $phone = $user->phone()->first();
        if (!$phone) {
            $phone = new Phone();
            $phone->user_id = $user->id;
        }
        $phone->cca2 = $cca2;
        $phone->country_code = $code;
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
//    public function selectNotSubscribed()
//    {
////        set_time_limit(0);
//        /*Раскомментировать рабочий код, не удалять*/
//        $users = Company::whereNull('bot')->whereNull('deleted_at')->select('user_id')->distinct()->get();
//        foreach ($users as $user) {
//            if(Bot::where('botable_id', $user->user_id)->doesntExist())
//            {
//                $client = new Client(['headers' => ['Content-Type' => 'application/json', 'Authorization' => 'Basic ' . config('app.billing_token')]]);
//                $URI = config('app.billing_url') . '/subscribe/free/' . $user->id;
//                $response = $client->get($URI);
//                $plan = json_decode($response->getBody());
//            }
//        }
//    }


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
            return redirect()->route('manager.users.show', ['id' => $request->user_id]);
        } else {
            dd($resp);
        }
    }
}
