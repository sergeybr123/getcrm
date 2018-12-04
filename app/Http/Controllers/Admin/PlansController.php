<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;

class PlansController extends Controller
{
    public function index()
    {
        $token = config('app.billing_token');
        $billing_url = config('app.billing_url');
        $client = new Client(['headers' => ['Content-Type' => 'application/json', 'Authorization' => 'Basic ' . $token]]);
        $url = $billing_url.'/plan/all';
        $response = $client->get($url);
        $plans = json_decode($response->getBody());
        return view('admin.plans.index', ['plans' => $plans->data]);
    }

    public function create(Request $request)
    {
        if($request->isMethod('POST')) {
            $client = new Client(['headers' => ['Content-Type' => 'application/json', 'Authorization' => 'Basic ' . config('app.billing_token')]]);
            $URI = config('app.billing_url') . '/plans';
            $response = $client->post($URI, [
                'body' => $request,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ]
            ]);
            $resp = json_decode($response->getBody());
            if($resp->error == 0) {
                return redirect()->route('admin::plans::index');
            } else {
                return redirect()->route('admin::plans::create');
            }
        } else {
            return view('admin.plans.create');
        }
    }

    public function update(Request $request, $id)
    {
        if($request->isMethod('POST')) {
            $client = new Client(['headers' => ['Content-Type' => 'application/json', 'Authorization' => 'Basic ' . config('app.billing_token')]]);
            $URI = config('app.billing_url') . '/plans/' . $id;
            $response = $client->put($URI, [
                'body' => $request,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ]
            ]);
            $resp = json_decode($response->getBody());
            if($resp->error == 0) {
                return redirect()->route('admin::plans::index');
            } else {
                return redirect()->route('admin::plans::edit', $id);
            }
        } else {
            $token = config('app.billing_token');
            $billing_url = config('app.billing_url');
            $client = new Client(['headers' => ['Content-Type' => 'application/json', 'Authorization' => 'Basic ' . $token]]);
            $url = $billing_url.'/plans/' . $id;
            $response = $client->get($url);
            $plan = json_decode($response->getBody());
            if(!isset($plan->error)){
                return view('admin.plans.edit', ['plan' => $plan->data]);
            } else {
                return redirect()->route('admin::plans::index');
            }
        }
    }

    public function delete($id)
    {
        $client = new Client(['headers' => ['Content-Type' => 'application/json', 'Authorization' => 'Basic ' . config('app.billing_token')]]);
        $URI = config('app.billing_url') . '/plans/' . $id;
        $response = $client->delete($URI);
        $req = json_decode($response->getBody());
        if($req->error == 0) {
            return redirect()->route('admin::plans::index');
        }
//        dd(json_decode($response->getBody()));
    }
}
