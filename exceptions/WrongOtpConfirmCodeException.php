<?php
/**
 * Created by PhpStorm.
 * User: zvinger
 * Date: 18.12.17
 * Time: 13:40
 */

namespace Zvinger\GoogleOtp\exceptions;

class WrongOtpConfirmCodeException extends BaseGoogleOtpException
{
    public function __construct($message = "", $code = 0, $previous = NULL)
    {
        $message = 'Wrong confirm code';
        parent::__construct($message, $code, $previous);
    }

}