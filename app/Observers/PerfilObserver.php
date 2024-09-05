<?php

namespace App\Observers;

use App\Events\RegistroActualizado;
use App\Models\PerfilModel;
use App\Models\EventLog;
class PerfilObserver
{
    /**
     * Handle the PerfilModel "created" event.
     */
    public function created(PerfilModel $perfilModel): void
    {
        //
        EventLog::create([
            'user_id'=> auth()->id(),
            'event_type'=>'Registro creado',
            'event_data'=>json_encode([
                'tabla'=>$perfilModel->getTable(),
                'registro_id'=>$perfilModel->id,
                
            ])
        ]);
        
    }

    /**
     * Handle the PerfilModel "updated" event.
     */
    public function updated(PerfilModel $perfilModel): void
    {
        //
        $camposActualizados = array_diff_key($perfilModel->getDirty(), array_flip([$perfilModel->getUpdatedAtColumn()]));

        if(!empty($camposActualizados))
        {
            foreach($camposActualizados as $campo =>$nuevoValor)
            {
                $valorOrginal=$perfilModel->getOriginal($campo);
                EventLog::create([
                    'user_id'=> auth()->id(),
                    'event_type'=>'Registro actualizado',
                    'event_data'=>json_encode([
                        'tabla'=>$perfilModel->getTable(),
                        'registro_id'=>$perfilModel->id,
                        'campo_modificado'=>$campo,
                        'valor_anterior'=>$valorOrginal,
                        'valor_nuevo'=>$nuevoValor
                        ])
                ]);
            }
            
           
        }
    }

    /**
     * Handle the PerfilModel "deleted" event.
     */
    public function deleted(PerfilModel $perfilModel): void
    {
        //
    }

    /**
     * Handle the PerfilModel "restored" event.
     */
    public function restored(PerfilModel $perfilModel): void
    {
        //
    }

    /**
     * Handle the PerfilModel "force deleted" event.
     */
    public function forceDeleted(PerfilModel $perfilModel): void
    {
        //
    }
}
