<?php

namespace App\Models\Builders;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Modules\Auth\Enums\UserTypeEnum;
use Modules\Auth\Helpers\UserTypeHelper;
use Modules\Auth\Traits\VerificationBuilderTrait;

class UserBuilder extends Builder
{
    use VerificationBuilderTrait;

    public function selectAllDetails()
    {
        $this->select(
            [
                'id',
                'name',
                'phone',
                'avatar',
                'type',
                'wallet',
            ]
        );
    }
    public function basicInformation()
    {
        $this->select([
            'id',
            'name',
            'email',
            'phone',
            'wallet',
        ]);
    }
    public function withType($type)
    {
        $this->with($type);
    }

    public function loginByType(array $data, bool $inMobile)
    {
        if ($inMobile) {
            return $this
                ->whereNotNull('email')
                ->where('email', $data['email'])
                ->whereIn('type', UserTypeHelper::mobileTypes());
        }

        return $this
            ->whereNotNull('email')
            ->where('email', $data['email'])
            ->whereIn('type', UserTypeHelper::nonMobileTypes());
    }

    public function withMinimalSelectedColumns(array $excludeColumns = [], array $additionalColumns = []): UserBuilder
    {
        $columns = array_diff([
            'users.id',
            'users.name',
        ], array_map(fn($column) => "users.$column", $excludeColumns));

        return $this->select([...$columns, ...$additionalColumns]);
    }

    public function withConditionalAvatar(bool $withAvatar = true)
    {
        return $this->when($withAvatar, fn(self $q) => $q->with('avatar'));
    }

    public function withMinimalDetails(bool $withAvatar = true, array $excludeColumns = [], array $additionalColumns = [])
    {
        return $this
            ->withMinimalSelectedColumns($excludeColumns, $additionalColumns)
            ->withConditionalAvatar($withAvatar);
    }

    public function whereIsAdmin(): UserBuilder
    {
        return $this->where('type', UserTypeEnum::ADMIN);
    }

    public function whereActive(): UserBuilder
    {
        return $this->where('status', true);
    }
    public function whereReatched($type): UserBuilder
    {
        return $this->where($type, true);
    }

    public function whereNotMe(): UserBuilder
    {
        return $this->where('id', '!=', auth('sanctum')->user()?->id);
    }

  
}
