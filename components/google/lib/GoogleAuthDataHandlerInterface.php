<?php
/**
 * Created by PhpStorm.
 * User: zvinger
 * Date: 18.12.17
 * Time: 13:27
 */

namespace Zvinger\GoogleOtp\components\google\lib;

interface GoogleAuthDataHandlerInterface
{
    /**
     * @param $user_id
     * @return string|null
     */
    public static function getUserSecret($user_id);

    /**
     * @param $user_id
     * @param $secret
     * @return bool
     */
    public static function saveUserSecret($user_id, $secret): bool;

    /**
     * @param $user_id
     * @return string|null
     */
    public static function getUserName($user_id);
}