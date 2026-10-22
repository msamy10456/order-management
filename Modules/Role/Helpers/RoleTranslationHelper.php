<?php

namespace Modules\Role\Helpers;

class RoleTranslationHelper
{
    public static function en(): array
    {
        return [
            'permission' => 'Permission',
            'role' => 'Role',
            'permissions' => 'Permissions',
            'roles' => 'Roles',
            'user_does_not_have_permission' => 'You do not have permission to perform this action.',
            'subscription_type' => 'Subscription Type',
            'have_no_permission' => 'You do not have permission to access this resource',
        ];
    }

    public static function ar(): array
    {
        return [
            'permission' => 'الأذن',
            'role' => 'الدور',
            'permissions' => 'الأذونات',
            'roles' => 'الأدوار',
            'user_does_not_have_permission' => 'ليس لديك إذن لتنفيذ هذا الإجراء.',
            'subscription_type' => 'نوع الاشتراك',
            'have_no_permission' => 'ليس لديك الصلاحيه للوصول لهذا العنوان',
        ];
    }

    public static function fr(): array
    {
        return [
            'permission' => 'Permission',
            'role' => 'Rôle',
            'permissions' => 'Permissions',
            'roles' => 'Rôles',
            'user_does_not_have_permission' => 'Vous n avez pas de permission pour effectuer cette action.',
            'subscription_type' => 'Type d abonnement',
            'have_no_permission' => 'Vous n avez pas de permission pour accéder à cette ressource',
        ];
    }
}
