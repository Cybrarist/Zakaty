<?php

namespace App\Observers;

use App\Enums\TransactionTypeEnum;
use App\Models\Money;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TransactionObserver
{

    public function creating(Transaction $transaction): void
    {
        $transaction->user_id = Auth::id();

        $money = Money::query()
            ->where('id', $transaction->money_id);

        if ($transaction->type == TransactionTypeEnum::Income)
            $money->increment('amount', $transaction->amount * 1000);
        elseif ($transaction->type == TransactionTypeEnum::Expense)
            $money->decrement('amount', $transaction->amount * 1000);
        else
            throw new \Exception(
                'Transaction type is not valid'
            );

    }
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        //
    }

    public function updating(Transaction $transaction): void
    {
        $difference = $transaction->amount - $transaction->getOriginal('amount');

        $money = Money::query()
            ->where('id', $transaction->money_id);

        if ($transaction->type == TransactionTypeEnum::Income)
            $money->increment('amount', $difference * 1000);
        elseif ($transaction->type == TransactionTypeEnum::Expense)
            $money->decrement('amount', $difference * 1000);
        else
            throw new \Exception(
                'Transaction type is not valid'
            );
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        $money = Money::query()
            ->where('id', $transaction->money_id);

        if ($transaction->type == TransactionTypeEnum::Income)
            $money->decrement('amount', $transaction->amount * 1000);
        elseif ($transaction->type == TransactionTypeEnum::Expense)
            $money->increment('amount', $transaction->amount * 1000);
        else
            throw new \Exception(
                'Transaction type is not valid'
            );
    }

    /**
     * Handle the Transaction "restored" event.
     */
    public function restored(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "force deleted" event.
     */
    public function forceDeleted(Transaction $transaction): void
    {
        //
    }
}
