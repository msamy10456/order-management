<?php

namespace Modules\Role\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            //            'status' => $this->whenHas('status'),
            'permissions' => PermissionResource::collection($this->whenLoaded('permissions')),
        ];
    }
}
