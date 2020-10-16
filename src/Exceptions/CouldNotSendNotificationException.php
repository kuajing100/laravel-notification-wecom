<?php

/*
 * This file is part of the kuajing100/laravel-notification-wecom
 * (c) Kuajing100 <hadi@kuajing100.com>
 * This source file is subject to the MIT license that is bundled with this source code in the file LICENSE.
 */

namespace Kuajing100\WeCom\Exceptions;

use Exception;

class CouldNotSendNotificationException extends Exception
{
    public static function invalidMessenger($messenger, $class)
    {
        $type = is_object($messenger) ? get_class($messenger) : gettype($messenger);
        return new static('The message should be an instance of ' . $class . ". Given `{$type}` is invalid.", 1);
    }
}
