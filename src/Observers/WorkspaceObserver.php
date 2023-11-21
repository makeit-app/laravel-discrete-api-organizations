<?php

namespace MakeIT\DiscreteApi\Organizations\Observers;

use MakeIT\DiscreteApi\Organizations\Models\Workspace as Model;
use Illuminate\Support\Str;

class WorkspaceObserver
{
    public function creating(Model $model): void
    {
        if (empty($model->{$model->getKeyName()})) {
            $model->{$model->getKeyName()} = Str::uuid()->toString();
        }
    }
}
