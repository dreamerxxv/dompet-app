<?php

namespace App\Filament\Widgets;

use App\Models\Kategori;
use App\Models\Transaksi;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon as SupportCarbon;

class WidgetIncomeChart extends ChartWidget
{
    use InteractsWithPageFilters;
    protected static ?string $heading = 'Chart Pemasukan';

    protected function getData(): array
    {
        $startDate = ! is_null($this->filters['startDate'] ?? null) ?
            SupportCarbon::parse($this->filters['startDate']) :
            null;

        $endDate = ! is_null($this->filters['endDate'] ?? null) ?
            SupportCarbon::parse($this->filters['endDate']) :
            now();

        $data = Trend::query(Transaksi::incomes())
        ->between(
            start: $startDate ?? now(), // Use $startDate directly
            end: $endDate // Use $endDate directly
        )
            ->perDay()
            ->sum('jumlah');

        $kategori = Kategori::query(Transaksi::expenses());

        return [
            'labels' => $kategori->get()->map(fn(Kategori $value) => $value->nama),
            'datasets' => [
                [
                    'label' => 'Pendapatan Harian',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => [
                        'rgb(37, 150, 190)',
                        'rgb(255, 87, 51)',
                        'rgb(104, 255, 51)',
                        'rgb(51, 63, 255)',
                        'rgb(255, 51, 51)',
                        'rgb(255, 249, 51)'
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
