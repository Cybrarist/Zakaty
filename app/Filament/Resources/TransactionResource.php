<?php

namespace App\Filament\Resources;

use App\Enums\MoneyTypeEnum;
use App\Enums\TransactionTypeEnum;
use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Helpers\CacheHelper;
use App\Models\Currency;
use App\Models\Money;
use App\Models\Transaction;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(['md' => 2, 'lg' => 4])
            ->schema([
                TextInput::make('name')
                    ->columnSpan([
                        'md'=>1,
                        'lg'=>2
                    ])
                    ->autofocus()
                    ->label('Name')
                    ->string()
                    ->maxLength(255)
                    ->required(),

                TextInput::make('amount')
                    ->columnSpan(1)
                    ->label('Amount')
                    ->numeric()
                    ->required(),

                Forms\Components\DatePicker::make('date')
                    ->columnSpan(1)
                    ->label('Date')
                    ->default(today())
                    ->required(),

                Select::make('currency_id')
                    ->columnSpan(1)
                    ->label(__('general.currency.title'))
                    ->model(User::class)
                    ->options(Currency::whereIn('id', CacheHelper::get_currencies_for_user_money_accounts(Auth::id()))
                        ->get()
                        ->pluck('code_name', 'id'))
                    ->default(Auth::user()->currency_id)
                    ->preload()
                    ->required()
                    ->searchable(),

                Select::make('type')
                    ->columnSpan(1)
                    ->label('Type')
                    ->options(TransactionTypeEnum::class)
                    ->required()
                    ->default('income')
                    ->preload()
                    ->native(false),

                Select::make('money_id')
                    ->relationship('money', 'name')
                    ->options(function (Forms\Get $get) {
                        return Money::query()
                            ->where('currency_id', $get('currency_id'))
                            ->pluck('name', 'id');
                    })
                    ->label('Money')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->columnSpan(1)
                    ->live(),

                Textarea::make('notes')
                    ->columnSpanFull()
                    ->autosize()
                    ->nullable(),

            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                $query->with(['currency', 'money']);
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


                TextColumn::make('money.name')
                    ->label('Money'),

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
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
