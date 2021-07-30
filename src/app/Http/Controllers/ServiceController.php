<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ServiceController extends Controller
{
    public function info(): Response
    {
        return response('', 200);
    }

    public function addProduct(): Response
    {
        return response('', 200);
    }

    public function addCoin(): Response
    {
        return response('', 200);
    }

    public function collectCoins(): Response
    {
        return response('', 200);
    }
}
