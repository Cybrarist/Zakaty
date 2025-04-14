<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\MoneyAssetsOverview;
use App\Filament\Widgets\MoneyAssetsZakahOverview;
use App\Filament\Widgets\TitleWidget;
use App\Filament\Widgets\PricesToday;
use App\Helpers\CacheHelper;
use App\Models\Currency;
use App\Models\User;
use Awcodes\Palette\Forms\Components\ColorPicker;
use Awcodes\Palette\Forms\Components\ColorPickerSelect;
use Faker\Provider\Text;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Auth;

class EditProfile extends \Filament\Pages\Auth\EditProfile
{

    protected function getForms(): array
    {

        return [
            'form' => $this->form(
                $this->makeForm()
                    ->columns(3)
                    ->schema([
                        $this->getNameFormComponent()->columnSpan(1),
                        $this->getEmailFormComponent()->columnSpan(1),
                        $this->getPasswordFormComponent()->columnSpan(1),

                        Select::make('currency_id')
                            ->label('Default Currency')
                            ->model(User::class)
                            ->options(Currency::all()->pluck('code_name', 'id'))
                            ->native(false)
                            ->preload()
                            ->required()
                            ->searchable(),


//                        Section::make('Notification Settings')
//                            ->columns(3)
//                            ->columnSpanFull()
//                            ->schema([
//                                Checkbox::make('settings.apprise_url')
//                                    ->label('Apprise Endpoint')
//                                    ->hintIcon( "heroicon-o-information-circle" ,"http://apprise_url/notify/key/?tags=all")
//                                ,
//
//                            ]),


                        Section::make('General Settings')
                            ->columns(3)
                            ->columnSpanFull()
                            ->schema([
                                Checkbox::make('settings.enable_top_navbar')
                                    ->label('Enable Top Navbar instead of Sidebar'),

                            ]),

                        Section::make('Money Assets Zakah Settings')
                            ->columns(3)
                            ->columnSpanFull()
                            ->collapsible()
                            ->schema([
                                DatePicker::make('next_money_zakah_date')
                                    ->label('Next Money Zakah Date')
                                    ->date()
                                    ->minDate(today()),

                                Checkbox::make('settings.consider_jeweleries_in_zakah')
                                    ->label('Consider Jeweleries For Gold & Silver Zakah')
                                    ->inline(false),

                            ]),

                    ])
                    ->statePath('data'),
            ),
        ];
    }


}
