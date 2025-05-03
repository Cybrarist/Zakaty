<?php

namespace App\Filament\Resources;

use App\Enums\ZakahPaymentMethodEnum;
use App\Enums\ZakahPaymentStatusEnum;
use App\Enums\ZakahPaymentTypeEnum;
use App\Filament\Resources\ZakahPaymentResource\Pages;
use App\Filament\Resources\ZakahPaymentResource\RelationManagers;
use App\Models\Currency;
use App\Models\User;
use App\Models\ZakahPayment;
use DeepCopy\TypeFilter\Date\DatePeriodFilter;
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
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class ZakahPaymentResource extends Resource
{
    protected static ?string $model = ZakahPayment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort=4;


    public static function form(Form $form): Form
    {
        return $form
            ->columns(['md' => 2, 'lg' => 4])
            ->schema([
                Forms\Components\Section::make([

                    TextInput::make('name')
                        ->columnSpanFull()
                        ->string()
                        ->maxLength(255),

                    Forms\Components\DatePicker::make('paid_at')
                        ->default(today())
                        ->required()
                        ->native(false),

                    Select::make('status')
                        ->columnSpan(1)
                        ->label('Status')
                        ->options(ZakahPaymentStatusEnum::class)
                        ->required()
                        ->default(ZakahPaymentStatusEnum::Paid->value)
                        ->preload()
                        ->native(false),

                    Select::make('type')
                        ->columnSpan(1)
                        ->label('Type')
                        ->options(ZakahPaymentTypeEnum::class)
                        ->live(debounce: 500)
                        ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {



                            if ($state == ZakahPaymentTypeEnum::Money->value){
                                if (!$get('currency_id'))
                                {
                                    $set('amount', ceil(Auth::user()->total_pay_money * Auth::user()->currency->rate));
                                    $set('currency_id', Auth::user()->currency_id);
                                }
                                else{
                                    $set('amount', ceil(Auth::user()->total_pay_money * Currency::findOrFail($get('currency_id'))->rate));
                                }

                            }
                        })
                        ->required()
                        ->preload()
                        ->native(false),

                    Select::make('payment_method')
                        ->columnSpan(1)
                        ->label('Payment Method')
                        ->options(ZakahPaymentMethodEnum::class)
                        ->nullable()
                        ->default(ZakahPaymentMethodEnum::Cash)
                        ->preload()
                        ->native(false),
                ])
                    ->columnSpan([
                        'sm'=>1,
                        'md'=>2,
                        'lg'=>2

                    ])
                    ->columns(3),

                Forms\Components\Section::make([

                    Forms\Components\TextInput::make('amount')
                        ->label('Amount')
                        ->default(0)
                        ->numeric()
                        ->minValue(0.001)
                        ->step(0.001)
                        ->required(),

                    Select::make('currency_id')
                        ->label('Default Currency')
                        ->model(User::class)
                        ->options(Currency::all()->pluck('code_name', 'id'))
                        ->live(debounce: 500)
                        ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get){
                            if (!$state) return;
                            if (!$get('amount'))
                                $set('amount',   ceil(Auth::user()->total_pay_money * Currency::findOrFail($state)->rate));

                        })
                        ->native(false)
                        ->preload()
                        ->required()
                        ->searchable(),

                ])
                    ->columnSpan([
                        'sm'=>1,
                        'md'=>2,
                        'lg'=>2

                    ])->columns(2),



                Textarea::make('notes')
                    ->columnSpanFull()
                    ->autosize()
                    ->nullable(),

                Forms\Components\FileUpload::make('images')
                    ->label('Images')
                    ->image()
                    ->disk('zakah_payment')
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
            ->modifyQueryUsing(function ($query) {
                $query->with('currency');
            })
            ->filtersApplyAction(function ($livewire){
                $livewire->dispatch('refresh_zakah_payment_widgets', ['filters'=>$livewire->tableFilters]);;
            })
            ->defaultSort('paid_at','desc')
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('paid_at')->date()->sortable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('type')->badge(),
                TextColumn::make('payment_method')->badge(),
                TextColumn::make('amount')
                    ->formatStateUsing(function ($record) {
                        return Number::currency($record->amount,$record->currency->code ) ;
                    }),
                TextColumn::make('usd_amount')
                    ->label('Amount($)')
                    ->prefix('$')
                    ->sortable()

            ])
            ->filters([
                SelectFilter::make('status')
                ->label('Status')
                ->options(ZakahPaymentStatusEnum::class)
                ->multiple()
                ->native(false),

                SelectFilter::make('type')
                ->label('Type')
                ->options(ZakahPaymentTypeEnum::class)
                ->multiple()
                ->native(false),

                SelectFilter::make('payment_method')
                ->label('Payment Method')
                ->options(ZakahPaymentMethodEnum::class)
                ->multiple()
                ->native(false),

                DateRangeFilter::make('paid_at')
                ,

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListZakahPayments::route('/'),
            'create' => Pages\CreateZakahPayment::route('/create'),
            'edit' => Pages\EditZakahPayment::route('/{record}/edit'),
        ];
    }
}
