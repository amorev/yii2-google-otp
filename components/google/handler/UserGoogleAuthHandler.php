<?php
/**
 * Created by PhpStorm.
 * User: zvinger
 * Date: 15.12.17
 * Time: 21:34
 */

namespace Zvinger\GoogleOtp\components\google\handler;

use Zvinger\GoogleOtp\components\google\GoogleAuthenticatorComponent;
use Zvinger\GoogleOtp\components\google\lib\GoogleAuthDataHandlerInterface;

class UserGoogleAuthHandler
{
    /**
     * @var int
     */
    private $_user_id;

    /**
     * @var GoogleAuthDataHandlerInterface
     */
    private $_handler;

    /**
     * UserGoogleAuthHandler constructor.
     * @param GoogleAuthenticatorComponent $component
     */
    public function __construct(GoogleAuthenticatorComponent $component)
    {
        $handlerClassName = $component->userHandlerClassName;
        if ($handlerClassName == NULL) {
            $this->_handler = new EmptyUserHandler($component);
        } else {
            $this->_handler = new $handlerClassName();
        }
    }

    /**
     * @return null|string
     */
    public function getCurrentSecret()
    {
        return $this->_handler->getUserSecret($this->_user_id);
    }

    /**
     * @return bool
     */
    public function getUserAuthStatus()
    {
        return !empty($this->getCurrentSecret());
    }

    public function getUsername()
    {
        return $this->_handler->getUserName($this->_user_id);
    }

    /**
     * @param $secret
     * @return bool
     */
    public function saveSecret($secret)
    {
        return $this->_handler->saveUserSecret($this->_user_id, $secret);
    }

    /**
     * @param int $user_id
     * @return UserGoogleAuthHandler
     */
    public function setUserId($user_id)
    {
        $this->_user_id = $user_id;

        return $this;
    }
}