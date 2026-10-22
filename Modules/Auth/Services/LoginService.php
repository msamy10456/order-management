<?php

namespace Modules\Auth\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\Actions\LogoutUser;
use Modules\Auth\Enums\AuthEnum;
use Modules\Auth\Enums\LoginTypeEnum;
use Modules\Auth\Exceptions\LoginException;
use Modules\Auth\Helpers\UserRelationHelper;
use Modules\Auth\Helpers\UserTypeHelper;
use Modules\Auth\Traits\VerifiableTrait;
use Throwable;

class LoginService
{
    use VerifiableTrait;

    public function __construct(private readonly LogoutUser $logoutUserAction, private readonly RefreshTokenService $refreshTokenService) {}

    public function Mobile(array $data)
    {
        return $this->login($data, LoginTypeEnum::MOBILE);
    }

    public function dashboard(array $data)
    {
        return $this->login($data, LoginTypeEnum::DASHBOARD);
    }

    private function login(array $data, $type)
    {
        $user = $this->findUser($data, $type);

        $this->validateUser($user, $data);

        return $this->processLogin($user, $data);
    }

    private function findUser(array $data, $type): ?User
    {
        return User::query()
            ->loginByType($data, $type)
            ->with('avatar')
            ->select([
                'id',
                'name',
                'email',
                'phone',
                'type',
                'password',
                'status',
                AuthEnum::VERIFIED_AT,
            ])
            ->first();
    }

    /**
     * @throws LoginException
     */
    private function validateUser(?User $user, array $data): void
    {
        if (
            ! $user ||
            (isset($data['password']) && ! self::validatePassword($user, $data['password']))
        ) {
            LoginException::wrongCredentials();
        }

        if (is_null($user->{AuthEnum::VERIFIED_AT})) {
            LoginException::notVerified();
        }

        if (! $user->status) {
            LoginException::blockedAccount();
        }
    }

    /**
     * @throws Throwable
     */
    private function processLogin(User $user, array $data)
    {
        return DB::transaction(function () use ($user, $data) {
            $this->loginUser($user, $data['fcm_token'] ?? null);

            return $user;
        });
    }

    private function loginUser(User $user, $fcmToken = null): void
    {
        if ($fcmToken) {
            $user->forceFill(['fcm_token' => $fcmToken])->save();
        }

        auth()->login($user, true);
        $user->forceFill(['last_login_at' => now()])->save();

        self::addBearerToken($user);
        UserRelationHelper::loadUserRelations($user);
    }

    public static function validatePassword(User $user, $requestPassword): bool
    {
        $user->makeVisible('password');

        $userPassword = $user->password;

        if (is_null($userPassword)) {
            return false;
        }

        return Hash::check($requestPassword, $userPassword);
    }

    public static function shouldAddBearerToken($type): bool
    {
        return ! app()->isProduction() || in_array($type, UserTypeHelper::mobileTypes());
    }

    public static function addBearerToken($user): void
    {
        if (self::shouldAddBearerToken($user->type)) {
            self::generateBearerToken($user);

            $refreshTokenService = app(RefreshTokenService::class);
            $user->refresh_token = $refreshTokenService->store($user->id);
        }
    }

    public static function generateBearerToken(&$user)
    {
        $expiresAt = now()->addMinutes(config('sanctum.expiration', 15) ?: 15);

        $token = $user->createToken(
            $user->name ?: 'Sample User',
            expiresAt: $expiresAt
        );
        $user->token = $token;
    }
}
