<?php

namespace App\Filament\Resources;

use App\Enums\UserRoleEnum;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?int $navigationSort=4;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationGroup ='Settings';


    public static function canAccess(): bool
    {
        return Auth::user()->role == UserRoleEnum::Admin;
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('name')
                    ->string()
                    ->required(),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->unique(User::class, 'email', ignoreRecord: true)
                    ->required()
                    ->autofocus()
                    ->label('Email')
                    ->maxLength(255),

                Forms\Components\Select::make('role')
                    ->options(UserRoleEnum::class)
                    ->required()
                    ->hint('Admin Can Add Users')
                    ->label('Role')
                    ->default('user'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable()->sortable(),
                TextColumn::make('role')
                ->badge(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                ->options(UserRoleEnum::class)
                ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->modalDescription(function ($record) {
                        $final_string= "Are you sure you would like to do this? </br><p style='color:red'>";

                        $money_count =$record->money()->count();
                        $gold_count =$record->gold()->count();
                        $silver_count =$record->silver()->count();

                        if ($money_count)
                            $final_string.= "The user still have {$money_count} money record</br>";

                        if ($gold_count)
                            $final_string.= "The user still have {$gold_count} gold record </br>";

                        if ($silver_count)
                            $final_string.= "The user still have {$silver_count} silver record</br>";

                        $final_string.= "</p>";

                        return Str::of($final_string)->toHtmlString();
                    }),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
