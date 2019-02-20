<?php

namespace App\Http\Controllers;

use Laravel\Passport\Client;
use Illuminate\Http\Request;

class ClientsController extends Controller
{
    public function register(Request $request)
    {
        Client::create();

        return response()->json()->status(201);
    }

}
