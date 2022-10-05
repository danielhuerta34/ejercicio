<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $url = 'https://bitpay.com/api/rates';
        $json = json_decode(file_get_contents($url));

        foreach ($json as $obj) {
            if ($obj->code == 'USD') $btc = $obj->rate;
        }

        $history = DB::table("history")->get();

        return view('home', [
            'btc' => $btc,
            'history' => $history
        ]);
    }

    public function obtenerBitcoin(Request $request)
    {

        if (!$request->isMethod('post')) {
            return redirect()->route('inicio');
        }
        date_default_timezone_set("America/Santiago");
        $fecha = date('Y-m-d');
        $hora = date('G:i:s');

        $url = 'https://bitpay.com/api/rates';
        $json = json_decode(file_get_contents($url));

        foreach ($json as $obj) {
            if ($obj->code == 'USD') $btc = $obj->rate;
        }

        DB::table('history')->insert([
            'usd' => $btc,
            'date' => $fecha,
            'hour' => $hora
        ]);

        return array("bitcoin" => $btc, "fecha" => $fecha, "hora" => $hora);
    }
}
