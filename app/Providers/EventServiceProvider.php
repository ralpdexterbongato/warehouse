<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\TaskEvent' => [
            'App\Listeners\TaskEventListener',
        ],
        'App\Events\NewApprovedMIRSEvent' => [
            'App\Listeners\NewApprovedMIRSEventListener',
        ],
        'App\Events\NewMCTEvent' => [
            'App\Listeners\NewMCTEventListener',
        ],
        'App\Events\NewMRTEvent' => [
            'App\Listeners\NewMRTEventListener',
        ],
        'App\Events\NewRVEvent' => [
            'App\Listeners\NewRVEventListener',
        ],
        'App\Events\NewRVApprovedEvent' => [
            'App\Listeners\NewRVApprovedEventListener',
        ],
        'App\Events\NewPOEvent' => [
            'App\Listeners\NewPOEventListener',
        ],
        'App\Events\NewRREvent' => [
            'App\Listeners\NewRREventListener',
        ],
        'App\Events\NewMREvent' => [
            'App\Listeners\NewMREventListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
