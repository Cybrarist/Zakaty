<?php

namespace App\Filament\Resources\ZakahPaymentResource\Widgets;

use App\Enums\ZakahPaymentTypeEnum;
use App\Models\ZakahPayment;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Livewire\Attributes\On;
use function PHPUnit\Framework\isFalse;

class ZakahPaymentChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'zakahPaymentChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Zakah Payments';

    protected static bool $deferLoading = true;
    protected static bool $isLazy= true;


    protected int | string | array $columnSpan = 1;


    public static function canView(): bool
    {
        return ZakahPayment::count() > 0;
    }

    #[On('refresh_zakah_payment_widgets')]

    public function refreshData($filters){

        $this->filterFormData = $filters['filters'];

        $this->updateOptions();
    }


    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {

        $payments_query= ZakahPayment::query()->orderBy('paid_at');

        if (isset($this->filterFormData['status']['values']) && sizeof($this->filterFormData['status']['values']) > 0 ) $payments_query->whereIn('status', $this->filterFormData['status']['values']);
        if (isset($this->filterFormData['type']['values']) && sizeof($this->filterFormData['type']['values']) > 0 ) $payments_query->whereIn('type', $this->filterFormData['type']['values']);
        if (isset($this->filterFormData['payment_method']['values']) && sizeof($this->filterFormData['payment_method']['values']) > 0 ) $payments_query->whereIn('payment_method', $this->filterFormData['payment_method']['values']);

        if (isset($this->filterFormData['paid_at']['paid_at'] ) &&  $this->filterFormData['paid_at']['paid_at']){
            $dates=  explode(' - ', $this->filterFormData['paid_at']['paid_at']);
            $payments_query->whereBetween('paid_at', [
                Carbon::createFromFormat('d/m/Y', $dates[0]),
                Carbon::createFromFormat('d/m/Y', $dates[1])
            ]);


        }


        $payments=$payments_query->get();

        $days_only= $payments->pluck('paid_at')->toArray();

        $unique_days_only = array_unique($days_only);

        $basic_data =[];


        foreach ($unique_days_only as $day) {
            $basic_data[$day->toDateString()] = [
                'x'=>$day->toDateString(),
                'y' => 0
            ];
        }

        $data =[
            ZakahPaymentTypeEnum::Money->value=> [
                'name' => ZakahPaymentTypeEnum::Money->name,
                'data' => $basic_data,
            ],
            ZakahPaymentTypeEnum::Animal->value=> [
                'name' => ZakahPaymentTypeEnum::Animal->name,
                'data' => $basic_data,
            ],
            ZakahPaymentTypeEnum::Crops->value=> [
                'name' => ZakahPaymentTypeEnum::Crops->name,
                'data' => $basic_data,
            ],
        ];

        foreach ($payments as $payment) {
            $data[$payment->type->value]['data'][$payment->paid_at->toDateString()]=[
                'x'=>$payment->paid_at->toDateString(),
                'y' =>  $data[$payment->type->value]['data'][$payment->paid_at->toDateString()]['y'] + $payment->usd_amount,
            ];
        }

        $data = array_map(function ($item) {
            $item['data'] = array_values($item['data']);

            return $item;
        }, $data);



        return [

            'chart' => [
                'type' => 'bar',
                'height' => 300,
//                'width' => '100%',
                'stacked' => true,
            ],
            'series' =>array_values($data),
            'colors' => ZakahPaymentTypeEnum::get_colors(),
            'plotOptions' => [
                'bar' => [
                    'horizontal' => false,
                    'columnWidth'=>'10%',
                    'borderRadius' => 3,
                ],
            ],

//            'grid' => [
//                'show' => false,
//            ],
            'xaxis' => [
                "type" => 'datetime',
                'categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            ],
            'yaxis' => [
                "decimalsInFloat" => 2,
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
        ];


    }

    protected function extraJsOptions(): ?\Filament\Support\RawJs
    {
        return RawJs::make(<<<'JS'
        {
            yaxis: {
                labels: {
                    formatter: function (val, index) {
                        return '$' + val.toLocaleString('en-US');

                    }
                }
            },
            dataLabels: {
                 formatter: function (val, index) {
                        return '$' + val.toLocaleString('en-US');

                    }
            }
        }
        JS);
    }
}
