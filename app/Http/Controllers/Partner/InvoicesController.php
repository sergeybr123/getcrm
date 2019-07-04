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
}
