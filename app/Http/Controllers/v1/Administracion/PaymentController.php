<?php

namespace App\Http\Controllers\v1\Administracion;

use App\Http\Controllers\Controller;
use App\Models\Credit;
use App\Models\Pawn;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \Validator;
class PaymentController extends Controller
{
    public function showPay(Request $request){
        $params = $request->all();
        $credit = Credit::where('pawn_id', $params['pawn_id'])->first();
        $payments = Payment::where('credit_id', $credit->id)->orderBy('pay_date', 'asc')->get();
        return response()->json([
            "status" => "200",
            "message" => 'Registro exitoso',
            "type" => 'success',
            "data" => $payments
        ]);
    }

    public function pay(Request $request){
        DB::beginTransaction();
        try {
            $params = $request->all();
         
            $vacios = Validator::make($request->all(), [
                'pawn_id' => 'required',
                'data' => 'required'
            ]);
            if ($vacios->fails()) {
                return response([
                    'message' => "No debe dejar campos vacíos",
                    'fields' => $request->all(),
                    'type' => "error",
                ]);
            }

            foreach ($params['data'] as $key => $value) {
                $payment = Payment::where('id', $value['id'])->first();
                $payment->update([
                    'status' => $value['status']
                ]);
            }
            $credit = Credit::where('pawn_id', $params['pawn_id'])->first();
            $payments = Payment::where('credit_id', $credit->id)->where('status', 'pendiente')->where('amount', '!=',0)->get();
            if(count($payments)==0){
                $pawn = Pawn::where('id', $params['pawn_id'])->first();
                $pawn->update([
                    'status' => 'finalizado'
                ]);
            }
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
    //
   
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
                'credit_id' => 'required',
                'data' => 'required'
            ]);
            if ($vacios->fails()) {
                return response([
                    'message' => "No debe dejar campos vacíos",
                    'fields' => $request->all(),
                    'type' => "error",
                ]);
            }
            foreach ($params['data'] as $key => $value) {
                Payment::create([
                    'credit_id' =>$params['credit_id'],
                    'amount' => $value['amount'],
                    'pay_date' => $value['pay_date'],
                    'interest' => $value['interest'],
                    'balance' => $value['balance']
                ]);
            }
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
    /**
     * Función para obtener los datos de un categories
     * @param int $id 
     * @return json
     */
    public function show($id)
    {
        $data = Payment::find($id);
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
            'image'=>'required'
        ]);
        if ($vacios->fails()) {
            return response([
                'message' => "No deje campos vacíos",
                'type' => "error",
            ]);
        }
        DB::beginTransaction();
        try {
            $category = Payment::find($id);
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
            $data = Payment::find($id);
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
        $data = Payment::orderBy('id', 'desc')->get();
        return response()->json([
            "status" => "200",
            "data" => $data,
            "message" => 'Listado exitoso',
            "type" => 'success'
        ]);
    }
}
