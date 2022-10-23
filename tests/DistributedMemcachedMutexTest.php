<?php

namespace Assertis\Util;

use Assertis\Util\Stubs\MemcachedStub;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DistributedMemcachedMutexTest extends TestCase
{
    /**
     * @var DistributedMemcachedMutex
     */
    private $mutex;
    /**
     * @var DistributedMemcachedMutex
     */
    private $serverlessMutex;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->markTestSkipped('This test is skipped because it is not compatible with PHP 8.0');

        parent::setUp();
        $memcached = new MemcachedStub();
        $memcachedWithoutServers = new MemcachedStub();
        $this->mutex = new DistributedMemcachedMutex($memcached->withServersAdded());
        $this->serverlessMutex = new DistributedMemcachedMutex($memcachedWithoutServers);
    }

    /**
     * @test
     */
    public function lockWhenMemcacheKeyDoesNotExist()
    {
        try {
            $this->mutex->lock('some_key');
            $this->success();
        } catch (AlreadyLockedException $exception) {
            $this->fail($exception->getMessage());
        }
    }

    /**
     * @test
     */
    public function lockThrowsExceptionWhenMemcacheKeyExists()
    {
        try {
            $this->mutex->lock('some_key');
            $this->mutex->lock('some_key');
            $this->fail('DistributedMemcachedMutex::lock method should throw AlreadyLockedException.');
        } catch (AlreadyLockedException $exception) {
            $this->success();
        }
    }

    /**
     * @test
     */
    public function unlockWhenMemcacheKeyExists()
    {
        try {
            $this->mutex->lock('some_key');
            $this->mutex->unlock('some_key');
            $this->mutex->lock('some_key');
            $this->success();
        } catch (AlreadyLockedException $exception) {
            $this->fail($exception->getMessage(). ' It should not exists because it was unlocked.');
        }
    }

    /**
     * @test
     */
    public function unlockWhenMemcacheKeyDoesNotExist()
    {
        try {
            $this->mutex->unlock('some_key');
            $this->mutex->lock('some_key');
            $this->success();
        } catch (AlreadyLockedException $exception) {
            $this->fail($exception->getMessage(). ' It should not exists because it was not present.');
        }
    }

    /**
     * @test
     */
    public function lockWhenNoServers()
    {
        try {
            $this->serverlessMutex->lock('some_key');
            $this->fail('DistributedMemcachedMutex::lock method should throw InvalidArgumentException.');
        } catch (InvalidArgumentException $exception) {
            $this->success();
        }
    }

    private function success()
    {
        $this->assertTrue(true);
    }
}
