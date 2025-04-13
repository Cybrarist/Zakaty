
your money has reached below Nisab Today, would you like to reset the zakah date or keep it as it on on
<p>
    {{$user->next_money_zakah_date}}
</p>


<p>
    <a href="{{\Illuminate\Support\Facades\URL::temporarySignedRoute('money.reset' , now()->addDay() , ['user'=>$user->id])}}"> Click This Button To Reset</a>
</p>
