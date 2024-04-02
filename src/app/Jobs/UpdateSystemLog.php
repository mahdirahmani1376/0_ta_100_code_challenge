<?php

namespace App\Jobs;

use App\Models\AbstractBaseLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Class UpdateSystemLog
 * @package App\Jobs
 * @method static dispatch(AbstractBaseLog $systemLog, array $response)
 */
class UpdateSystemLog implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var AbstractBaseLog|null
     */
    public ?AbstractBaseLog $baseLog;

    /**
     * @var array
     */
    public array $response;

    /**
     * Create a new event instance.
     * @param $baseLog
     * @param array $response
     */
    public function __construct($baseLog, array $response)
    {
        $this->baseLog = $baseLog;
        $this->response = $response;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->baseLog instanceof AbstractBaseLog) {
            $this->baseLog->response_header = $this->response['header'];
            $this->baseLog->response_body = $this->response['body'];
            $this->baseLog->response_status = $this->response['status'];

            try {
                $this->baseLog->update();
            } catch (\Exception $e) {
                Log::error('fail to update information in mongoDB');
                return null;
            }
        }
    }
}
