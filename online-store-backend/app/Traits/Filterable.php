<?php

namespace App\Traits;

use App\Filters\Filters;
use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    /**
     * Apply all relevant filters to the query builder.
     */
    public function scopeFilter(Builder $query, Filters $filters): Builder
    {
        return $filters->apply($query);
    }
}
