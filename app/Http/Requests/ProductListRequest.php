<?php

namespace App\Http\Requests;

use App\Core\Controller\ListRequest;

/**
 * Request class for Product list API
 * Extends base ListRequest to define allowed sorts and filters
 */
class ProductListRequest extends ListRequest
{
    /**
     * Default values
     */
    protected int $defaultPerPage = 15;
    protected string $defaultSortBy = 'created_at';
    protected string $defaultDirection = 'desc';

    /**
     * Allowed columns for sorting
     */
    protected array $allowedSorts = [
        'id',
        'created_at',
        'sell_price',
        'name',
        'quantity'
    ];

    /**
     * Allowed filter keys
     */
    protected array $allowedFilters = [
        'keyword',
        'brand_id',
        'line_id',
        'is_active',
        'min_price',
        'max_price',
        'in_stock'
    ];
}
