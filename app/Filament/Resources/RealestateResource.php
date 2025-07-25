<?php

namespace App\Filament\Resources;

use Afsakar\LeafletMapPicker\LeafletMapPicker;
use App\Enums\RealEstateOwnershipMethod;
use App\Enums\RealEstateOwnershipReasonEnum;
use App\Enums\RealEstateRentTypeEnum;
use App\Enums\RealEstateTypeEnum;
use App\Filament\Resources\RealestateResource\Pages;
use App\Filament\Resources\RealestateResource\RelationManagers;
use App\Models\Currency;
use App\Models\Realestate;
use App\Models\User;
use Awcodes\Shout\Components\Shout;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class RealestateResource extends Resource
{
    protected static ?string $model = Realestate::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Shout::make('illegible')
                    ->visible(function (Forms\Get $get) {
                        return $get('ownership_reason') && ! in_array($get('ownership_reason'), RealEstateOwnershipReasonEnum::eligible())
                            &&
                            ! in_array($get('method_of_ownership'), RealEstateOwnershipMethod::eligible());
                    })
                ->content(__('realestate.hints.illegible'))
                ->type('warning')
                ->columnSpanFull(),



                //General Information
                Forms\Components\Section::make([

                    Forms\Components\TextInput::make('name')
                        ->columnSpanFull()
                        ->label(__('general.name')),

                    Forms\Components\TextInput::make('value')
                        ->numeric()
                        ->step(0.001)
                        ->label(fn() => __('general.value')),

                    Select::make('currency_id')
                        ->columnSpan(1)
                        ->label(__('general.currency.title'))
                        ->model(User::class)
                        ->options(Currency::all()->pluck('code_name', 'id'))
                        ->default(Auth::user()->currency_id)
                        ->preload()
                        ->required()
                        ->searchable(),

                    ])
                    ->columns(3)
                    ->columnSpan(2),

                Forms\Components\Section::make([
                    Select::make('ownership_reason')
                        ->options(RealEstateOwnershipReasonEnum::class)
                        ->label(__('realestate.ownership_reason'))
                        ->live()
                        ->required()
                        ->searchable()
                        ->preload(),


                    Select::make('method_of_ownership')
                        ->options(RealEstateOwnershipMethod::class)
                        ->required()
                        ->live()
                        ->label(__('realestate.method_of_ownership'))
                        ->searchable()
                        ->preload(),

                    Select::make('type')
                        ->options(RealEstateTypeEnum::class)
                        ->label(__('realestate.type'))
                        ->searchable()
                        ->preload(),



                ])
                ->columns(1)
                ->columnSpan(1),


                //selling information
                Forms\Components\Section::make([
                    Forms\Components\DatePicker::make('decided_to_sell_at')
                        ->label(__('realestate.decided_to_sell_at'))
                        ->helperText('this will calculate according to the above amount. make sure the amount reflects the actual worth of the property')
                        ->required()
                        ->date(),

                    Forms\Components\DatePicker::make('sold_at')
                        ->label(__('realestate.sold_at'))
                        ->nullable(),

                ])
                    ->heading(__('realestate.selling_details'))
                    ->columns(3)
                    ->visible(fn(Forms\Get $get) => in_array($get('ownership_reason'), [
                        RealEstateOwnershipReasonEnum::Selling->value,
                        RealEstateOwnershipReasonEnum::SellingButRentingForNow->value
                    ])),

                //renting information
                Forms\Components\Section::make([

                    Select::make('rent_type')
                        ->options(RealEstateRentTypeEnum::class)
                        ->requiredIf('ownership_reason', RealEstateOwnershipReasonEnum::Renting->value)
                        ->default(RealEstateRentTypeEnum::Monthly->value)
                        ->label(__('realestate.rent_type'))
                        ->searchable()
                        ->preload(),

                    Forms\Components\TextInput::make('rent_amount')
                    ->numeric()
                    ->requiredIf('ownership_reason', RealEstateOwnershipReasonEnum::Renting->value)
                    ->step(0.001)
                    ->label(__('realestate.rent_amount')),
                ])
                    ->heading(__('realestate.selling_details'))
                    ->columns(3)
                    ->visible(fn(Forms\Get $get) => $get('ownership_reason') == RealEstateOwnershipReasonEnum::Renting->value),


                //Other info
                Textarea::make('notes')
                    ->autosize()
                    ->label(__('general.notes'))
                    ->columnSpanFull(),

                Forms\Components\Section::make([
                    Forms\Components\FileUpload::make('images')
                        ->label(__('general.images'))
                        ->image()
                        ->disk('realestate')
                        ->columnSpanFull()
                        ->panelLayout('grid')
                        ->previewable()
                        ->openable()
                        ->columnSpan(1)
                        ->downloadable(),

                    Forms\Components\FileUpload::make('documents')
                        ->label(__('general.documents'))
                        ->disk('realestate')
                        ->columnSpanFull()
                        ->panelLayout('grid')
                        ->previewable()
                        ->openable()
                        ->columnSpan(1)
                        ->downloadable(),

                ])
                    ->heading(__('general.attachments'))
                    ->collapsible()
                    ->collapsed()
                    ->columns(2),

                LeafletMapPicker::make('coordinates')
                    ->label(__( 'realestate.location' ))
                    ->height('500px')
                    ->defaultZoom(15)
                    ->draggable()
                    ->clickable()
                    ->myLocationButtonLabel('Go to My Location')
                    ->columnSpanFull()
                    ->nullable(),

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('usd_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lng')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contract_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ownership_reason')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency_id')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
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
            'index' => Pages\ListRealestates::route('/'),
            'create' => Pages\CreateRealestate::route('/create'),
            'edit' => Pages\EditRealestate::route('/{record}/edit'),
        ];
    }
}
