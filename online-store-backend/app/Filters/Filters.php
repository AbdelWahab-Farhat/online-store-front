<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class Filters
{
    protected Request $request;
    protected Builder $builder;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply the filters to the builder.
     */
    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        foreach ($this->filters() as $name => $value) {
            if (method_exists($this, $name) && $value !== '' && $value !== null) {
                $this->$name($value);
            }
        }

        return $this->builder;
    }

    /**
     * Get all request filters data.
     */
    public function filters(): array
    {
        return $this->request->all();
    }
}
