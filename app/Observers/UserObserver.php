<?php

namespace App\Observers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        if ($user->role === UserRole::CTV->value) {
            $sales = User::where('role', UserRole::SALE->value)->get();
            if ($sales->isNotEmpty()) {
                // Here we can implement more complex logic based on configuration
                // For now, random assignment
                $randomSale = $sales->random();
                $user->sale_id = $randomSale->id;
                $user->save();
            }
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
