<?php

namespace App\Observers;

use App\Models\Candidatos;

class CandidatoObserver
{
    /**
     * Handle the Candidatos "created" event.
     */
    public function created(Candidatos $candidatos): void
    {
        //
    }

    /**
     * Handle the Candidatos "updated" event.
     */
    public function updated(Candidatos $candidatos): void
    {
        //
    }

    /**
     * Handle the Candidatos "deleted" event.
     */
    public function deleted(Candidatos $candidatos): void
    {
        //
    }

    /**
     * Handle the Candidatos "restored" event.
     */
    public function restored(Candidatos $candidatos): void
    {
        //
    }

    /**
     * Handle the Candidatos "force deleted" event.
     */
    public function forceDeleted(Candidatos $candidatos): void
    {
        //
    }
}
