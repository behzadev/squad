<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Code;
use App\Jobs\IsWinner;
use Illuminate\Http\Response;
use App\Http\Requests\StoreWinner;
use App\Http\Requests\QueryWinners;
use App\Http\Controllers\Controller;

class WinnerController extends Controller
{
    /**
     * This endpoint gets hit by sms service provider
     *
     * @param StoreWinner $request
     * @return Response
     */
    public function store(StoreWinner $request): Response
    {
        IsWinner::dispatch(
            $request->number,
            $request->message
        );

        return response([
            'status' => 'success',
        ], 200);
    }

    /**
     * Query winners to check if a cell number with a code is winner or not
     *
     * @param QueryWinners $request
     * @return Response
     */
    public function query(QueryWinners $request): Response
    {
        $code = Code::where('value', $request->code)->first();
        if (!$code) {
            return response([
                'is_winner' => false
            ], 200);
        }

        $winner = \App\Models\Winner::where('cell_number', $request->cell_number)
            ->where('code_id', $code->id)
            ->count();

        $isWinner = $winner > 0 ? true : false;

        return response([
            'is_winner' => $isWinner
        ], 200);
    }
}
