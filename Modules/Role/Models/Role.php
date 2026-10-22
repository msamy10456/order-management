<?php

namespace Modules\Role\Models;

use App\Traits\PaginationTrait;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Role\Models\Builders\RoleBuilder;
use Modules\Role\Traits\RoleRelations;
use Spatie\Permission\Models\Role as ModelsRole;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Role\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 *
 * @method static RoleBuilder|Role newModelQuery()
 * @method static RoleBuilder|Role newQuery()
 * @method static RoleBuilder|Role paginatedCollection()
 * @method static RoleBuilder|Role query()
 * @method static RoleBuilder|Role searchByForeignKey(string $foreignKeyColumn, ?string $value = null)
 * @method static RoleBuilder|Role searchByRelation(string $relationName, array $columns = [], array $translatedKeys = [], bool $orWhere = false)
 * @method static RoleBuilder|Role searchable(array $columns = [], array $translatedKeys = [], bool $orWhere = false, string $handleKeyName = 'handle')
 * @method static RoleBuilder|Role whereAccessible(bool $inInstitution = false)
 *
 * @mixin \Eloquent
 */
class Role extends ModelsRole
{
    use HasFactory, PaginationTrait, RoleRelations, Searchable;

    protected $fillable = [
        'name',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function newEloquentBuilder($query): RoleBuilder
    {
        return new RoleBuilder($query);
    }
}
