<?php

namespace AttractCores\LaravelCoreTranslation\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/**
 * Class TranslationResource
 *
 * @property \AttractCores\LaravelCoreTranslation\Models\Translation $resource
 *
 * @package AttractCores\LaravelCoreTranslation\Http\Resources;
 * Date: 05.03.2021
 * Version: 1.0
 * Author: Yure Nery <yurenery@gmail.com>
 */
class TranslationResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'    => $this->resource->getKey(),
            'group' => $this->resource->group,
            'key'   => $this->resource->key,
            'text'  => $this->resource->text,
            $this->mergeWhen($request->is('*backend/*'), function () {
                return [
                    'translatable_id'   => $this->resource->translatable_id,
                    'translatable_type' => $this->resource->translatable_type,
                    'translatable_field' => $this->resource->translatable_field,
                    'created_at'        => $this->resource->created_at ?
                        $this->resource->created_at->getPreciseTimestamp(3) : NULL,
                    'updated_at'        => $this->resource->updated_at ?
                        $this->resource->updated_at->getPreciseTimestamp(3) : NULL,
                ];
            })
        ];
    }

}