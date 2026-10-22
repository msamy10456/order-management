<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TrafficFilterService
{
    private const VALID_FILTERS = ['1', '2', '3']; // 1: week, 2: month, 3: year

    private $filter;

    private $filterKey;

    private $startDate;

    private $endDate;

    private $model;

    public function __construct($model, $filterKey = 'filter')
    {
        $this->model = $model;
        $this->filterKey = $filterKey;
    }

    private function validateFilter(string $filter): string
    {
        if (! in_array($filter, self::VALID_FILTERS)) {
            return '1';
        }

        return $filter;
    }

    public function analyze(Request $request)
    {
        $this->filter = $this->validateFilter($request->query($this->filterKey, '1'));

        $this->setDateRange();

        return $this->getTrafficData();
    }

    private function setDateRange(): void
    {
        $now = Carbon::now();

        $this->startDate = match ($this->filter) {
            '1' => $now->clone()->startOfWeek(),
            '2' => $now->clone()->startOfMonth(),
            '3' => $now->clone()->startOfYear(),
        };

        $this->endDate = match ($this->filter) {
            '1' => $now->clone()->endOfWeek(),
            '2' => $now->clone()->endOfMonth(),
            '3' => $now->clone()->endOfYear(),
        };
    }

    private function getTrafficData(): array
    {
        return match ($this->filter) {
            '1' => $this->getWeeklyTraffic(),
            '2' => $this->getMonthlyTraffic(),
            '3' => $this->getYearlyTraffic(),
        };
    }

    private function getWeeklyTraffic(): array
    {
        $usersTraffic = $this->getBaseQuery()
            ->select(
                DB::raw('DAYNAME(created_at) as day'),
                DB::raw('count(*) as count')
            )
            ->groupBy('day')
            ->orderByRaw("FIELD(day, 'Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday')")
            ->get()
            ->pluck('count', 'day');

        return $this->formatWeeklyData($usersTraffic);
    }

    private function formatWeeklyData(Collection $data): array
    {
        $daysOfWeek = [
            'Saturday',
            'Sunday',
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
        ];

        $formatted = [];

        foreach ($daysOfWeek as $day) {
            $formatted[$day] = $data->get($day, 0);
        }

        return $formatted;
    }

    private function getMonthlyTraffic(): array
    {
        $usersTraffic = $this->getBaseQuery()
            ->select(
                DB::raw('WEEK(created_at) - WEEK(DATE_SUB(created_at, INTERVAL DAY(created_at)-1 DAY)) + 1 as week_number'),
                DB::raw('count(*) as count')
            )
            ->groupBy('week_number')
            ->orderBy('week_number')
            ->get()
            ->pluck('count', 'week_number');

        return $this->formatMonthlyData($usersTraffic);
    }

    private function formatMonthlyData(Collection $data): array
    {
        $formatted = [];

        for ($week = 1; $week <= 5; $week++) {
            $formatted["Week $week"] = $data->get($week, 0);
        }

        return $formatted;
    }

    private function getYearlyTraffic(): array
    {
        $usersTraffic = $this->getBaseQuery()
            ->select(
                DB::raw('MONTHNAME(created_at) as month'),
                DB::raw('count(*) as count')
            )
            ->groupBy('month')
            ->orderByRaw("FIELD(month, 'January', 'February', 'March', 'April', 'May', 'June',
                              'July', 'August', 'September', 'October', 'November', 'December')")
            ->get()
            ->pluck('count', 'month');

        return $this->formatYearlyData($usersTraffic);
    }

    private function formatYearlyData(Collection $data): array
    {
        $months = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December',
        ];

        $formatted = [];

        foreach ($months as $month) {
            $formatted[$month] = $data->get($month, 0);
        }

        return $formatted;
    }

    private function getBaseQuery()
    {
        return $this->model->whereBetween('created_at', [$this->startDate, $this->endDate]);
    }
}
