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
            if ($obj->code == 'USD') {
                $btc = $obj->rate;
            }
        }

        $datos = DB::table("history")->count();
        if ($datos > 0) {
            $fila_anterior = DB::select(DB::raw('SELECT *
        FROM history
        WHERE id = (SELECT MAX(id)
                    FROM history
                    WHERE id < ' . $btc . ')'));

            $descuento = $fila_anterior[0]->usd - $btc;
            $total = ($descuento / $fila_anterior[0]->usd) * 100;

            DB::table('history')->insert([
                'usd' => $btc,
                'variation' => $total,
                'date' => $fecha,
                'hour' => $hora
            ]);
        } else {
            DB::table('history')->insert([
                'usd' => $btc,
                'variation' => 0,
                'date' => $fecha,
                'hour' => $hora
            ]);
        }

        return array("bitcoin" => $btc, "variacion" => round($total, 3), "fecha" => date("d/m/Y", strtotime($fecha)), "hora" => $hora);
    }
}
