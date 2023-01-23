<?php

namespace App\Http\Controllers\v1\Administracion;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Credit;
use App\Models\Pawn;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use \Validator;

class CreditController extends Controller
{
    /**
     * Función para crear nuevos categories
     * @param Request $request 
     * @return json
     */
    public function create(Request $request)
    {
        DB::beginTransaction();
        try {
            $params = $request->all();
            $vacios = Validator::make($request->all(), [
                'pawn_id' => 'required',
                'amount' => 'required',
                'term' => 'required',
                'interest' => 'required',
                'pay_type' => 'required',
                'data' => 'required'
            ]);
            if ($vacios->fails()) {
                return response([
                    'message' => "No debe dejar campos vacíos",
                    'fields' => $request->all(),
                    'type' => "error",
                ]);
            }
            $params['status'] = 'activo';
            $params['start_date'] = date('Y-m-d');
            $params['asesor_id'] = Auth::id();


            $credit = Credit::where('pawn_id', $params['pawn_id'])->first();
            if ($credit != null) {
                $credit->update($params);
            } else {
                return response([
                    'message' => "No se ha registrado el plazo y forma de pago",
                    'type' => "error",
                    "status" => "500",
                ]);
            }
            if($credit->payments!=null){
                Payment::where('credit_id', $credit->id)->delete();
            }
            foreach ($params['data'] as $key => $value) {
                $value['payment'] = number_format($value['payment'], 2, '.', '');
                $value['interest'] = number_format($value['interest'], 2, '.', '');
                $value['balance'] = number_format($value['balance'], 2, '.', '');

                Payment::create([
                    'credit_id' => $credit->id,
                    'amount' => $value['payment'],
                    'pay_date' => $this->obtenerFechaPorPeriodo($value['period']),
                    'interest' => $value['interest'],
                    'balance' => $value['balance']
                ]);
            }
            $firstDate=$this->obtenerFechaPorPeriodo(1);
            $pawn = Pawn::find($params['pawn_id']);
            $pawn->update(['asesor_id' => $params['asesor_id'],'value'=>$params['amount']]);
            $str = '¿Acepta su credito por un monto de $' . $params['amount'] . ' con un plazo de ' . $params['term'] . ' meses'.', su primer pago es el '.$firstDate."?";

            Chat::create([
                'message' => $str,
                'pawn_id' => $pawn->id,
                'user_id' => $pawn->user_id,
                'admin_id' => Auth::id(),
                'is_admin' => 1,
                'type' => 'message_credit'
            ]);


            DB::commit();
            return response()->json([
                "status" => "200",
                "message" => 'Registro exitoso',
                "type" => 'success'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "status" => "500",
                "message" => $e->getMessage(),
                "type" => 'error'
            ]);
        }
    }
    //pasa esta funcion de javascript a php


    public function obtenerFechaPorPeriodo($period)
    {
        $fecha = new Carbon();
        $fecha->addMonths($period);
        return $fecha->toDateString();
    }

    /**
     * Función para obtener los datos de un categories
     * @param int $id 
     * @return json
     */
    public function show($pawnId)
    {
        $data = Credit::where('pawn_id', $pawnId)->with('payments')->get();
        if (count($data) == 0) {
            return response()->json([
                "status" => "404",
                "message" => 'No se encontraron datos',
                "type" => 'error'
            ]);
        }
        return response()->json([
            "status" => "200",
            "message" => 'Datos obtenidos con éxito',
            "data" => $data,
            "type" => 'success'
        ]);
    }
    /**
     * Función para modificar los datos de un categories
     * @param int $id, Request $request 
     * @return json
     */
    public function update(Request $request, $id)
    {
        $vacios = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'image' => 'required'
        ]);
        if ($vacios->fails()) {
            return response([
                'message' => "No deje campos vacíos",
                'type' => "error",
            ]);
        }
        DB::beginTransaction();
        try {
            $category = Credit::find($id);
            $category->update($request->all());
            DB::commit();
            return response()->json([
                "status" => "200",
                "message" => 'Modificación exitosa',
                "type" => 'success'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "status" => "500",
                "message" => $e->getMessage(),
                "type" => 'error'
            ]);
        }
    }

    /**
     * Función para eliminar un categories
     * @param  int $id
     * @return json
     */
    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $data = Credit::find($id);
            $data->delete();
            DB::commit();
            return response()->json([
                "status" => "200",
                "message" => 'Eliminación exitosa',
                "type" => 'success'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "status" => "500",
                "message" => $e->getMessage(),
                "type" => 'error'
            ]);
        }
    }
    /**
     * Función para obtener todos los categories
     * @return json
     */
    public function index()
    {
        $data = Credit::orderBy('id', 'desc')->get();
        return response()->json([
            "status" => "200",
            "data" => $data,
            "message" => 'Listado exitoso',
            "type" => 'success'
        ]);
    }
}
