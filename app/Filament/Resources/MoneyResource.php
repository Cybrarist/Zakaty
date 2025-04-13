<?php

namespace App\Filament\Resources;

use App\Enums\MoneyTypeEnum;
use App\Filament\Resources\MoneyResource\Pages;
use App\Filament\Resources\MoneyResource\RelationManagers;
use App\Models\Currency;
use App\Models\Money;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;

class MoneyResource extends Resource
{
    protected static ?string $model = Money::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?int $navigationSort=1;

    public static function getLabel(): ?string
    {
        return __('general.money.title');
    }

    /**
     * @return string|null
     */
    public static function getPluralLabel(): ?string
    {
        return __('general.money.title');
    }


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
                    ->label('Account Name')
                    ->string()
                    ->maxLength(255)
                    ->required(),

                Select::make('currency_id')
                    ->columnSpan(1)
                    ->label('Currency')
                    ->relationship('currency', 'name')
                    ->default(Auth::user()->currency_id)
                    ->native(false)
                    ->preload()
                    ->searchable()
                    ->required(),

                TextInput::make('amount')
                    ->columnSpan(1)
                    ->label('Amount')
                    ->numeric()
                    ->required(),

                Select::make('type')
                    ->columnSpan(1)
                    ->label('Type')
                    ->options(MoneyTypeEnum::class)
                    ->required()
                    ->default('cash')
                    ->preload()
                    ->native(false),

                Textarea::make('notes')
                    ->columnSpanFull()
                ->nullable(),

                Forms\Components\FileUpload::make('images')
                    ->label('Images')
                    ->image()
                    ->disk('money')
                    ->columnSpanFull()
                    ->panelLayout('grid')
                    ->previewable()
                    ->openable()
                    ->downloadable(),

            ]);
    }

    public static function table(Table $table): Table
    {


        return $table
            ->emptyStateHeading(__('general.money.table.empty_heading'))
            ->modifyQueryUsing(function ($query) {
                $query->with(['currency']);
            })
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(function ($record) {
                        return $record->currency->currency_symbol . Number::format($record->amount);
                    }),
                TextColumn::make('usd_amount')
                    ->sortable()
                    ->label('Amount($)')
                    ->prefix('$')
                    ->formatStateUsing(fn ($state) =>  Number::format($state)),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
            ])
            ->filters([
                SelectFilter::make('currency_id')
                    ->label(__('general.currency.title'))
                    ->options(Currency::all()->pluck('name', 'id'))
                    ->multiple(),
                SelectFilter::make('type')
                    ->label(__('general.money.type'))
                    ->options(MoneyTypeEnum::array())
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                ->after(fn($livewire)=> $livewire->dispatch('refresh_money_widgets')),
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
            'index' => Pages\ListMoney::route('/'),
            'create' => Pages\CreateMoney::route('/create'),
            'edit' => Pages\EditMoney::route('/{record}/edit'),
        ];
    }
}
