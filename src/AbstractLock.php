<?php
/**
 * Part of the "charcoal-dev/semaphore" package.
 * @link https://github.com/charcoal-dev/semaphore
 */

declare(strict_types=1);

namespace Charcoal\Semaphore;

use Charcoal\Base\Traits\NoDumpTrait;
use Charcoal\Base\Traits\NotCloneableTrait;
use Charcoal\Base\Traits\NotSerializableTrait;

/**
 * Class AbstractLock
 * @package Charcoal\Semaphore
 */
abstract class AbstractLock
{
    protected ?float $previousTimestamp;
    protected bool $isLocked = false;
    protected bool $autoReleaseSet = false;

    use NotSerializableTrait;
    use NotCloneableTrait;
    use NoDumpTrait;

    /**
     * @param AbstractSemaphore $semaphore
     * @param string $resourceId
     * @param float|null $concurrentCheckEvery
     * @param int $concurrentTimeout
     */
    public function __construct(
        public readonly AbstractSemaphore $semaphore,
        public readonly string            $resourceId,
        public readonly ?float            $concurrentCheckEvery = null,
        public readonly int               $concurrentTimeout = 0
    )
    {
    }

    /**
     * @return void
     */
    abstract public function releaseLock(): void;

    /**
     * @return bool
     */
    public function isLocked(): bool
    {
        return $this->isLocked;
    }

    /**
     * @return void
     */
    public function setAutoRelease(): void
    {
        if ($this->autoReleaseSet) {
            return;

        }

        $resourceLock = $this;
        register_shutdown_function(function () use ($resourceLock) {
            $resourceLock->releaseLock();
        });

        $this->autoReleaseSet = true;
    }

    /**
     * @return float|null
     */
    public function previousTimestamp(): ?float
    {
        return $this->previousTimestamp;
    }

    /**
     * @param float $seconds
     * @return bool
     */
    public function checkElapsedTime(float $seconds): bool
    {
        if (!$this->previousTimestamp) {
            return true;
        }

        return ((microtime(true) - $this->previousTimestamp) >= $seconds);
    }
}
