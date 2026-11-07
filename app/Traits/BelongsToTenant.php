<?php

namespace App\Traits;

use App\Models\Tenant;
use App\Support\Tenancy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::creating(function (Model $model): void {
            if (empty($model->tenant_id) && Tenancy::id()) {
                $model->tenant_id = Tenancy::id();
            }
        });

        static::addGlobalScope('tenant', function (Builder $builder): void {
            if (Tenancy::check()) {
                $builder->where($builder->getModel()->getTable().'.tenant_id', Tenancy::id());
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
