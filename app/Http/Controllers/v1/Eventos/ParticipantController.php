<?php

namespace App\Http\Controllers\v1\Eventos;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Participant;
use App\Models\ParticipantBox;
use App\Models\ParticipantCategory;
use App\Models\ParticipantEvent;
use App\Models\People;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use \Validator;

class ParticipantController extends Controller
{
    /**
     * Función para crear nuevos participant
     * @param Request $request 
     * @return json
     */
    public function create(Request $request)
    {
        DB::beginTransaction();
        try {
            $params = $request->all();
            $vacios = Validator::make($request->all(), [
                'category_id' => 'required',
                'names' => 'required',
                'last_names' => 'required',
                'box_id' => 'required',
                'event_id' => 'required'
            ]);
            if ($vacios->fails()) {
                return response([
                    'message' => "No debe dejar campos vacíos",
                    'fields' => $request->all(),
                    'type' => "error",
                ]);
            }
            //CREANDO PERSONA
            //Si existe con name y last_names
            $person = People::where('names', $params['names'])
                ->where('last_names', $params['last_names'])
                ->first();
            if (is_null($person)) {
                $person = People::create([
                    'names' => $params['names'],
                    'last_names' => $params['last_names'],
                    'user_id' => Auth::id()
                ]);
            }
            $params['people_id'] = $person->id;
            $params['user_id'] = Auth::id();
            Participant::create($params);

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
     * Función para obtener los datos de un participant
     * @param int $id 
     * @return json
     */
    public function show($id)
    {
        $data = Participant::find($id);
        return response()->json([
            "status" => "200",
            "message" => 'Datos obtenidos con éxito',
            "data" => $data,
            "type" => 'success'
        ]);
    }
    /**
     * Función para modificar los datos de un participant
     * @param int $id, Request $request 
     * @return json
     */
    public function update(Request $request, $id)
    {
        $vacios = Validator::make($request->all(), [
            'names' => 'required',
            'last_names' => 'required',
            'category_id' => 'required',
            'box_id' => 'required',
            'event_id' => 'required'

        ]);
        if ($vacios->fails()) {
            return response([
                'message' => "No deje campos vacíos",
                'type' => "error",
            ]);
        }

        try {
            $participant = Participant::find($id);
            $participant->people()->update([
                'names' => $request->input('names'),
                'last_names' => $request->input('last_names')
            ]);
            $participant->update([
                'category_id' => $request->input('category_id'),
                'box_id' => $request->input('box_id'),
                'event_id' => $request->input('event_id')
            ]);
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
     * Función para eliminar un participant
     * @param  int $id
     * @return json
     */
    public function delete($id)
    {
        $data = Participant::find($id);
        $data->delete();

        return response()->json([
            "status" => "200",
            "message" => 'Eliminación exitosa',
            "type" => 'success'
        ]);
    }
    /**
     * Función para obtener todos los participant
     * @return json
     */
    public function index(Request $request)
    {
        $category = $request->input('category_id');
        $isFinal = $request->input('is_final');
        if ($category == null) {
            $data = Participant::with('people', 'categories', 'boxes', 'events', 'points')->whereHas('events', function ($query)  use($isFinal) {
                $query->where('is_open', true);
                
                if($isFinal=='true'){
                    $query->where('is_final', true);
                }
            })->orderBy('total_score', 'desc')->get();
        } else {
            $data = Participant::with('people', 'categories', 'boxes', 'events', 'points')->whereHas('categories', function ($query) use ($category) {
                $query->where('category_id', $category);
            })->whereHas('events', function ($query)  use($isFinal){
                $query->where('is_open', true);
                if($isFinal=='true'){
                    $query->where('is_final', true);
                }
            })->orderBy('total_score', 'desc')->get();
        }

        return response()->json([
            "status" => "200",
            "data" => $data,
            "message" => 'Listado exitoso',
            "type" => 'success'
        ]);
    }
    /**
     * Función para obtener todos los participant
     * @return json
     */
    public function indexAdmin(Request $request)
    {
        $eventId = $request->input('event_id');
        if (is_null($eventId)) {
            //$data = Participant::with('people', 'categories', 'boxes', 'events')->orderBy('id', 'desc')->get();
            $data = Participant::with('people', 'categories', 'boxes', 'events')->whereHas('events', function ($query) {
                $query->where('is_open', true);
            })->orderBy('id', 'desc')->get();
        } else {
            $data = Participant::with('people', 'categories', 'boxes', 'events')->whereHas('events', function ($query) use ($eventId) {
                $query->where('id', $eventId);
            })->orderBy('id', 'desc')->get();
        }


        return response()->json([
            "status" => "200",
            "data" => $data,
            "message" => 'Listado exitoso',
            "type" => 'success'
        ]);
    }

    /**
     * Función para obtener todos los participant por evento
     * @return json
     */
    public function showByEvent(Request $request,$id)
    {
        $category = $request->input('category_id');
        $data = Participant::with('people', 'categories', 'boxes', 'events')->with(['points' => function ($q) use ($id) {
            $q->where('step_id', $id);
        }])->whereHas('events', function ($query) {
            $query->where('is_open', true);
        })->whereHas('categories', function ($query) use($category) {
            $query->where('id', $category);
        })->join('people', 'participants.people_id', 'people.id')->selectRaw("participants.*,people.names || ' ' || people.last_names as fullname")->get();
        return response()->json([
            "status" => "200",
            "data" => $data,
            "message" => 'Listado exitoso',
            "type" => 'success'
        ]);
    }

    /**
     * Función para registrar los puntos de un participante
     * @return json
     */
    public function createPoints(Request $request)
    {
        $data = $request->input('data');
        DB::beginTransaction();
        try {
            foreach ($data as $value) {
                $participant = Participant::find($value['id']);
                if ($value['point_id'] == null) {
                    $participant->points()->create([
                        'value' => $value['value'],
                        'score' => $value['score'],
                        'position' => $value['position'],
                        'step_id' => $value['step_id']
                    ]);
                } else {
                    $participant->points()->updateOrCreate([
                        'id' => $value['point_id']
                    ], [
                        'value' => $value['value'],
                        'score' => $value['score'],
                        'position' => $value['position'],
                        'step_id' => $value['step_id']
                    ]);
                }
                //Obtener la suma de score de los puntos de todos los steps
                $totalScore = $participant->points()->sum('score');
                $participant->update([
                    'total_score' => $totalScore
                ]);
            }
            DB::commit();
            return response()->json([
                "status" => "200",
                "message" => 'Guardado exitoso',
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
}
