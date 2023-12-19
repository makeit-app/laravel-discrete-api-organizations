<?php

namespace MakeIT\DiscreteApi\Organizations\Observers;

use Illuminate\Support\Str;
use MakeIT\DiscreteApi\Organizations\Models\Organization as Model;
use MakeIT\Utils\Sorter;

class OrganizationObserver
{
    public function creating(Model $model): void
    {
        if (empty($model->{$model->getKeyName()})) {
            $model->{$model->getKeyName()} = Str::uuid()->toString();
        }
        $model->workspace_slots = config('discreteapiorganizations.limit.workspaces');
        $model->member_slots = config('discreteapiorganizations.limit.members');
    }

    public function created(Model $model): void
    {
        $model->workspaces()->create([
            'title' => __('Default Workspace'),
            'is_default' => true,
            Sorter::FIELD => 1,
        ]);
    }
}
