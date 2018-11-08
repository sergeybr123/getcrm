<?php

namespace App\Http\Controllers\Manager;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use GuzzleHttp\Client;

use App\User;
use App\Models\Phone;
use App\Models\Company;

define("BASE_URL", env('BILLING_URL'));

class InvoicesController extends Controller
{



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(isset($request->page)) {
            $page = $request->page;
        } else {
            $page = 1;
        }

        $users = [];

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
//            dd($users[10][0]->phone->phone);
            return view('manager.invoices.index', ['invoices' => $users, 'all' => $invoices]);
        } else {
            dd($invoices);
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
