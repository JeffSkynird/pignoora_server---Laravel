<?php

namespace App\Http\Controllers\v1\Seguridad;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use \Validator;
use Illuminate\Support\Facades\Auth;

use function PHPSTORM_META\map;

class UsuarioController extends Controller
{
    public function index(){
        $usuarios = User::orderBy('id', 'desc')->get();
        return json_encode([
            "status" => "200",
            "data"=> $usuarios,
            "message" => 'Listado exitoso',
            "type" => 'success'
        ]);
    }
    public function indexBy(Request $request){

        $usuarios = array();
        if($request->type=="asesores"){
            $usuarios = User::where('tipo_user','asesor')->orderBy('id', 'desc')->get();
        }else if($request->type=="clientes"){
            $usuarios = User::where('tipo_user','cliente')->orderBy('id', 'desc')->get();
        }else if($request->type=="interesados"){
            $usuarios = User::where('tipo_user','interested')->orderBy('id', 'desc')->get();
        }else if($request->type=="administradores"){
            $usuarios = User::where('tipo_user','admin')->orderBy('id', 'desc')->get();
        }
        return json_encode([
            "status" => "200",
            "data"=> $usuarios,
            "message" => 'Listado exitoso',
            "type" => 'success'
        ]);
    }
    public function create(Request $request)
    {
        try {
            $params = $request->all();
            $vacios = Validator::make($request->all(), [
                'address' => 'required',
                'names' => 'required',
                'last_names' => 'required',
                'email' => 'required',
                'password' => 'required'
            ]);
            if ($vacios->fails()) {
                return response([
                    'message' => "No debe dejar campos vacíos",
                    'fields' => $request->all(),
                    'type' => "error",
                ]);
            }
            User::create($params);
            return json_encode([
                "status" => "200",
                "message" => 'Registro exitoso',
                "type" => 'success'
            ]);
        } catch (\Exception $e) {
            return json_encode([
                "status" => "500",
                "message" => $e->getMessage(),
                "type" => 'error'
            ]);
        }
    }
    public function show($id)
    {
        $data = User::find($id);
        return json_encode([
            "status" => "200",
            "message" => 'Datos obtenidos con éxito',
            "data" => $data,
            "type" => 'success'
        ]);
    }
    public function showAuth()
    {
        return json_encode([
            "status" => "200",
            "message" => 'Datos obtenidos con éxito',
            "data" => Auth::user(),
            "type" => 'success'
        ]);
    }
    public function update(Request $request,$id){
        $names = $request->input('names');
        $lastNames = $request->input('last_names');
        $email = $request->input('email');
        $password = $request->input('password');
        $address = $request->input('address');
        $phone = $request->input('phone');

        try {
            $vacios = Validator::make($request->all(), [
                'address'=>'required',
                'names' => 'required',
                'last_names' => 'required',
                'email' => 'required',
                'phone' => 'required'
            ]);
            if ($vacios->fails()) {
                return response([
                    'message' => "No debe dejar campos vacíos",
                    'fields' => $request->all(),
                    'type' => "error",
                ]);
            }

            $user = User::find($id);
            $user->names=$names;
            $user->last_names=$lastNames;
            $user->email=$email;
            $user->address = $address;
            $user->phone = $phone;

            if(!is_null($password)){
                    $user->password=$password;
            }
            $user->save();
            return json_encode([
                "status" => "200",
                "message" => 'Modificación exitosa',
                "type" => 'success'
            ]);
        } catch (\Exception $e) {
            return json_encode([
                "status" => "500",
                "message" => $e->getMessage(),
                "type" => 'error'
            ]);
        }
    }
    public function updateAuth(Request $request){
        $userAuth = Auth::user();
        $names = $request->input('names');
        $lastNames = $request->input('last_names');
        $email = $request->input('email');
        $password = $request->input('password');
        $address = $request->input('address');
        $phone = $request->input('phone');

        $vacios = Validator::make($request->all(), [
            'address'=>'required',
            'names' => 'required',
            'last_names' => 'required',
            'email' => 'required',
            'phone' => 'required'
        ]);
        if ($vacios->fails()) {
            return response([
                'message' => "Revise los campos ingresados",
                'type' => "error",
            ]);
        }
        try {
            $user = User::find($userAuth->id);
            $user->names=$names;
            $user->last_names=$lastNames;
            $user->email=$email;
            $user->address = $address;
            $user->phone = $phone;
            if(!is_null($password)){
                $user->password=$password;
            }
            $user->save();

            return json_encode([
                "status" => "200",
                "message" => 'Modificación exitosa',
                "type" => 'success'
            ]);
        } catch (\Exception $e) {
            return json_encode([
                "status" => "500",
                "message" => $e->getMessage(),
                "type" => 'error'
            ]);
        }
    }
    public function delete($id)
    {
        $data = User::find($id);
        $data->delete();
        return json_encode([
            "status" => "200",
            "message" => 'Eliminación exitosa',
            "type" => 'success'
        ]);
    }
}
