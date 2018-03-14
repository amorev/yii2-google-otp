<?php
/**
 * Created by PhpStorm.
 * User: zvinger
 * Date: 15.12.17
 * Time: 17:37
 */

namespace Zvinger\GoogleOtp\components\google;

use Zvinger\GoogleOtp\components\google\handler\UserGoogleAuthHandler;
use Zvinger\GoogleOtp\components\google\lib\GoogleAuthenticatorLib;
use Zvinger\GoogleOtp\components\google\models\CreateUserAuthResult;
use Zvinger\GoogleOtp\exceptions\WrongOtpConfirmCodeException;

class GoogleAuthenticatorComponent
{
    public $length = 16;

    public $userHandlerClassName;

    public $methodGetUserSecret;

    public $methodSaveUserSecret;

    public $methodGetUserName;

    public $dataCryptKey = 'cryptKeyGoogleAuth';

    private $_authenticator;

    /**
     * @var array
     */
    private $_handler_by_user = [];

    /**
     * @param $user_id
     * @param null $verifyCode
     * @return CreateUserAuthResult
     * @throws WrongOtpConfirmCodeException
     */
    public function createUserAuth($user_id, $verifyCode = NULL)
    {
        $handler = $this->getUserGoogleAuthHandler($user_id);
        $secret = $handler->getCurrentSecret();
        if ($secret && !$this->verifyCode($secret, $verifyCode)) {
            throw new WrongOtpConfirmCodeException();
        }
        $newSecret = $this->createSecret();
        $qr = $this->getQRCodeGoogleUrl("SVCPool." . $handler->getUsername(), $newSecret);
        $model = new CreateUserAuthResult();
        $model->secret = $newSecret;
        $model->qrUrl = $qr;

        return $model;
    }

    /**
     * @param $user_id
     * @param $code
     * @return bool
     */
    public function validateUserCode($user_id, $code)
    {
        $handler = $this->getUserGoogleAuthHandler($user_id);
        $secret = $handler->getCurrentSecret();

        return $this->verifyCode($secret, $code);
    }

    /**
     * @return string
     */
    public function createSecret()
    {
        $authenticator = $this->getAuthenticator();

        return $authenticator->createSecret($this->length);
    }

    /**
     * @param $user_id
     * @param $secret
     * @return bool
     */
    public function saveUserSecret($user_id, $secret)
    {
        return $this->getUserGoogleAuthHandler($user_id)->saveSecret($secret);
    }

    /**
     * @param $name
     * @param $secret
     * @return string
     */
    public function getQRCodeGoogleUrl($name, $secret)
    {
        return $this->getAuthenticator()->getQRCodeGoogleUrl($name, $secret);
    }

    /**
     * @param $secret
     * @param $code
     * @return bool
     */
    public function verifyCode($secret, $code)
    {
        return $this->getAuthenticator()->verifyCode($secret, $code);
    }

    /**
     * @param $user_id
     * @return UserGoogleAuthHandler
     */
    public function getUserGoogleAuthHandler($user_id)
    {
        if (empty($this->_handler_by_user[$user_id])) {
            $userGoogleAuthHandler = new UserGoogleAuthHandler($this);
            $userGoogleAuthHandler->setUserId($user_id);
            $this->_handler_by_user[$user_id] = $userGoogleAuthHandler;
        }

        return $this->_handler_by_user[$user_id];
    }

    /**
     * @param $user_id
     * @return bool
     */
    public function getUserGoogleAuthStatus($user_id)
    {
        return $this->getUserGoogleAuthHandler($user_id)->getUserAuthStatus();
    }

    public function getUserOtpCheckHash($user_id)
    {
        $secret = $this->getUserGoogleAuthHandler($user_id)->getCurrentSecret();
        $string = md5($this->dataCryptKey . $secret . $user_id);

        return $string;
    }

    public function validateUserHash($user_id, $hash)
    {
        return $hash === $this->getUserOtpCheckHash($user_id);
    }

    public function getCode($secret, $timeSlice = null)
    {
        return $this->getAuthenticator()->getCode($secret, $timeSlice);
    }

    /**
     * @return GoogleAuthenticatorLib
     */
    private function getAuthenticator(): GoogleAuthenticatorLib
    {
        if (empty($this->_authenticator)) {
            $this->_authenticator = new GoogleAuthenticatorLib();
        }

        return $this->_authenticator;
    }
}