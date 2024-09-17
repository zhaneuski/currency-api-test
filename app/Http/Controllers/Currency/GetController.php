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

        uasort($data, function ($a, $b) {
            return ($b['slug'] ?? 0) <=> ($a['slug'] ?? 0);
        });

        return view('admin.dashboard', [
            'data' => $data ?? [],
        ]);
    }
}
