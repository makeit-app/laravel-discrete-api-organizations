<?php

namespace MakeIT\DiscreteApi\Organizations\Observers;

use MakeIT\DiscreteApi\Organizations\Models\OrganizationSlot as Model;
use Illuminate\Support\Str;

class OrganizationSlotObserver
{
    public function creating(Model $model): void
    {
        if (empty($model->{$model->getKeyName()})) {
            $model->{$model->getKeyName()} = Str::uuid()->toString();
        }
    }
}
