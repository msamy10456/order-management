<?php

namespace Modules\Auth\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Socialite\Facades\Socialite;
use Modules\Auth\Enums\AuthEnum;
use Modules\Auth\Exceptions\SocialAuthException;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class SocialiteService
{
    /**
     * @throws FileCannotBeAdded
     * @throws FileIsTooBig
     * @throws FileDoesNotExist|SocialAuthException
     */
    public function getRedirect($requestProvider)
    {
        return Socialite::driver($requestProvider)->redirect();
    }
    public function handleProviderCallback(array $data, $type)
    {
        $requestProvider = $data['provider'];
        $accessToken = $data['access_token'];

        try {
            $user = Socialite::driver($requestProvider)->stateless()->userFromToken($accessToken);
        } catch (SocialAuthException $e) {
            SocialAuthException::invalidCredentials();
        }

        $email = $user->getEmail();

        if (! $email) {
            SocialAuthException::invalidEmail();
        }

        $existingUser = User::query()
            ->where('email', $user->getEmail())
            ->where('type', $type)
            ->first();

        if (! $existingUser) {
            $existingUser = User::create([
                'email' => $email,
                AuthEnum::VERIFIED_AT => now(),
                'name' => $user->getName() ?: explode('@', $email)[0],
                'status' => true,
                'password' => null,
                'type' => $type,
                'social_provider' => $requestProvider,
            ]);

            $existingUser = User::whereId($existingUser->id)->first();

            if ($user->getAvatar()) {
                $avatar = $existingUser
                    ->addMediaFromUrl($user->getAvatar())
                    ->toMediaCollection(AuthEnum::AVATAR_COLLECTION_NAME);

                $existingUser = User::whereId($existingUser->id)->first();

                $existingUser->setRelation('avatar', new Collection([$avatar]));
            }

            // if ($type == UserTypeEnum::PARENT) {
            //     ParentModel::factory(1)->create([
            //         'user_id' => $existingUser->id,
            //     ]);
            // } elseif ($type == UserTypeEnum::PATIENT) {
            //     NormalUser::factory(1)->create([
            //         'user_id' => $existingUser->id,
            //     ]);
            // }
        }

        $existingUser->loadMissing('avatar');

        LoginService::addBearerToken($existingUser);

        return $existingUser;
    }
}
