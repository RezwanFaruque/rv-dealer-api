<?php

namespace App\JsonApi\Models;

use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{

    /**
     * @var string
     */
    protected $resourceType = 'models';

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
            'name' => $resource->name,
            'brochure_url' => $resource->brochure_url,
            'brochure_title' => $resource->brochure_title,
            'created-at' => $resource->created_at->toAtomString(),
            'updated-at' => $resource->updated_at->toAtomString(),
        ];
    }

    /**
     * @param object $model
     * @param bool $isPrimary
     * @param array $includeRelationships
     * @return array
     * @internal param object $classification
     */
    public function getRelationships($model, $isPrimary, array $includeRelationships)
    {
        return [
            'brand' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['brand']),
                self::DATA => function () use ($model) {
                    return $model->brand;
                },
            ],
            'rvs' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true
            ]
        ];
    }
}
