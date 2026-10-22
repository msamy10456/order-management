<?php

namespace Modules\Auth\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Address\Models\Address;
use Modules\Auth\Enums\AuthEnum;
use Modules\Auth\Enums\UserTypeEnum;
use Modules\Auth\Helpers\UserTypeHelper;
use Illuminate\Support\Str;

class AuthDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userTypes = UserTypeEnum::availableTypes();
        $emailTypes = UserTypeEnum::availableTypes();
        $phoneCounter = 0;

        foreach ($userTypes as $type) {
            $alphaType = UserTypeEnum::alphaTypes()[$type];
            $handleKey = in_array($type, $emailTypes) ? 'email' : 'phone';
            $handleValue = in_array($type, $emailTypes) ? "$alphaType@admin.com" : "+20112345678$phoneCounter";

            $user = User::create([
                'name' => $alphaType,
                $handleKey => $handleValue,
                AuthEnum::VERIFIED_AT => now(),
                'status' => true,
                'password' => $alphaType,
                'type' => $type,
                'phone' => "+20112345678$phoneCounter"
            ]);

            if ($handleKey == 'phone') {
                $phoneCounter++;
            }

            if ($type == UserTypeEnum::ADMIN) {
                $user->update([
                    'email' => 'admin@admin.com',
                    'name' => 'admin',
                    'password' => 'admin',

                ]);
            }
            $user->assignRole('admin');
            if ($type == UserTypeEnum::ADMIN_EMPLOYEE) {
                $user->assignRole('admin');
            }
        }
    }
}
