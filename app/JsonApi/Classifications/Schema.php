<?php

namespace App\JsonApi\Classifications;

use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{

    /**
     * @var string
     */
    protected $resourceType = 'classifications';

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
        ];
    }
    public function getRelationships($classification, $isPrimary, array $includeRelationships)
    {
        return [
            'rvs' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true
            ]
        ];
    }
}
