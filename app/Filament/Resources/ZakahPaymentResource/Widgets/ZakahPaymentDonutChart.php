<?php

namespace App\Filament\Resources\ZakahPaymentResource\Widgets;

use App\Models\ZakahPayment;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Illuminate\Support\Number;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Livewire\Attributes\On;

class ZakahPaymentDonutChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'zakahPaymentDonutChart';


    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Zakah Payments';
    protected static bool $deferLoading = true;
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

        $payments=$payments_query
            ->groupBy('type')
            ->selectRaw('type, sum(usd_amount)/1000 as total_amount')
            ->get();

        return [
            'chart' => [
                'type' => 'donut',
                'height' => 300,
            ],
            'plotOptions' => [
                'pie' => [
                    'expandOnClick'=> false,
                    'donut' => [
                        'labels'=>[
                            'show'=>true,
                            'total'=>[
                                'show'=>true,
                                'showAlways'=>true,
                                'label' =>'Total',
                                'value'=> $payments->sum('total_amount'),
                            ]
                        ]
                    ],
                    'position'=> 'top'
                ],
            ],
            'series' => $payments->pluck('total_amount')->toArray(),
            'labels' => $payments->pluck('type')->toArray(),
            'legend' => [
                'labels' => [
                    'fontFamily' => 'inherit',
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

            plotOptions: {
                pie:{
                    donut :{
                        labels:{
                            total: {
                                formatter: function (w) {
                                    return '$' + w.globals.seriesTotals
                                      .reduce((a, b) => a + b, 0)
                                      .toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                }
                            }
                        }
                    }
                }
            }
        }
        JS);
    }
}
