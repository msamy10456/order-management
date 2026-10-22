<?php

namespace App\Helpers;

use Modules\Auth\Enums\UserTypeEnum;

class GeneralHelper
{
    public static function adminMiddlewares(): array
    {
        return array_merge(self::getDefaultLoggedUserMiddlewares(), ['user_type_in:' . UserTypeEnum::ADMIN . '|' . UserTypeEnum::ADMIN_EMPLOYEE ]);
    }

    public static function langMiddleware(): string
    {
        return "localization";
    }

    public static function userMiddlewares(): array
    {
        return self::getDefaultLoggedUserMiddlewares(
            ['user_type_in:' . UserTypeEnum::USER ]
        );
    }

    public static function clientMiddlewares(): array
    {
        return self::getDefaultLoggedUserMiddlewares(
            ['user_type_in:' . UserTypeEnum::USER]
        );
    }

    public static function getDefaultLoggedUserMiddlewares(array $additionalMiddlewares = []): array
    {
        return [
            self::authMiddleware(),
            ...$additionalMiddlewares,
        ];
    }

    public static function vendorOrAdminMiddlewares(): array
    {
        return self::getDefaultLoggedUserMiddlewares(['user_type_in:' . UserTypeEnum::ADMIN]);
    }

    public static function authMiddleware(): string
    {
        return 'auth:sanctum';
    }

    public static function HtmlToText($text): string
    {
        $str = str_replace('&nbsp;', ' ', $text);
        $str = str_replace('\\r\\n', ' ', $str);
        $str = html_entity_decode($str, ENT_QUOTES | ENT_COMPAT, 'UTF-8');
        $str = html_entity_decode($str, ENT_HTML5, 'UTF-8');
        $str = html_entity_decode($str);
        $str = htmlspecialchars_decode($str);
        return strip_tags($str);
    }

    public static function getAllLang($model, $key)
    {
        if (request()->lang) {
            return $model->getTranslations($key);
        }
        return "";
    }
}
