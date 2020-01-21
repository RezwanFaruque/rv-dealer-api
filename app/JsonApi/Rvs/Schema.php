<?php

namespace App\JsonApi\Rvs;

use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{

    /**
     * @var string
     */
    protected $resourceType = 'rvs';

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
            'title' => $resource->title,
            'stock_number' => $resource->stock_number,
            'year' => $resource->year,
            'condition' => $resource->condition,
            'is_sold' => $resource->is_sold,
            'price_field' => $resource->price_field,
            'price' => $resource->price,
            'msrp' => $resource->msrp,
            'monthly_payment' => $resource->monthly_payment,
            'use_special_pricing' => $resource->use_special_pricing,
            'unit' => $resource->unit,
            'fp_id' => $resource->fp_id,
            'floorplan' => $resource->floorplan,
            'floorplan_image' => $resource->floorplan_image,
            'fuel_type' => $resource->fuel_type,
            'headline' => $resource->headline,
            'length' => $resource->length,
            'use_get_low_price' => $resource->use_get_low_price,
            'length_inches' => $resource->length_inches,
            'engine_model' => $resource->engine_model,
            'engine_manufacturer' => $resource->engine_manufacturer,
            'interior_color' => $resource->interior_color,
            'exterior_color' => $resource->exterior_color,
            'chassis' => $resource->chassis,
            'mileage' => $resource->mileage,
            'youtube_link' => $resource->youtube_link,
            'description' => $resource->description,
            'description_dealer_only' => $resource->description_dealer_only,
            'tour_360_url' => $resource->tour_360_url,
            'created-at' => $resource->created_at->toAtomString(),
            'updated-at' => $resource->updated_at->toAtomString(),
        ];
    }

    public function getRelationships($rv, $isPrimary, array $includeRelationships)
    {

        return [
            'brand' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => false,
                self::SHOW_DATA => isset($includeRelationships['brand']),
                self::DATA => function () use ($rv) {
                    return $rv->brand;
                },
            ],
            'model' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => false,
                self::SHOW_DATA => isset($includeRelationships['model']),
                self::DATA => function () use ($rv) {
                    return $rv->model;
                },
            ],
            'type' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => false,
                self::SHOW_DATA => isset($includeRelationships['type']),
                self::DATA => function () use ($rv) {
                    return $rv->type;
                },
            ],
            'options' => [
                self::SHOW_SELF => true,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['options']),
                self::DATA => function () use ($rv) {
                    return $rv->options;
                }
            ],
            'images' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => true,
                self::SHOW_DATA => isset($includeRelationships['images']),
                self::DATA => function () use ($rv) {
                    $filter = request()->get('filter');
                    if(isset($filter['images']) && isset($filter['images']['limit']))
                    {
                        return $rv->images->take($filter['images']['limit']);
                    }
                    return $rv->images;
                }
            ],
            'classifications' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => false,
                self::SHOW_DATA => isset($includeRelationships['classifications']),
                self::DATA => function () use ($rv) {
                    return $rv->classifications;
                }
            ],
            'attributes' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => false,
                self::SHOW_DATA => isset($includeRelationships['attributes']),
                self::DATA => function () use ($rv) {
                    return $rv->attributes;
                }
            ],
            'documents' => [
                self::SHOW_SELF => false,
                self::SHOW_RELATED => false,
                self::SHOW_DATA => isset($includeRelationships['documents']),
                self::DATA => function () use ($rv) {
                    return $rv->documents;
                }
            ]
        ];

    }


}
