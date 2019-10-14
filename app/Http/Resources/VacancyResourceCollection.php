<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class VacancyResourceCollection extends ResourceCollection
{
    public $collects = 'App\Http\Resources\VacancyResource';
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        Array($request);return [
            'success' => true,
            'data' => $this->collection,
        ];
    }
}
