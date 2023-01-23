<?php

namespace App\Http\Controllers\v1\Administracion;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Credit;
use App\Models\Pawn;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use \Validator;

class ChatController extends Controller
{
    /**
     * Función para crear nuevos categories
     * @param Request $request 
     * @return json
     */
    public function sendUser(Request $request)
    {
        DB::beginTransaction();
        try {
            $params = $request->all();
            $vacios = Validator::make($request->all(), [
                'message' => 'required',
                'pawn_id' => 'required'
            ]);
            if ($vacios->fails()) {
                return response([
                    'message' => "No debe dejar campos vacíos",
                    'fields' => $request->all(),
                    'type' => "error",
                ]);
            }
            $params['user_id'] = Auth::id();

            if ($params['type'] == "response_value") {
                $res = Pawn::find($params['pawn_id']);
                $bol = $params['message'] == 'Avaluo aceptado' ? true : false;
                $val = 0;
                if($bol){
                    $msg = Chat::where('pawn_id', $params['pawn_id'])->where('type', 'request_value')->orderBy('id', 'desc')->first();
                    if($msg!=null){
                        $val = $msg->value;
                    }
                }
                if($res->status == 'entregado'){
                    $res->update(['is_acepted' => $bol, 'value' => $val, 'status' => $bol?'por cobrar':'rechazado']);
                    $cl = User::find($res->user_id);
                    $cl->update(['tipo_user' => 'client']);
                }else{
                    if($res->status != 'por cobrar'){
                        $res->update(['is_acepted' => $bol, 'value' => $val, 'status' => $bol?'aceptado':'rechazado']);
                    }
                }
            }else if($params['type'] == "map"){
                $res = Pawn::find($params['pawn_id']);
                $res->update(['location' => $params['message']]);
            }else if($params['type'] == "response_term"){
                $res = Pawn::find($params['pawn_id']);
                $credit = Credit::create([
                    'pawn_id' => $params['pawn_id'],
                    'amount' => 0,
                    'term' => $params['message'],
                    'interest' => 0,
                    'pay_type' => 'mensual',
                    'status' => 'activo',
                    'start_date' => date('Y-m-d'),
                    'asesor_id' => 2
                ]);
                Chat::create([
                    'message' => 'Elija la forma de pago:',
                    'pawn_id' => $params['pawn_id'],
                    'user_id' => Auth::id(),
                    'admin_id' => 2,
                    'is_admin' => 1,
                    'type' => 'request_pay_type'
                ]);
            }else if($params['type'] == "response_pay_type"){
                $res = Pawn::find($params['pawn_id']);
                $credit = Credit::where('pawn_id', $params['pawn_id'])->first();
                $credit->update(['pay_type' => $params['message']]);

                Chat::create([
                    'message' => 'Plazo '.$credit->pay_type. ' a '.$credit->term.' meses seleccionado',
                    'pawn_id' => $params['pawn_id'],
                    'user_id' => Auth::id(),
                    'admin_id' => 2,
                    'is_admin' => 1,
                    'type' => 'response_value'
                ]);

            }
            $cht = Chat::create($params);
            DB::commit();
            return response()->json([
                "status" => "200",
                "message" => 'Registro exitoso',
                "data" => $cht,
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

    public function sendAdmin(Request $request)
    {
        DB::beginTransaction();
        try {
            $params = $request->all();
            $vacios = Validator::make($request->all(), [
                'message' => 'required',
                'pawn_id' => 'required'
            ]);
            if ($vacios->fails()) {
                return response([
                    'message' => "No debe dejar campos vacíos",
                    'fields' => $request->all(),
                    'type' => "error",
                ]);
            }
            $params['admin_id'] = Auth::id();
            $params['is_admin'] = true;
            $pawn = Pawn::find($params['pawn_id']);
            $params['user_id'] = $pawn->user_id;
            Chat::create($params);
            if ($pawn->asesor_id == null) {
                $pawn->update(['asesor_id' => $params['admin_id'],'status' => 'en proceso']);
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

    public function getChat(Request $request,$id)
    {
        $order = 'desc';
        if($request->has('order')){
            $order = $request->order;
        }
        $data = Chat::where('pawn_id', $id)->orderBy('created_at', $order)->get();
        $res = Pawn::find($id);
        return response()->json([
            "status" => "200",
            "message" => 'Datos obtenidos con éxito',
            "data" => $data,
            "pawn_status" => $res->status,
            "type" => 'success'
        ]);
    }
    /**
     * Función para obtener los datos de un categories
     * @param int $id 
     * @return json
     */
    public function show($id)
    {
        $data = Chat::find($id);
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
            $category = Chat::find($id);
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
            $data = Chat::find($id);
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
        $data = Chat::orderBy('created_at', 'desc')->get();
        return response()->json([
            "status" => "200",
            "data" => $data,
            "message" => 'Listado exitoso',
            "type" => 'success'
        ]);
    }
}
