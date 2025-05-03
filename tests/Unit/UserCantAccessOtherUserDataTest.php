<?php

namespace Tests\Unit;


use App\Models\Currency;
use App\Models\ExchangePrice;
use App\Models\Gold;
use App\Models\Money;
use App\Models\Silver;
use App\Models\User;
use App\Models\ZakahPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertEquals;

class UserCantAccessOtherUserDataTest extends TestCase
{

    use RefreshDatabase;


    protected function setUp(): void
    {
        parent::setUp();

        Currency::create([
            'name' => 'USD',
            'code' => 'USD',
            'rate' => 1,
        ]);

        User::factory(2)->create();

        Money::factory(10)->create();
        Gold::factory(10)->create();
        Silver::factory(10)->create();
        ZakahPayment::factory(10)->create();

    }


    public function test_user_can_access_his_own_records_only(): void
    {
        $this->actingAs(User::first());

        //check user can access all his own records
        $gold = Gold::all();
        $silver= Silver::all();
        $zakah_payment= ZakahPayment::all();
        $money= Money::all();


        assertEquals(10, $gold->count());
        assertEquals(10, $silver->count());
        assertEquals(10, $zakah_payment->count());
        assertEquals(10, $money->count());


        $this->actingAs(User::find(2));

        $gold = Gold::all();
        $silver= Silver::all();
        $zakah_payment= ZakahPayment::all();
        $money= Money::all();


        assertEmpty($gold->count());
        assertEmpty($silver->count());
        assertEmpty($zakah_payment->count());
        assertEmpty( $money->count());


        Gold::factory(1)->create(['user_id' => 2]);
        Silver::factory(1)->create(['user_id' => 2]);
        ZakahPayment::factory(1)->create(['user_id' => 2]);
        Money::factory(1)->create(['user_id' => 2]);

        // make sure the second user has records
        $gold = Gold::all();
        $silver= Silver::all();
        $zakah_payment= ZakahPayment::all();
        $money= Money::all();

        assertEquals(1, $gold->count());
        assertEquals(1, $silver->count());
        assertEquals(1, $zakah_payment->count());
        assertEquals(1, $money->count());


        //make sure the first user can't access the second user's records
        $this->actingAs(User::first());
        $gold = Gold::all();
        $silver= Silver::all();
        $zakah_payment= ZakahPayment::all();
        $money= Money::all();

        assertEquals(10, $gold->count());
        assertEquals(10, $silver->count());
        assertEquals(10, $zakah_payment->count());
        assertEquals(10, $money->count());


    }
}
