<?php

namespace App\Filament\Widgets;

use App\Models\Transaksi;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon as SupportCarbon;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;
    protected function getStats(): array
    {

        $startDate = ! is_null($this->filters['startDate'] ?? null) ?
            SupportCarbon::parse($this->filters['startDate']) :
            null;

        $endDate = ! is_null($this->filters['endDate'] ?? null) ?
            SupportCarbon::parse($this->filters['endDate']) :
            now();

        $pemasukan = Transaksi::incomes()->whereBetween('tanggal', [$startDate, $endDate])->sum('jumlah');
        $pengeluaran = Transaksi::expenses()->whereBetween('tanggal', [$startDate, $endDate])->sum('jumlah');

        return [
            Stat::make('Pemasukan', $pemasukan)
                ->description('32k', 'pemasukan')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Pengeluaran', $pengeluaran)
                ->description('5k', 'pengeluaran')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
            Stat::make('Average time on page', $pemasukan - $pengeluaran)
                ->description('25%', 'Sisa')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning'),
        ];
    }
}
