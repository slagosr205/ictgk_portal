<?php

namespace App\Listeners;
use App\Events\RegistroActualizado;
use App\Models\EventLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogRegistroActualizadoToDatabase
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RegistroActualizado $event): void
    {
        //
        \Log::info('Enviando a guardar la informacion '.$event->registro);
        EventLog::create([
            'user_id'=> auth()->id(),
            'event_type'=>'Registro actualizado',
            'event_data'=>json_encode(['registro_id'=>$event->registro])
        ]);
    }
}
