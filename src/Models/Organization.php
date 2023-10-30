<?php

namespace MakeIT\DiscreteApi\Organizations\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use MakeIT\Utils\Sorter;

/**
 * @method static create(array $array)
 */
class Organization extends Model
{
    use SoftDeletes;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'organizations';
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        Sorter::FIELD,
        'title',
        'description',
        'is_personal',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'pivot',
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'deleted_at' => 'datetime',
    ];
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    public function getIncrementing(): bool
    {
        return true;
    }

    public function getKeyType(): string
    {
        return 'string';
    }

    public function role(): ?string
    {
        if (isset($this->pivot)) {
            return config('discreteapiorganizations.roles')[$this->pivot->role];
        }
        return null;
    }

    public function scopePersonal(Builder $query, bool $is = true): void
    {
        $query->where('is_personal', $is);
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy(Sorter::FIELD, Sorter::ASCENDING);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organizations_members')->withPivot([
            '*',
        ]);
    }

    public function workspaces(): HasMany
    {
        return $this->hasMany(Workspace::class, 'organization_id');
    }

    public function slots(): HasOne
    {
        return $this->hasOne(OrganizationSlot::class, 'organization_id');
    }

}
