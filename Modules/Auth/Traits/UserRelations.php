<?php

namespace Modules\Auth\Traits;

use App\Helpers\MediaHelper;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Address\Models\Address;
use Modules\Auth\Enums\AuthEnum;
use Modules\Cart\Models\Cart;
use Modules\Comment\Models\Comment;
use Modules\Markable\Entities\FavoriteModel;
use Modules\Order\Models\Order;
use Modules\Product\Models\Product;
use Modules\Role\Models\Role;

trait UserRelations
{
    public function avatar()
    {
        return MediaHelper::mediaRelationship($this, AuthEnum::AVATAR_COLLECTION_NAME);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function permissionsOnly(): BelongsToMany
    {
        return $this->roles();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likeByUsers()
    {
        return $this->belongsToMany(Comment::class, 'comment_like', 'user_id', 'comment_id')->withTimestamps();
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function favoriteProduct()
    {
        return $this->belongsToMany(Product::class, 'favorite_product', 'user_id', 'product_id');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function favorites()
    {
        return $this->hasMany(FavoriteModel::class);
    }
}
