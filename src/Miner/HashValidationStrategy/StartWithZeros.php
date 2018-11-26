<?php

declare(strict_types=1);

namespace drupol\phpblockchain\Miner\HashValidationStrategy;

use InvalidArgumentException;

/**
 * Class StartWithZeros.
 */
final class StartWithZeros implements HashValidationStrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function hashMatch(string $hash, int $difficulty = 1): bool
    {
        if (1 > $difficulty) {
            throw new InvalidArgumentException('Difficulty must be equal or greater to 1.');
        }

        if (mb_strlen($hash) <= $difficulty) {
            throw new InvalidArgumentException('Difficulty must be smaller than the hash length.');
        }

        return 0 === mb_strpos(
            $hash,
            str_pad('', $difficulty, '0')
        );
    }
}
