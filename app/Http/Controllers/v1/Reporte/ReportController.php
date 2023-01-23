<?php

namespace App\Http\Controllers\v1\Reporte;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Order;
use App\Models\Pacient;
use App\Models\Pawn;
use App\Models\Planning;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function kpis()
    {
        $pawnsPendientes = Pawn::where('is_acepted', '=', null)->count();
        $pawnsAceptados = Pawn::where('is_acepted', '=', true)->count();
        $pawnsRechazados = Pawn::where('is_acepted', '=', false)->count();
        $clientes = User::where('tipo_user', '=', 'cliente')->count();
        $interesados = User::where('tipo_user', '=', 'interested')->count();
        $administradores = User::where('tipo_user', '=', 'admin')->count();

        return response()->json([
            "status" => "200",
            "message" => 'Datos obtenidos con éxito',
            "data" => [
                "pendientes" => $pawnsPendientes,
                "aceptados" => $pawnsAceptados,
                "rechazados" => $pawnsRechazados,
                'clientes' => $clientes,
                'interesados' => $interesados,
                'administradores' => $administradores
            ],
            "type" => 'success'
        ]);
    }
    public function graph1()
    {
        $orders1 = Order::whereHas('plannings', function ($query) {
            return $query->where('is_complete', '=', true);
        })->whereBetween('created_at', 
        [Carbon::now()->subMonth(3), Carbon::now()]
            )->get()->sortBy(function ($item) {
                return $item->created_at;
           })->groupBy(function ($date) {
            return Carbon::parse($date->created_at)->format('F');
        })->map->count();

        return response()->json([
            "status" => "200",
            "message" => 'Datos obtenidos con éxito',
            "data" =>   $orders1,
            "type" => 'success'
        ]);
    }
    public function graph2()
    {
        $orders1 = Exam::whereHas('plannings', function ($query) {
            return $query->where('is_complete', '=', true);
        })->get()->groupBy('name');
        $orders2 = Planning::with('exam')->where('is_complete',true)->get()->groupBy('exam.name')->map(function ($row) {
            return $row->count('exam.name');
         })->sortDesc()->take(3);  

        return response()->json([
            "status" => "200",
            "message" => 'Datos obtenidos con éxito',
            "data" =>   $orders2,
            "type" => 'success'
        ]);
    }
}
