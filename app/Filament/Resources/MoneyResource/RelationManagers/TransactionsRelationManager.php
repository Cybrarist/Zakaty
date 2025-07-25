<?php

namespace App\Filament\Resources\MoneyResource\RelationManagers;

use App\Enums\TransactionTypeEnum;
use App\Filament\Resources\TransactionResource;
use App\Models\Currency;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    public function table(Table $table): Table
    {

        return $table
            ->recordTitleAttribute('name')
            ->recordUrl(
                fn (Model $record): string => route('filament.admin.resources.transactions.edit', ['record' => $record]),
            )

            ->modifyQueryUsing(function ($query) {
                $query->with(['currency']);
            })
            ->defaultSort('date','desc')
            ->columns([
                TextColumn::make('date')->sortable(),

                TextColumn::make('name')->searchable()->sortable(),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(function ($record) {
                        return $record->currency->currency_symbol . Number::format($record->amount);
                    }),

                TextColumn::make('type')
                    ->label('Type')
                    ->color(fn($state) => TransactionTypeEnum::get_color($state))
                    ->badge()
            ])
            ->filters([
                DateRangeFilter::make('date'),

                SelectFilter::make('currency_id')
                    ->label(__('general.currency.title'))
                    ->options(Currency::all()->pluck('name', 'id'))
                    ->multiple(),

                SelectFilter::make('type')
                    ->label('Type')
                    ->options(TransactionTypeEnum::class)
                    ->multiple()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
