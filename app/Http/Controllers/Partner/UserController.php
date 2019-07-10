<?php

namespace App\Http\Controllers\Partner;

use App\Models\Phone;
use App\Models\Profile;
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
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
//        $subscribe = BillingSubscribe::where('user_id', $user->id)->with(['plan', 'additionals'])->first();
        $client = new Client(['headers' => ['Content-Type' => 'application/json', 'Authorization' => 'Basic ' . config('app.billing_token')]]);
        $URI = config('app.billing_url') . '/subscribe/' . $user->id;
        $response = $client->get($URI);
        $subscribe = json_decode($response->getBody());
//        dd($subscribe->data);
        $profile = Profile::where('user_id', $user->id)->first();
        if(!$profile) {
            $profile = new Profile();
            $profile->user_id = $user->id;
            $profile->save();
        }
        $user_phone = Phone::where('user_id', $user->id)->first();
        $path = 'js/phone.json';
        $phones = json_decode(file_get_contents($path), true);
        return view('partner.index', ['user' => $user, 'subscribe' => $subscribe->data, 'profile' => $profile, 'user_phone' => $user_phone, 'phones' => $phones]);
    }

    public function edit(Request $request)
    {
        $auth = Auth::user();
        $user = User::findOrFail($auth->id);
        $phone = Phone::where('user_id', $auth->id)->get();
        $profile = Profile::where('user_id', $auth->id)->first();

        if($request->email) {
            if($request->email != $user->email) {
                $user->email = $request->email;
                $user->save();
            }
        }

        if($request->phone) {
            if($request->phone != $phone->phone) {
                $phone->country_code = $request->country_code;
                $phone->phone = $request->phone;
                $phone->save();
                $user->username = str_replace(['+', '-', '(', ')'. ' '], '', $request->country_code.$request->phone);
                $user->save();
            }
        }
    }

    public function change_email(Request $request)
    {

        $user = User::findOrFail(Auth::user()->id);
        $dbl_email = User::where('email', $request->email)->first();
        if(!$dbl_email) {
            if($user->email != $request->email) {
                $user->email = $request->email;
                $user->save();
                return response()->json(['error' => 0, 'message' => 'Данные успешно сохранены']);
            } else {
                return response()->json(['error' => 0, 'message' => 'Данные не изменены']);
            }
        } else {
            if($dbl_email->id == $user->id) {
                return response()->json(['error' => 1, 'message' => 'Данный email уже существует']);
            } else {
                return response()->json(['error' => 1, 'message' => 'Данный email принадлежит другому пользователю']);
            }
        }

    }

    public function change_phone(Request $request)
    {
        $phone_number = str_replace(['+', '-', '(', ')'. ' ', '_'], '', $request->phone);
        $user = User::findOrFail(Auth::user()->id);
        $phone = Phone::where('user_id', $user->id)->first();
        $dbl_phone = Phone::where('phone', $phone_number)->first();
        if(!$dbl_phone) {
            if($phone) {
//                return response()->json(['phone' => $phone->phone]);
                if($request->phone != $phone->phone) {
                    $phone->country_code = $request->country_code;
                    $phone->phone = $phone_number;
                    $phone->save();
                    //изменяем username - там записан номер телефона без +, пробелов и прочего
                    $user->username = str_replace(['+', '-', '(', ')'. ' '], '', $request->country_code.$request->phone);
                    $user->save();
                    return response()->json(['error' => 0, 'message' => 'Данные успешно сохранены']);
                }
            } else {
                $phone = new Phone();
                $phone->country_code = $request->country_code;
                $phone->phone = $phone_number;
                $phone->save();
                //изменяем username - там записан номер телефона без +, пробелов и прочего
                $user->username = str_replace(['+', '-', '(', ')'. ' '], '', $request->country_code.$request->phone);
                $user->save();
                return response()->json(['error' => 0, 'message' => 'Данные успешно добавлены']);
            }
        } else {
            if($dbl_phone->user_id == $user->id) {
                return response()->json(['error' => 1, 'message' => 'Данный номер уже существует']);
            } else {
                return response()->json(['error' => 1, 'message' => 'Данный номер принадлежит другому пользователю']);
            }
        }
    }

    public function change_profile(Request $request)
    {
        $profile = Profile::where('user_id', Auth::user()->id)->first();
        if($profile) {
            $profile->first_name = $request->first_name;
            $profile->last_name = $request->last_name;
            $profile->company = $request->company;
            $profile->location = $request->location;
            $profile->save();
            return response()->json(['error' => 0, 'message' => 'Данные успешно сохранены']);
        } else {
            $profile = new Profile();
            $profile->first_name = $request->first_name;
            $profile->last_name = $request->last_name;
            $profile->company = $request->company;
            $profile->location = $request->location;
            $profile->save();
            return response()->json(['error' => 0, 'message' => 'Данные успешно добавлены']);
        }
    }

    public function change_password(Request $request)
    {
        $user = User::findOrFail(Auth::user()->id);

        if($request->password == $request->confirm_password) {

            if(strlen($request->password) > 6) {
                $user->password = Hash::make($request->password);
                $user->save();
                return response()->json(['error' => 0, 'message' => 'Пароль успешно изменен']);
            } else {
                return response()->json(['error' => 1, 'message' => 'Пароль слишком короткий']);
            }
        } else {
            return response()->json(['error' => 1, 'message' => 'Значения в полях не совпадают']);
        }
    }

//    public function show($id)
//    {
//        $user = User::find($id);
//        $bots = Company::where('user_id', $id)->whereNotNull('bot')->whereNull('deleted_at')->get();
//        $pages = Company::where('user_id', $id)->whereNull('bot')->whereNull('deleted_at')->get();
//
//        $client = new Client(['headers' => ['Content-Type' => 'application/json', 'Authorization' => 'Basic ' . config('app.billing_token')]]);
//
//        // Получаем подписку пользователя
////        $URI = config('app.billing_url') . '/subscribe/' . $user->id;
////        $response = $client->get($URI);
////        $subscribe_resp = json_decode($response->getBody());
////        $subscribe = $subscribe_resp->data;
//        $subscribe = BillingSubscribe::where('user_id', $id)->first();
//
//
//        // Позже переделать
////        $url_plans = config('app.billing_url') . '/plan/all';
////        $resp_plans = $client->get($url_plans);
////        $plans = json_decode($resp_plans->getBody());
//        $plans = BillingPlan::all();
//
//        // Получаем все счета пользователя
//        $url_inv = config('app.billing_url') . '/user-invoice/' . $user->id;
//        $resp_inv = $client->get($url_inv);
//        $invoices_resp = json_decode($resp_inv->getBody());
//        $invoices = $invoices_resp->data;
//
//
//
//        return view('partner.users.show', ['user' => $user, 'bots' => $bots, 'pages' => $pages, 'subscribe' => $subscribe, 'plans' => $plans, 'invoices' => $invoices]);
//    }
//
//
//    public function createBot(Request $request)
//    {
//        $user = User::where('email', $request->email)->first();
//
//        if($request->isMethod('POST')){
//
//            $request->validate($request, [
//                'slug' => 'required|string|between:5,50|unique:companies|regex:/^[a-z0-9\-\_]+$/iu',
//                'email' => 'email',
//            ]);
//
//            $partner = Auth::user();
//            /*----Создаем пустого бота-----*/
//            $company = new Company;
//            $company->user_id = $user->id;
//            $company->slug = $request->slug;
//            $company->bot = "{}";
//            $company->save();
//            /*----Создаем запись, что это партнерский бот-----*/
//            $pb = new PartnersCompany;
//            $pb->user_id = $partner->id;
//            $pb->company_id = $company->id;
//            $pb->save();
//            return redirect()->route('partner::users::show', ['id' => $user->id]);
//        } else {
//            return view('partner.users.create_bot', ['user' => $user]);
//        }
//
//    }
}
