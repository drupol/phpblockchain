<?php

declare(strict_types=1);

namespace tests\drupol\phpblockchain\Hasher;

use drupol\phpblockchain\Hasher\AbstractHasher;

/**
 * Class Md5.
 */
final class Md5 extends AbstractHasher
{
    /**
     * {@inheritdoc}
     */
    public function hash(string $data, bool $raw_output = true): string
    {
        return $this->doHash('md5', $data, $raw_output);
    }

    /**
     * {@inheritdoc}
     */
    public function unpack(string $hash): string
    {
        return implode('', unpack('H*', $hash));
    }
}
