<?php

namespace App\JsonApi\Rvs;

use CloudCreativity\LaravelJsonApi\Rules\AllowedFieldSets;
use CloudCreativity\LaravelJsonApi\Validation\AbstractValidators;

class Validators extends AbstractValidators
{

    /**
     * Allowed parameters for pagination
     * @var array
     */
    protected $allowedPagingParameters = ['number', 'size'];

    /**
     * The include paths a client is allowed to request.
     *
     * @var string[]|null
     *      the allowed paths, an empty array for none allowed, or null to allow all paths.
     */
    protected $allowedIncludePaths = [
        'brand',
        'model',
        'type',
        'options',
        'classifications',
        'images',
        'attributes',
        'documents'
    ];

    /**
     * The sort field names a client is allowed send.
     *
     * @var string[]|null
     *      the allowed fields, an empty array for none allowed, or null to allow all fields.
     */
    protected $allowedSortParameters = null;


    /**
     * Get resource validation rules.
     *
     * @param mixed|null $record
     *      the record being updated, or null if creating a resource.
     * @return mixed
     */
    protected function rules($record = null): array
    {
        return [
            //
        ];
    }

    /**
     * Get query parameter validation rules.
     *
     * @return array
     */
    protected function queryRules(): array
    {
        return [
            'page.number' => 'filled|numeric|min:1',
            'page.size' => 'filled|numeric',
            'filter.sold' => 'boolean',
            'filter.condition' => 'boolean',
            'filter.brand'=> 'filled|numeric|min:1',
            'filter.option'=> 'filled|max:255',
            'filter.options'=> 'array|filled',
            'filter.brand_list'=> 'filled',
            'filter.model'=> 'filled|numeric|min:1',
            'filter.type'=> 'filled|numeric|min:1',
            'filter.ids' => 'filled',
            'filter.min_discount' => 'filled|numeric'
        ];
    }
    public function allowedFieldSets(): AllowedFieldSets
    {
        return parent::allowedFieldSets();
    }

}
