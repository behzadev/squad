<?php

namespace App\Jobs;

use App\Models\Code;
use App\Models\Winner;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class IsWinner implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $number;

    protected $code;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $number, string $code)
    {
        $this->number = $number;

        $this->code = $code;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $code = Code::where('value', $this->code)->first();

        if (!$code) {
            return;
        }

        if ($code->isUsable()) {
            Winner::create([
                'cell_number' => $this->number,
                'code_id' => $code->id,
            ]);

            $code->count_left--;
            $code->save();
        }
    }
}
