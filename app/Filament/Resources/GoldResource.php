<?php

namespace App\Filament\Resources;

use App\Enums\KaratEnum;
use App\Enums\PreciousMetalColorEnum;
use App\Enums\PreciousMetalTypeEnum;
use App\Enums\WeightEnum;
use App\Filament\Resources\GoldResource\Pages;
use App\Filament\Resources\GoldResource\RelationManagers;
use App\Models\Gold;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;
use Marvinosswald\FilamentInputSelectAffix\TextInputSelectAffix;

class GoldResource extends Resource
{
    protected static ?string $model = Gold::class;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?int $navigationSort=2;


    public static function getLabel(): ?string
    {
        return __('general.gold.title');
    }

    /**
     * @return string|null
     */
    public static function getPluralLabel(): ?string
    {
        return __('general.gold.title');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(4)
            ->schema([

                Forms\Components\Section::make([
                    TextInput::make('name')
                        ->columnSpanFull()
                        ->string()
                        ->required()
                        ->maxLength(255),

                    TextInputSelectAffix::make('weight')
                        ->columnSpan(1)
                        ->numeric()
                        ->required()
                        ->select(fn() => Forms\Components\Select::make('weight_unit')
                            ->extraAttributes([
                                'class' => 'w-20' // if you want to constrain the selects size, depending on your usecase
                            ])
                            ->default('g')
                            ->options(WeightEnum::class)
                        ),


                    Forms\Components\Select::make('karat')
                        ->label('Karat')
                        ->columnSpan(1)
                        ->name('karat')
                        ->options(KaratEnum::class)
                        ->preload()
                        ->default(24)
                        ->required()
                        ->native(false),

                    Forms\Components\Toggle::make('is_jewellery')
                        ->label('Is Jewellery?')
                        ->columnSpan(1)
                        ->inline(false)
                        ->default(true),

                ])
                    ->columnSpan([
                        'sm'=>1,
                        'md'=>1,
                        'lg'=>3

                    ])
                    ->columns(3),


                Forms\Components\Section::make([

                    Forms\Components\Select::make('type')
                        ->label(__('general.gold.type'))
                        ->columnSpan(1)
                        ->name('karat')
                        ->options(function (Get $get): array  {
                            return  ($get('is_jewellery'))
                                ? Arr::map(PreciousMetalTypeEnum::jewelleries(), function ( $type) {
                                    return $type->getLabel();
                                })
                                : Arr::map(PreciousMetalTypeEnum::non_jewelleries(), function ( $type) {
                                    return $type->getLabel();
                                });
                        })
                        ->live()
                        ->preload()
                        ->nullable()
                        ->native(false),

                    Forms\Components\Select::make('color')
                        ->label(__('general.color'))
                        ->columnSpan(1)
                        ->name('karat')
                        ->options(PreciousMetalColorEnum::gold())
                        ->preload()
                        ->nullable()
                        ->native(false),


                ])
                    ->columnSpan([
                        'sm'=>4,
                        'md'=>1,
                        'lg'=>1
                    ]),

                Textarea::make('notes')
                    ->label(__('general.notes'))
                    ->columnSpanFull()
                    ->nullable(),

                Forms\Components\FileUpload::make('images')
                    ->label(__('general.images'))
                    ->image()
                    ->disk('gold')
                    ->columnSpanFull()
                    ->panelLayout('grid')
                    ->previewable()
                    ->openable()
                    ->downloadable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        Auth::user()->load('currency');
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),

                TextColumn::make('weight')
                    ->label('Weight')
                    ->suffix(fn($record)=> $record->weight_unit->value),

                TextColumn::make('id')
                    ->label('Amount')
                    ->formatStateUsing(fn ($record) =>  Auth::user()->currency->currency_symbol . Number::format($record->usd_amount * Auth::user()->currency->rate)),

                TextColumn::make('usd_amount')
                    ->sortable()
                    ->label('Amount($)')
                    ->prefix('$')
                    ->formatStateUsing(fn ($state) =>  Number::format($state)),

                Tables\Columns\TextColumn::make('type')
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('color')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => PreciousMetalColorEnum::get_badge($state)),

                Tables\Columns\TextColumn::make('karat')
                    ->sortable()
                    ->badge(),

            ])
            ->filters([
                SelectFilter::make('weight_unit')
                ->label('Weight Unit')
                ->options(WeightEnum::class),


                SelectFilter::make('type')
                ->label('Type')
                ->options(PreciousMetalTypeEnum::class),

                SelectFilter::make('color')
                ->label('Color')
                ->options(PreciousMetalColorEnum::class),

                SelectFilter::make('karat')
                ->label('Karat')
                ->options(KaratEnum::class),


            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                ->after(fn($livewire)=> $livewire->dispatch('refresh_gold_widgets')),

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
            'index' => Pages\ListGold::route('/'),
            'create' => Pages\CreateGold::route('/create'),
            'edit' => Pages\EditGold::route('/{record}/edit'),
        ];
    }
}
