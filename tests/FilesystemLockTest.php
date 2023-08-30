<?php
/*
 * This file is a part of "charcoal-dev/semaphore" package.
 * https://github.com/charcoal-dev/semaphore
 *
 * Copyright (c) Furqan A. Siddiqui <hello@furqansiddiqui.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code or visit following link:
 * https://github.com/charcoal-dev/semaphore/blob/main/LICENSE
 */

declare(strict_types=1);

namespace Charcoal\Tests\Semaphore\Filesystem;

use Charcoal\Filesystem\Directory;
use Charcoal\Semaphore\AbstractSemaphore;
use Charcoal\Semaphore\Exception\SemaphoreLockError;
use Charcoal\Semaphore\FilesystemSemaphore;
use PHPUnit\Framework\TestCase;

/**
 * Class FilesystemLockTest
 * @package Charcoal\Tests\Semaphore\Filesystem
 */
class FilesystemLockTest extends TestCase
{
    /**
     * @return void
     * @throws \Charcoal\Filesystem\Exception\FilesystemException
     * @throws \Charcoal\Semaphore\Exception\SemaphoreException
     * @throws \Charcoal\Semaphore\Exception\SemaphoreLockException
     */
    public function testBasicLock(): void
    {
        $resourceId = "basic_resource_1";
        $semaphore = new FilesystemSemaphore(new Directory(__DIR__ . DIRECTORY_SEPARATOR . "temp"));

        $lock1 = $semaphore->obtainLock($resourceId, concurrentCheckEvery: null);
        $this->assertTrue($lock1->isLocked(), "Lock obtained successfully");

        // Send concurrent request:
        $this->expectExceptionCode(SemaphoreLockError::CONCURRENT_REQUEST_BLOCKED->value);
        /** @noinspection PhpUnusedLocalVariableInspection */
        $lock2 = $semaphore->obtainLock($resourceId);
    }

    /**
     * @return void
     * @throws \Charcoal\Filesystem\Exception\FilesystemException
     * @throws \Charcoal\Semaphore\Exception\SemaphoreException
     * @throws \Charcoal\Semaphore\Exception\SemaphoreLockException
     */
    public function testConcurrencyTimeout(): void
    {
        $resourceId = "some_resource_2";
        $semaphore = new FilesystemSemaphore(new Directory(__DIR__ . DIRECTORY_SEPARATOR . "temp"));

        $lock1 = $semaphore->obtainLock($resourceId, concurrentCheckEvery: null);
        $this->assertTrue($lock1->isLocked(), "Lock obtained successfully");

        // This will hang for up to 3 seconds...
        // Check every 0.5 second, maximum of 3 seconds
        $this->expectExceptionCode(SemaphoreLockError::CONCURRENT_REQUEST_TIMEOUT->value);
        /** @noinspection PhpUnusedLocalVariableInspection */
        $lock2 = $semaphore->obtainLock($resourceId, concurrentCheckEvery: 0.5, concurrentTimeout: 3);
    }

    /**
     * @return void
     * @throws \Charcoal\Filesystem\Exception\FilesystemException
     * @throws \Charcoal\Semaphore\Exception\SemaphoreException
     * @throws \Charcoal\Semaphore\Exception\SemaphoreLockException
     * @throws \Throwable
     */
    public function testCheckLockReleasedDuringConcurrency(): void
    {
        $resourceId = "some_resource_3";
        $semaphore = new FilesystemSemaphore(new Directory(__DIR__ . DIRECTORY_SEPARATOR . "temp"));

        $fiber = new \Fiber(function (AbstractSemaphore $semaphore, string $resourceId) {
            $lock1 = $semaphore->obtainLock($resourceId, concurrentCheckEvery: null);
            $this->assertTrue($lock1->isLocked(), "Lock obtained successfully");

            sleep(3);
            $lock1->releaseLock();

            return $lock1;
        });

        $fiber->start($semaphore, $resourceId);

        // This will hang for up to 3 seconds... (as soon as fiber releases lock)
        // Check every 0.25 second,
        $lock2 = $semaphore->obtainLock($resourceId, concurrentCheckEvery: 0.25);
        $this->assertTrue($lock2->isLocked());

        /** @var \Charcoal\Semaphore\AbstractLock $lock1 */
        $lock1 = $fiber->getReturn();
        $this->assertFalse($lock1->isLocked());
    }
}
