<?php

namespace App\Filters;

class CategoryFilters extends Filters
{
    /**
     * Search categories by name.
     */
    public function search($term)
    {
        return $this->builder->where('name', 'like', "%{$term}%");
    }

    /**
     * Filter active categories.
     */
    public function active($value)
    {
        if (filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
            return $this->builder->active();
        }
        return $this->builder;
    }
}
