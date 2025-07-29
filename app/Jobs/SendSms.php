<?php

namespace App\Jobs;

use App\Models\SmsLog;
use App\Models\Vendor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $smsLog;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(SmsLog $smsLog)
    {
        $this->smsLog = $smsLog;

        $this->onQueue('sms');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $vendor = Vendor::find($this->smsLog->vendor_id);
        if ($this->smsLog->status_text === 'Pending-Forgot-Password') {
            $response = Http::post('https://msg.elitbuzz-bd.com/smsapi', [
                'api_key' => 'C20079736354ebdde6c322.72432502',
                'type' => 'text',
                'contacts' => '88' . $this->smsLog->to,
                'msg' => $this->smsLog->message,
                'label' => 'transactional',
                'senderid' => '8809612446650',
            ]);

            if ($response->successful()) {
                $this->smsLog->status = 1;
                $this->smsLog->status_text = "DELIVERED";

            } else {
                $this->smsLog->status = 2;
                $this->smsLog->status_text = "Server Error";
            }
        }
        else {
            if ($vendor->sms_balance < 1) {
                $this->smsLog->status = 2;
                $this->smsLog->status_text = "INSUFFICIENT_BALANCE";
            } else {
                $response = Http::post('https://msg.elitbuzz-bd.com/smsapi', [
                    'api_key' => 'C20079736354ebdde6c322.72432502',
                    'type' => 'text',
                    'contacts' => '88' . $this->smsLog->to,
                    'msg' => $this->smsLog->message,
                    'label' => 'transactional',
                    'senderid' => '8809612446650',
                ]);

                if ($response->successful()) {
                    $this->smsLog->status = 1;
                    $this->smsLog->status_text = "DELIVERED";

                    $vendor->decrement('sms_balance');
                } else {
                    $this->smsLog->status = 2;
                    $this->smsLog->status_text = "Server Error";
                }
            }
        }

        $this->smsLog->save();
    }
}
