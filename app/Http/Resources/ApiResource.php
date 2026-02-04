<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiResource extends JsonResource
{
    /**
     * Wrap single resource in "data" key. Disable when returning from success() which already has "data".
     */
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
