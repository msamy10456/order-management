<?php

namespace Modules\Auth\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|VerifyToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VerifyToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VerifyToken query()
 *
 * @property int $id
 * @property string $handle
 * @property string $code
 * @property string $expires_at
 * @property int $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|VerifyToken whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VerifyToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VerifyToken whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VerifyToken whereHandle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VerifyToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VerifyToken whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VerifyToken whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class VerifyToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'handle',
        'code',
        'expires_at',
        'type',
    ];
}
