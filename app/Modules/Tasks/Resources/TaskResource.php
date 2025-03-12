<?php

namespace App\Modules\Tasks\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Modules\Tasks\Models\Task;

class TaskResource extends JsonResource
{
    public function toArray( Request $request): array
    {                
        $result = [
            'id' => $this->id,
            'name' => $this->name,
            'completed' => $this->completed,
        ];

        return $result;
    }
}
