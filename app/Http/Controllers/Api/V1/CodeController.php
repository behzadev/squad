<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Code;
use App\Http\Requests\StoreCode;
use App\Http\Controllers\Controller;
use App\Http\Resources\CodeResource;
use Illuminate\Http\Response;

class CodeController extends Controller
{
    /**
     * Store a new code
     *
     * @param StoreCode $request
     * @return Response
     */
    public function store(StoreCode $request): Response
    {
        $code = Code::create($request->validated() + [
            'count_left' => $request->count_init
        ]);

        return response([
            'status' => 'success',
            'code' =>  new CodeResource($code)
        ], 201);
    }
}
