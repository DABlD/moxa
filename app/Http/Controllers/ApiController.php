<?php

namespace App\Http\Controllers;

use Illuminate\Http\{Request, JsonResponse};
use App\Models\Reading;
use Exception;

class ApiController extends Controller
{
    public function index(Request $req)
    {
        try{
            $reading = new Reading();
            $reading->moxa_id = $req->id;
            $reading->datetime = $req->datetime;
            $reading->total = $req->payload;
            $reading->save();
        } catch (Exception $e) {
            return response()->json([
                'data' => [],
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        
        return response()->json([
            'data' => $test,
            'message' => 'Success'
        ], JsonResponse::HTTP_OK);
    }
}
