<?php

namespace App\Listeners;

use App\Events\GlobalNotif;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class GlobalNotif
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  GlobalNotif  $event
     * @return void
     */
    public function handle(GlobalNotif $event)
    {
        //
    }
}
