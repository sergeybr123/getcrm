<?php

namespace App\Http\Controllers\Manager;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use GuzzleHttp\Client;

use App\User;
use App\Models\Phone;
use App\Models\Company;

class InvoicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = [];

        if($request->searchFilter == null && $request->searchText == null) {
            if(isset($request->page)) {
                $page = $request->page;
            } else {
                $page = 1;
            }
            $client = new Client();
            $URI = config('app.billing_url') . '/invoice?page=' . $page;
            $params['headers'] = ['Content-Type' => 'application/json', 'Authorization' => 'Basic ' . config('app.billing_token')];
            $response = $client->get($URI, $params, ['stream' => true]);
            $invoices = json_decode($response->getBody());
            if($invoices != null) {
                foreach ($invoices->data as $invoice) {
                    $user = User::where('id', $invoice->user_id)->with('phone')->first();
                    array_push($users, [$user, $invoice]);
                }
                return view('manager.invoices.index', ['invoices' => $users, 'all' => $invoices]);
            } else {
                dd($invoices);
            }
        } else {
            // Если поиск по номеру счета
            if($request->searchFilter == 1) {
                $client = new Client(['headers' => ['Content-Type' => 'application/json', 'Authorization' => 'Basic ' . config('app.billing_token')]]);
                $URI = config('app.billing_url') . '/invoice/' . $request->searchText;
                $response = $client->get($URI);
                $invoices = json_decode($response->getBody());
                if($invoices != null) {
                    foreach ($invoices as $invoice) {
                        $user = User::where('id', $invoice->user_id)->with('phone')->first();
                        array_push($users, [$user, $invoice]);
                    }
                    return view('manager.invoices.index', ['invoices' => $users, 'all' => $invoices]);
                } else {
                    dd($invoices);
                }

            } else {
                // Если поиск по email пользователя
                if($request->searchFilter == 2) {
                    $user_id = User::where('email', 'LIKE', '%' . $request->searchText . '%')->pluck('id');
                }
                // Если поиск по номеру телефона пользователя
                if($request->searchFilter == 3) {
                    $phone = substr($request->searchText, -10, 10);
                    $user_id = Phone::where('phone', $phone)->pluck('user_id');
                }

                $client = new Client(['headers' => ['Content-Type' => 'application/json', 'Authorization' => 'Basic ' . config('app.billing_token')]]);
                $URI = config('app.billing_url') . '/user-invoice/' . $user_id[0];
                $response = $client->get($URI);
//                dd($URI);
                $invoices = json_decode($response->getBody());
                if($invoices != null) {
                    foreach ($invoices->data as $invoice) {
                        $user = User::where('id', $invoice->user_id)->with('phone')->first();
                        array_push($users, [$user, $invoice]);
                    }
                    return view('manager.invoices.index', ['invoices' => $users, 'all' => $invoices]);
                } else {
                    dd($invoices);
                }
            }
        }



    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
}
