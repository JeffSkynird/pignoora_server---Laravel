<?php

namespace App\Http\Controllers\v1\Eventos;

use App\Http\Controllers\Controller;
use App\Models\People;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \Validator;

class PeopleController extends Controller
{
    /**
     * Función para crear nuevos people
     * @param Request $request 
     * @return json
    */
    public function create(Request $request)
    {
        try {
            $params = $request->all();

            $vacios = Validator::make($request->all(), [
                'names' => 'required',
                'last_names' => 'required'
            ]);
            if ($vacios->fails()) {
                return response([
                    'message' => "No debe dejar campos vacíos",
                    'fields' => $request->all(),
                    'type' => "error",
                ]);
            }
            $params['user_id']=Auth::id();
            People::create($params);
            return response()->json([
                "status" => "200",
                "message" => 'Registro exitoso',
                "type" => 'success'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "500",
                "message" => $e->getMessage(),
                "type" => 'error'
            ]);
        }
    }
    /**
     * Función para obtener los datos de un people
     * @param int $id 
     * @return json
     */
    public function show($id)
    {
        $data = People::find($id);
        return response()->json([
            "status" => "200",
            "message" => 'Datos obtenidos con éxito',
            "data" => $data,
            "type" => 'success'
        ]);
    }
    /**
     * Función para modificar los datos de un people
     * @param int $id, Request $request 
     * @return json
     */
    public function update(Request $request, $id)
    {
        $vacios = Validator::make($request->all(), [
            'names' => 'required',
            'last_names' => 'required'
        ]);
        if ($vacios->fails()) {
            return response([
                'message' => "No deje campos vacíos",
                'type' => "error",
            ]);
        }
       
        try {
            People::find($id)->update($request->all());
            return response()->json([
                "status" => "200",
                "message" => 'Modificación exitosa',
                "type" => 'success'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "500",
                "message" => $e->getMessage(),
                "type" => 'error'
            ]);
        }
    }
   
    /**
     * Función para eliminar un people
     * @param  int $id
     * @return json
     */
    public function delete($id)
    {
        $data = People::find($id);
        $data->delete();
      
        return response()->json([
            "status" => "200",
            "message" => 'Eliminación exitosa',
            "type" => 'success'
        ]);
    }
    /**
     * Función para obtener todos los people
     * @return json
     */
    public function index()
    {
        $data = People::all();
        return response()->json([
            "status" => "200",
            "data" => $data,
            "message" => 'Listado exitoso',
            "type" => 'success'
        ]);
    }
}
