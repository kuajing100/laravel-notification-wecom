<?php

/*
 * This file is part of the kuajing100/laravel-notification-wecom
 * (c) Kuajing100 <hadi@kuajing100.com>
 * This source file is subject to the MIT license that is bundled with this source code in the file LICENSE.
 */

namespace Kuajing100\WeCom;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Work\GroupRobot\Messenger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use Kuajing100\WeCom\Exceptions\CouldNotSendNotificationException;

class GroupChannel
{
    public function send($notifiable, Notification $notification)
    {
        $messenger = $notification->toWeComGroup($notifiable);
        if (!$messenger instanceof Messenger) {
            throw CouldNotSendNotificationException::invalidMessenger($messenger, Messenger::class);
        }
        $route = '';
        if ($notifiable instanceof Model) {
            $route = $notifiable->routeNotificationFor('WeCom');
        } elseif ($notifiable instanceof AnonymousNotifiable) {
            $route = $notifiable->routes[__CLASS__];
        }

        if ($route == '@all') {
            $messenger->message->mention('@all');
        } elseif (Str::startsWith($route, ['@'])) {
            $messenger->message->mention(str_replace('@', '', $route));
        } elseif ($route) {
            $messenger->message->mentionByMobile($route);
        }

        try {
            return $messenger->send();
        } catch (InvalidArgumentException $e) {
            throw $e;
        } catch (InvalidConfigException $e) {
            throw $e;
        } catch (RuntimeException $e) {
            throw $e;
        }
    }
}
