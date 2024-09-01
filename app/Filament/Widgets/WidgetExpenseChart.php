<?php

namespace App\Filament\Widgets;

use App\Models\Kategori;
use App\Models\Transaksi;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class WidgetExpenseChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Chart Pengeluaran';

    protected function getData(): array
    {
        $startDate = ! is_null($this->filters['startDate'] ?? null) ?
            Carbon::parse($this->filters['startDate']) :
            null;

        $endDate = ! is_null($this->filters['endDate'] ?? null) ?
            Carbon::parse($this->filters['endDate']) :
            now();

        $data = Trend::query(Transaksi::expenses())
            ->between(
                start: $startDate ?? now(), // Use $startDate directly
                end: $endDate // Use $endDate directly
            )
            ->perDay()
            ->sum('jumlah');

        $kategori = Kategori::query(Transaksi::expenses());

        return [
            'datasets' => [
                [
                    'label' => 'Pengeluaran Harian',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => [
                        'rgb(37, 150, 190)',
                        'rgb(255, 87, 51)',
                        'rgb(104, 255, 51)',
                        'rgb(51, 63, 255)',
                        'rgb(102, 215, 255)',
                        'rgb(255, 210, 102)',
                        'rgb(169, 255, 102)',
                        'rgb(255, 249, 51)'
                    ],
                ],
            ],
            'labels' => $kategori->get()->map(fn(Kategori $value) => $value->nama),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
