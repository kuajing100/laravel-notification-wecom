<?php

/*
 * This file is part of the kuajing100/laravel-notification-wecom
 * (c) Kuajing100 <hadi@kuajing100.com>
 * This source file is subject to the MIT license that is bundled with this source code in the file LICENSE.
 */

namespace Tests;

use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Work\Message\Client;
use EasyWeChat\Work\Message\Messenger;
use Kuajing100\WeCom\AppChannel;
use Kuajing100\WeCom\Exceptions\CouldNotSendNotificationException;
use Mockery;
use PHPUnit\Framework\TestCase;

class AppChannelTest extends TestCase
{
    public function testInstantiation()
    {
        $channel = new AppChannel();
        $this->assertInstanceOf(AppChannel::class, $channel);
    }

    public function testSendInvalidMessage()
    {
        $this->expectException(CouldNotSendNotificationException::class);
        $this->expectExceptionCode(1);
        $channel = new AppChannel();
        $channel->send(new TestNotifiable(), new TestNotification());
    }

    public function testSendMessageFailed()
    {
        $client = Mockery::mock(Client::class)->shouldReceive('send')->once()->andThrow(new RuntimeException())->getMock();
        $this->expectException(RuntimeException::class);
        $channel = new AppChannel();
        $channel->send(new TestNotifiable(), new TestNotification(new Messenger($client)));
    }

    public function testRouteToUser()
    {
        $user = 'user';
        $agent_id = 13;
        $client = Mockery::mock(Client::class)->shouldReceive('send')->once()->andReturn(true)->getMock();
        $messenger = new Messenger($client);
        $messenger->message('test');
        $messenger->ofAgent($agent_id);
        $channel = new AppChannel();
        $this->assertTrue($channel->send(new TestNotifiable($user), new TestNotification($messenger)));
    }
}
