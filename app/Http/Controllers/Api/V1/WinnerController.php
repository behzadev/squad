<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWinner;
use App\Jobs\IsWinner;

class WinnerController extends Controller
{
    public function store(StoreWinner $request)
    {
        IsWinner::dispatch(
            $request->number,
            $request->message
        );

        return response([
            'status' => 'success',
        ], 200);
    }
}
