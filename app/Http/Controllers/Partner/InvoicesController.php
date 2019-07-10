<?php

namespace App\Http\Controllers\Partner;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;

class InvoicesController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $client = new Client(['headers' => ['Content-Type' => 'application/json', 'Authorization' => 'Basic ' . config('app.billing_token')]]);
        $URI = config('app.billing_url') . '/user-invoice/' . $user->id;
        $response = $client->get($URI);
        $invoices = json_decode($response->getBody());
//        dd($invoices);
        return view('partner.invoices.index', ['user' => $user, 'invoices' => $invoices]);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $client = new Client(['headers' => ['Content-Type' => 'application/json', 'Authorization' => 'Basic ' . config('app.billing_token')]]);
        /*-----Получаем подписку-----*/
        $URI = config('app.billing_url') . '/subscribe/' . $user->id;
        $response = $client->get($URI);
        $subscribe = json_decode($response->getBody());
//        dd($subscribe);
        /*-----Получаем дополнения подписке-----*/
        $URI = config('app.billing_url') . '/additional-type/all';
        $response = $client->get($URI);
        $additional_type = json_decode($response->getBody());
//        dd($additional_type->type);
        if($request->isMethod('POST')) {

        } else {
            return view('partner.invoices.create', ['user' => $user, 'subscribe' => $subscribe->data, 'additional' => $additional_type->type]);
        }
    }

    public function completed($id)
    {
        $client = new Client(['headers' => ['Content-Type' => 'application/json', 'Authorization' => 'Basic ' . config('app.billing_token')]]);
        $URI = config('app.billing_url') . '/other/set-completed/' . $id;
        $response = $client->get($URI);
        $invoice = json_decode($response->getBody());
        return redirect()->route('partner::invoices::index');
    }
}
