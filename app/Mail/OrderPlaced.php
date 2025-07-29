<?php

namespace App\Mail;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Setting;
use App\Models\SocialLink;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class OrderPlaced extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order, $settings, $socialLinks;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->settings = Setting::where('vendor_id', $this->order->vendor_id)->with('vendor')->first();

        $this->subject = "Your ".$this->settings->vendor->name." Order ".$order->order_no;
        $this->from(config('mail.from.address'), $this->settings->vendor->name);

        $this->onQueue('mail');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->order = $this->order->load('city', 'area', 'items.product');

        $this->settings->logo = $this->settings->logo ? Storage::url($this->settings->logo) : null;
        $this->socialLinks = SocialLink::where('vendor_id', $this->order->vendor_id)->get();

        foreach ($this->order->items as $item) {
            $item->product->image = $item->product && $item->product->image ? Storage::url($item->product->image) : asset('images/no_image.webp');
        }

        return $this->view('emails.orders.placed');
    }
}
