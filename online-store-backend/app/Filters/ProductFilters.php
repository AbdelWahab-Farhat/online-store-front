<?php

namespace App\Filters;

class ProductFilters extends Filters
{
    /**
     * Filter by array of product IDs.
     */
    public function ids($ids)
    {
        if (is_array($ids) && count($ids) > 0) {
            return $this->builder->whereIn('id', $ids);
        }
        return $this->builder;
    }

    /**
     * Filter by category id.
     */
    public function category_id($id)
    {
        return $this->builder->whereHas('categories', function ($q) use ($id) {
            $q->where('categories.id', $id);
        });
    }

    /**
     * Filter by category slug.
     */
    public function category_slug($slug)
    {
        return $this->builder->whereHas('categories', function ($q) use ($slug) {
            $q->where('categories.slug', $slug);
        });
    }

    /**
     * Search by name or description.
     */
    public function search($term)
    {
        return $this->builder->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }

    /**
     * Filter by stock availability.
     */
    public function in_stock($value)
    {
        // $value will be '1', 'true', 'on', 'yes' from the HTTP request typically
        if (filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
            return $this->builder->inStock();
        }
        return $this->builder;
    }

    /**
     * Sort results by column.
     */
    public function sort_by($column)
    {
        $allowedSorts = ['name', 'price', 'created_at', 'quantity'];

        if (in_array($column, $allowedSorts)) {
            $sortDir = $this->request->get('sort_dir', 'desc') === 'asc' ? 'asc' : 'desc';
            return $this->builder->orderBy($column, $sortDir);
        }

        return $this->builder;
    }
}
