<?php

/*
 * This file is part of the kuajing100/laravel-notification-wecom
 * (c) Kuajing100 <hadi@kuajing100.com>
 * This source file is subject to the MIT license that is bundled with this source code in the file LICENSE.
 */

namespace Kuajing100\WeCom;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Work\Message\Messenger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notification;
use Kuajing100\WeCom\Exceptions\CouldNotSendNotificationException;
use EasyWeChat\Kernel\Exceptions\RuntimeException;

class AppChannel
{
    /**
     * @param $notifiable
     * @param Notification $notification
     * @return mixed
     * @throws CouldNotSendNotificationException
     * @throws InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function send($notifiable, Notification $notification)
    {
        $messenger = $notification->toWeComApp($notifiable);
        if (!$messenger instanceof Messenger) {
            throw CouldNotSendNotificationException::invalidMessenger($messenger, Messenger::class);
        }
        $route = '';
        if ($notifiable instanceof Model) {
            $route = $notifiable->routeNotificationFor('WeCom');
        } elseif ($notifiable instanceof AnonymousNotifiable) {
            $route = $notifiable->routes[__CLASS__];
        }
        if ($route) {
            $messenger->toUser($route);
        }
        try {
            return $messenger->send();
        } catch (InvalidArgumentException $e) {
            throw $e;
        } catch (RuntimeException $e) {
            throw $e;
        }
    }
}
