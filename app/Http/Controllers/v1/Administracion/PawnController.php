<?php

namespace App\Http\Controllers\v1\Administracion;

use App\Http\Controllers\Controller;
use App\Http\Services\ImageRemoteService;
use App\Models\Category;
use App\Models\Chat;
use App\Models\Pawn;
use App\Models\PawnImage;
use App\Models\Review;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use \Validator;

class PawnController extends Controller
{
    public function cancelPawn(Request $request){
        $params = $request->all();
        $pawn = Pawn::find($params['pawn_id']);
        if($pawn->status == 'cancelado'){
            return response()->json([
                "status" => "200",
                "message" => 'El empeño ya ha sido cancelado',
                "type" => 'error'
            ]);
        }
        $pawn->update(['status' => 'cancelado','observation'=>$params['observation']]);
        return response()->json([
            "status" => "200",
            "message" => 'Cancelación exitosa',
            "type" => 'success'
        ]);
    }
    public function aceptDriver($id){
        $driver = Auth::id();
        $pawn = Pawn::find($id);
        if($pawn->driver_id != null){
            return response()->json([
                "status" => "200",
                "message" => 'Ya tiene un conductor asignado',
                "type" => 'error'
            ]);
        }
        if($pawn->status !='aceptado'){
            return response()->json([
                "status" => "200",
                "message" => 'El empeño aún no ha sido aceptado',
                "type" => 'error'
            ]);
        }
        $pawn->update(['driver_id' => $driver, 'status' => 'por entregar']);
        return response()->json([
            "status" => "200",
            "message" => 'Aceptación exitosa',
            "type" => 'success'
        ]);
    }

    public function getUserByPawnId($id){
        $data = Pawn::where('id', $id)->first();
        return response()->json([
            "status" => "200",
            "data" => $data->user,
            "message" => 'Listado exitoso',
            "type" => 'success'
        ]);
    }
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
                'features' => 'required',
                'model' => 'required',
                'brand' => 'required',
                'pawn_type' => 'required'
            ]);
            if ($vacios->fails()) {
                return response([
                    'message' => "No debe dejar campos vacíos",
                    'fields' => $request->all(),
                    'type' => "error",
                ]);
            }
            $params['user_id'] = Auth::id();
            $data = Pawn::create($params);
            $str = $data->pawn_type . ' ' . $data->brand . ' ' . $data->model . ' ' . $data->features;

            Chat::create([
                'message' => 'Su empeño ha sido solicitado: ' . $str,
                'pawn_id' => $data->id,
                'user_id' => Auth::id(),
                'admin_id' => 2,
                'is_admin' => 1
            ]);
            Chat::create([
                'message' => 'Elija el plazo en meses a pagar:',
                'pawn_id' => $data->id,
                'user_id' => Auth::id(),
                'admin_id' => 2,
                'is_admin' => 1,
                'type' => 'request_term',
                'created_at' => now()->addSeconds(1)
            ]);

            DB::commit();
            return response()->json([
                "status" => "200",
                "message" => 'Registro exitoso',
                "data" =>    $data->id,
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
        $data = Pawn::where('id', $id)->orderBy('id', 'desc')->first();
        $images = PawnImage::where('pawn_id', $id)->select('id', 'url')->get();
        $tieneReviews = Pawn::where('id', $id)->whereHas('reviews')->orderBy('id', 'desc')->count();
        $review = Review::where('pawn_id', $id)->with('user')->first();
        return response()->json([
            "status" => "200",
            "message" => 'Datos obtenidos con éxito',
            "data" => $data,
            "images" => $images,
            "has_review"=> $tieneReviews > 0 ? true : false,
            'review'=> $review, 
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
            'features' => 'required',
            'model' => 'required',
            'brand' => 'required',
            'pawn_type' => 'required'
        ]);
        if ($vacios->fails()) {
            return response([
                'message' => "No deje campos vacíos",
                'type' => "error",
            ]);
        }
        DB::beginTransaction();
        try {
            $category = Pawn::find($id);
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
            $this->deleteImages($id);

            $data = Pawn::find($id);
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
        $id = Auth::id();
        $data = Pawn::where('user_id', $id)->orderBy('id', 'desc')->get();
        return response()->json([
            "status" => "200",
            "data" => $data,
            "message" => 'Listado exitoso',
            "type" => 'success'
        ]);
    }
    public function showSuperAdmin($id)
    {
        $data = Pawn::where('user_id', $id)->with('user', 'asesor')->orderBy('id', 'desc')->get();
        return response()->json([
            "status" => "200",
            "data" => $data,
            "message" => 'Listado exitoso',
            "type" => 'success'
        ]);
    }
    public function indexSuperAdmin()
    {
        $id = Auth::id();
        $data = Pawn::with('user', 'asesor')->orderBy('id', 'desc')->get();
        return response()->json([
            "status" => "200",
            "data" => $data,
            "message" => 'Listado exitoso',
            "type" => 'success'
        ]);
    }

    public function indexAdmin(Request $request)
    {
        $id = Auth::id();
        $data = array();
        if ($request->filtro == 'atendidos') {
            $data = Pawn::where('asesor_id', $id)->whereHas('user', function ($q) {
                $q->where('tipo_user', 'interested');
            })->orderBy('id', 'desc')->get();
        } else if ($request->filtro == 'nuevos') {
            $data = Pawn::where('asesor_id', null)->orderBy('id', 'desc')->get();
        } else if ($request->filtro == 'revisados') {
            $data = Pawn::whereHas('reviews', function ($q) use ($id) {
                $q->where('driver_id', $id);
            })->orderBy('id', 'desc')->get();
        } else if ($request->filtro == 'aceptados') {
            $data = Pawn::where('is_acepted', 1)->whereDoesntHave('reviews')->orderBy('id', 'desc')->get();
        }else if($request->filtro == 'clientes'){
            $data = Pawn::where('asesor_id', $id)->whereHas('user', function ($q) {
                $q->where('tipo_user', 'client');
            })->orderBy('id', 'desc')->get();
        }
        return response()->json([
            "status" => "200",
            "data" => $data,
            "message" => 'Listado exitoso',
            "type" => 'success'
        ]);
    }


    public function createImages(Request $request)
    {
        try {
            DB::beginTransaction();
            $remote = new ImageRemoteService();
            $images = $request->images;
            $pawnId = $request->pawn_id;
            foreach ($images as $image) {
                if ($remote->validateFile($image) == true) {
                    $obj = $remote->saveImgur($image);
                    $url = $obj['data']['link'];
                    $hash = $obj['data']['deletehash'];
                    PawnImage::create([
                        'pawn_id' => $pawnId,
                        'url' => $url,
                        'delete_hash' => $hash
                    ]);
                }
            } 
            DB::commit();
            return response()->json([
                "status" => "200",
                "message" => 'Imagenes guardadas con éxito',
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

    public function deleteImages($pawnId)
    {
        try {
            DB::beginTransaction();

            $images = PawnImage::where('pawn_id', $pawnId)->get();
            if (count($images) == 0) {
                return response()->json([
                    "status" => "200",
                    "message" => 'No hay imagenes para eliminar',
                    "type" => 'success'
                ]);
            }
            $remote = new ImageRemoteService();
            foreach ($images as $image) {
                $remote->deleteRemoteImage($image->delete_hash);
                $image->delete();
            }
            DB::commit();
            return response()->json([
                "status" => "200",
                "message" => 'Imagenes eliminadas con éxito',
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


    public function getLastPawns()
    {
        $id = Auth::id();
        $data = Pawn::where('user_id', $id)->orderBy('id', 'desc')->take(5)->get();
        return response()->json([
            "status" => "200",
            "data" => $data,
            "message" => 'Listado exitoso',
            "type" => 'success'
        ]);
    }

    public function getLastAdminPawns()
    {
        $id = Auth::id();
        $data = Pawn::where('asesor_id', $id)->orderBy('id', 'desc')->take(5)->get();
        return response()->json([
            "status" => "200",
            "data" => $data,
            "message" => 'Listado exitoso',
            "type" => 'success'
        ]);
    }
    public function getLastDriverPawns()
    {
        $id = Auth::id();
        $data = Pawn::whereHas('reviews', function ($q) use ($id) {
            $q->where('driver_id', $id);
        })->orderBy('id', 'desc')->take(5)->get();

        return response()->json([
            "status" => "200",
            "data" => $data,
            "message" => 'Listado exitoso',
            "type" => 'success'
        ]);
    }
}
