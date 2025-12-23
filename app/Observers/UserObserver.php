<?php

namespace App\Observers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        if ($user->role == UserRole::CTV->value) {
            $sales = User::where('role', UserRole::SALE->value)->get();
            if ($sales->isNotEmpty()) {
                $randomSale = $sales->random();
                $user->sale_id = $randomSale->id;
                $user->save();
            }

            $sale = $user->sale;
            if ($sale) {
                $user->cameras()->sync($sale->cameras()->pluck('id'));
            }
        }
    }
}
