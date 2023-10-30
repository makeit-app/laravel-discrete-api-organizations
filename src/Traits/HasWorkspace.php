<?php

namespace MakeIT\DiscreteApi\Organizations\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MakeIT\DiscreteApi\Organizations\Models\Workspace;

/**
 * @method belongsTo(string $class, string $string)
 */
trait HasWorkspace
{
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class, 'workspace_id');
    }
}
