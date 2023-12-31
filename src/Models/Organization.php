<?php

namespace MakeIT\DiscreteApi\Organizations\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use MakeIT\Utils\Sorter;

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
        'workspace_slots',
        'member_slots',
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
    protected $appends = [
        'role',
    ];

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

    public function role(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (isset($this->pivot)) {
                return config('discreteapiorganizations.roles')[$this->pivot->role];
            } else {
                if (request()->user() instanceof User || request()->user() instanceof Authenticatable) {
                    $tmp = request()->user()->organizations()->select(['id', 'organization_id', 'user_id'])->where('organization_id', $this->id)->first();
                    if (!is_null($tmp)) {
                        return $tmp->role;
                    }
                } elseif ($this->is_personal) {
                    // patch if created for now via observer (?no pivot data at this time)
                    return config('discreteapiorganizations.roles')[1];
                }
            }
            return null;
        });
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
        return $this->belongsToMany(User::class, 'pivot_organizations_users')->withPivot('*');
    }

    public function workspaces(): HasMany
    {
        return $this->hasMany(Workspace::class, 'organization_id');
    }

}
