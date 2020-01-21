<?php

namespace App\JsonApi\Brands;

use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{

    /**
     * @var string
     */
    protected $resourceType = 'brands';

    /**
     * @param $resource
     *      the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }


    /**
     * @param $resource
     *      the domain record being serialized.
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            'name' => $resource->name
        ];
    }

    /**
     * @param object $brand
     * @param bool $isPrimary
     * @param array $includeRelationships
     * @return array
     * @internal param object $classification
     */
    public function getRelationships($brand, $isPrimary, array $includeRelationships)
    {
        return [
            'rvs' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true
            ]
        ];
    }
}
