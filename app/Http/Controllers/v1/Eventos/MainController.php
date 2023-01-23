<?php

namespace App\Http\Controllers\v1\Eventos;

use App\Http\Controllers\Controller;
use App\Models\Box;
use App\Models\Category;
use App\Models\Participant;
use App\Models\People;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use \Validator;

class MainController extends Controller
{

    /**
     * Función para crear nuevos participant
     * @param Request $request 
     * @return json
     */
    public function create(Request $request)
    {
        $type = $request->input('type');
        $data = $request->input('data');
        $arNoValid = [];
        DB::beginTransaction();
        try {
            if (count($data) > 0) {
                foreach ($data as $val) {
                    $box = Box::where('name', $val['BOX'])->first();
                    if ($box == null) {
                        array_push($arNoValid, [
                            'BOX' => $val['BOX'],
                            'OBJ' => $val
                        ]);
                    } else {
                        $people = People::create([
                            'names' => $val['NOMBRES'],
                            'last_names' => $val['APELLIDOS'],
                            'user_id' => Auth::id()
                        ]);
                        $participant = Participant::create([
                            'people_id' => $people->id,
                            'total_score' => $val['TOTAL'],
                            'user_id' => Auth::id(),
                            'category_id' => intval($type),
                            'box_id' => $box->id,
                            'event_id' => 1
                        ]);
                        $participant->points()->create([
                            'score' => $val['EVENTO_1_POINTS'],
                            'position' => $val['EVENTO_1_RANK'],
                            'value' => $val['EVENTO_1_REP'],
                            'step_id' => 1
                        ]);
                        $participant->points()->create([
                            'score' => $val['EVENTO_2_POINTS'],
                            'position' => $val['EVENTO_2_RANK'],
                            'value' => $val['EVENTO_2_REP'],
                            'step_id' => 2
                        ]);
                    }
                }
            }
            DB::commit();
            return response()->json([
                "status" => "200",
                'data' => $arNoValid,
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
}
