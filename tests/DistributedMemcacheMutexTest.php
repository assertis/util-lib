<?php

namespace Assertis\Util;

use PHPUnit_Framework_TestCase;

class DistributedMemcacheMutexTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var DistributedMemcacheMutex
     */
    private $mutex;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->mutex = new DistributedMemcacheMutex(new MemcacheStub());
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
            $this->fail('DistributedMemcacheMutex::lock method should throw AlreadyLockedException');
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

    private function success()
    {
        $this->assertTrue(true);
    }
}
