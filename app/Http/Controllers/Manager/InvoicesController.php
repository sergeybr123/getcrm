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
        $filter = $request->searchFilter;
        $search = $request->searchText;

        $users = [];

        if($request->searchText == null) {
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
            } else {
                dd($invoices);
            }
        } else {
            // Если поиск по номеру счета
            if($filter == 1 && $search != '') {
                $client = new Client(['headers' => ['Content-Type' => 'application/json', 'Authorization' => 'Basic ' . config('app.billing_token')]]);
                $URI = config('app.billing_url') . '/invoice/' . $search;
                $response = $client->get($URI);
                $invoices = json_decode($response->getBody());
//                dd($invoices);
                if($invoices != null) {
                    foreach ($invoices as $invoice) {
                        $user = User::where('id', $invoice->user_id)->with('phone')->first();
                        array_push($users, [$user, $invoice]);
                    }
//                    return view('manager.invoices.index', ['invoices' => $users, 'all' => $invoices]);
                } else {
                    dd($invoices);
                }

            } else {
                // Если поиск по email пользователя
                if($filter == 2 && $search != '') {
                    $user = User::where('email', 'LIKE', '%' . $search . '%')->first();
                }
                // Если поиск по номеру телефона пользователя
                if($filter == 3 && $search != '') {
                    $phone = substr($search, -10, 10);
                    $user = Phone::where('phone', $phone)->first();
                }

                if($user != '') {
//                    dd($user);
                    $client = new Client(['headers' => ['Content-Type' => 'application/json', 'Authorization' => 'Basic ' . config('app.billing_token')]]);
                    $URI = config('app.billing_url') . '/user-invoice/' . $user->id;
                    $response = $client->get($URI);
//                dd($URI);
                    $invoices = json_decode($response->getBody());
                    if($invoices != null) {
                        foreach ($invoices->data as $invoice) {
                            $user = User::where('id', $invoice->user_id)->with('phone')->first();
                            array_push($users, [$user, $invoice]);
                        }
                        // return view('manager.invoices.index', ['invoices' => $users, 'all' => $invoices]);
                    } else {

                    }
                } else {
                    $invoices = [];
                    $users = [];
                }
            }
        }
//        dd($invoices);

        return view('manager.invoices.index', ['invoices' => $users, 'all' => $invoices, 'filter' => $filter, 'search' => $search]);

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
