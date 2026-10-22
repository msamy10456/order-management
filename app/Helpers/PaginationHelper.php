<?php

namespace App\Helpers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class PaginationHelper
{
    public static function paginationCountPerPage()
    {
        $count = request()->input('per_page') ?: 5;
        if (request()->per_page === 'all') {
            return PHP_INT_MAX - 1;
        }

        return ($count >= 5 && $count <= 100) ? $count : 5;
    }

    public static function paginateData(Builder $builder): LengthAwarePaginator
    {
        return $builder->fastPaginate(static::paginationCountPerPage(), page: static::getCurrentPage());
    }

    public static function getCurrentPage()
    {
        return request()->input('page') ?: 1;
    }

    public static function calculatePageByPosition(int $position): float|int
    {
        return (int) ceil($position / 20);
    }
}
