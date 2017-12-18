<?php
/**
 * Created by PhpStorm.
 * User: zvinger
 * Date: 18.12.17
 * Time: 13:43
 */

namespace Zvinger\GoogleOtp\components\google\handler;

use Zvinger\GoogleOtp\components\google\GoogleAuthenticatorComponent;
use Zvinger\GoogleOtp\components\google\lib\GoogleAuthDataHandlerInterface;

class EmptyUserHandler implements GoogleAuthDataHandlerInterface
{
    /**
     * @var GoogleAuthenticatorComponent
     */
    private static $_component;

    public function __construct(GoogleAuthenticatorComponent $component)
    {
        if (empty(static::$_component)) {
            static::$_component = $component;
        }
    }

    /**
     * @param $user_id
     * @return string|null
     */
    public static function getUserSecret($user_id)
    {
        return (static::$_component->methodGetUserSecret)($user_id);
    }

    /**
     * @param $user_id
     * @param $secret
     * @return bool
     */
    public static function saveUserSecret($user_id, $secret): bool
    {
        return (static::$_component->methodSaveUserSecret)($user_id, $secret);
    }

    /**
     * @param $user_id
     * @return string|null
     */
    public static function getUserName($user_id)
    {
        return (static::$_component->methodGetUserName)($user_id);
    }
}