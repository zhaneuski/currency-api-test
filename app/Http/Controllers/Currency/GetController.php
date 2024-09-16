<?php

namespace App\Http\Controllers\Currency;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;

class GetController extends Controller
{

    public function __invoke(Request $request)
    {
        $currency = Currency::latest()->first();

        $data = json_decode($currency->data, true);


        return view('admin.dashboard', $data);
    }
}
