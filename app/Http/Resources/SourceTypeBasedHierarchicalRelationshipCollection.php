<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class SourceTypeBasedHierarchicalRelationshipCollection extends EditHistoryResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return $this->decorateWithId([
            'code' => $this->code,
            'name' => $this->name,
            'source_type' => new OrderSourceTypeCollection($this->sourceType),
            'parent_id' => $this->parent_id,
            'is_active' => $this->is_active
        ]);
    }

    protected function decorateWithParent(array $array, string $parentClass): array
    {
        if (empty($array['parent_id']) === false) {
            $array['parent'] = new SourceTypeBasedHierarchicalRelationshipCollection(
                $parentClass::find($array['parent_id'])
            );
            unset($array['parent_id']);
        }

        return $this->decorateWithEditHistory($array);
    }
}
