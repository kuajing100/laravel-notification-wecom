<?php

/*
 * This file is part of the kuajing100/laravel-notification-wecom
 * (c) Kuajing100 <hadi@kuajing100.com>
 * This source file is subject to the MIT license that is bundled with this source code in the file LICENSE.
 */

namespace Tests;

use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Work\GroupRobot\Client;
use EasyWeChat\Work\GroupRobot\Messages\Text;
use EasyWeChat\Work\GroupRobot\Messenger;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Kuajing100\WeCom\Exceptions\CouldNotSendNotificationException;
use Kuajing100\WeCom\GroupChannel;
use PHPUnit\Framework\TestCase;

class GroupChannelTest extends TestCase
{
    public function testInstantiation()
    {
        $channel = new GroupChannel();
        $this->assertInstanceOf(GroupChannel::class, $channel);
    }

    public function testSendInvalidMessage()
    {
        $this->expectException(CouldNotSendNotificationException::class);
        $this->expectExceptionCode(1);
        $channel = new GroupChannel();
        $channel->send(new TestNotifiable(), new TestNotification());
    }

    public function testSendMessageFailed()
    {
        $client = \Mockery::mock(Client::class)->shouldReceive('send')->once()->andThrow(new RuntimeException())->getMock();
        $this->expectException(RuntimeException::class);
        $channel = new GroupChannel();
        $messenger = new Messenger($client);
        $messenger->message('');
        $channel->send(new TestNotifiable(), new TestNotification($messenger));
    }

    public function testRouteToUserId()
    {
        $client = \Mockery::mock(Client::class)->shouldReceive('send')->once()->andReturn(true)->getMock();
        $groupKey = 'test';
        $userId = 'user';
        $text = new Text('hello');
        $messenger = new Messenger($client);
        $messenger->message($text);
        $messenger->toGroup($groupKey);
        $channel = new GroupChannel();
        $this->assertTrue($channel->send(new TestNotifiable("@{$userId}"), new TestNotification($messenger)));
    }

    public function testRouteToUserPhone()
    {
        $client = \Mockery::mock(Client::class)->shouldReceive('send')->once()->andReturn(true)->getMock();
        $groupKey = 'test';
        $phone = 'user';
        $text = new Text('hello');
        $messenger = new Messenger($client);
        $messenger->message($text);
        $messenger->toGroup($groupKey);
        $channel = new GroupChannel();
        $this->assertTrue($channel->send(new TestNotifiable($phone), new TestNotification($messenger)));
    }
}

class TestNotifiable
{
    use Notifiable;

    protected $route;

    public function __construct($route = null)
    {
        $this->route = $route;
    }

    public function routeNotificationForWeCom()
    {
        return $this->route;
    }
}

class TestNotification extends Notification
{
    protected $messenger;

    public function __construct($messenger = null)
    {
        $this->messenger = $messenger;
    }

    public function toWeComGroup($notifiable)
    {
        return $this->messenger;
    }

    public function toWeComApp($notifiable)
    {
        return $this->messenger;
    }
}
