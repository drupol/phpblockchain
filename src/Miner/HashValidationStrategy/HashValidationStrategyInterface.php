<?php

declare(strict_types=1);

namespace drupol\phpblockchain\Miner\HashValidationStrategy;

/**
 * Interface HashValidationStrategyInterface.
 */
interface HashValidationStrategyInterface
{
    /**
     * @param string $hash
     * @param int $difficulty
     *
     * @return bool
     */
    public function hashMatch(string $hash, int $difficulty): bool;
}
