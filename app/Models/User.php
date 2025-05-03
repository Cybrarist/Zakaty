<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Casts\MoneyCast;
use App\Enums\UserRoleEnum;
use App\Observers\UserObserver;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[ObservedBy(UserObserver::class)]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use  HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'total_money_usd',
        'next_money_zakah_date',
        'total_pay_money',
        'total_gold_usd',
        'total_silver_usd',
        'settings',
        'currency_id',
        'role',
        'settings.apprise_url',
        'settings.consider_jeweleries_in_zakah',
        'settings.enable_top_navbar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'settings' => 'json',
            'total_money_usd' => MoneyCast::class,
            'total_gold_usd' => MoneyCast::class,
            'total_silver_usd' => MoneyCast::class,
            'total_pay_money' => MoneyCast::class,
            'next_money_zakah_date' => 'date',
            'role' => UserRoleEnum::class,
            'settings.enable_top_navbar'=>'boolean',
        ];
    }


    /**
     * Attributes
     */

    protected function totalMoneyAssets(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->total_money_usd + $this->total_gold_usd + $this->total_silver_usd,
        )->shouldCache();
    }



    /**
     * Relationships
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function money(): HasMany
    {
        return $this->hasMany(Money::class);
    }

    public function gold(): HasMany
    {
        return $this->hasMany(Gold::class);
    }

    public function silver(): HasMany
    {
        return $this->hasMany(Silver::class);
    }

    public function zakah_payments(): User|HasMany
    {
        return $this->hasMany(ZakahPayment::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
