<?php

namespace Assertis\Util;

use Assertis\Util\Stubs\MemcacheStub;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DistributedMemcacheMutexTest extends TestCase
{
    /**
     * @var DistributedMemcacheMutex
     */
    private $mutex;
    /**
     * @var DistributedMemcacheMutex
     */
    private $serverlessMutex;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->markTestSkipped('This test is skipped because it is not compatible with PHP 8.0');

        parent::setUp();
        $memcache = new MemcacheStub();
        $this->mutex = new DistributedMemcacheMutex($memcache->withServersAdded());
        $this->serverlessMutex = new DistributedMemcacheMutex($memcache);
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
            $this->fail('DistributedMemcacheMutex::lock method should throw AlreadyLockedException.');
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
            $this->fail('DistributedMemcacheMutex::lock method should throw InvalidArgumentException.');
        } catch (InvalidArgumentException $exception) {
            $this->success();
        }
    }

    private function success()
    {
        $this->assertTrue(true);
    }
}
