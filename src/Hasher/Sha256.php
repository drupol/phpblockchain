<?php

declare(strict_types=1);

namespace drupol\phpblockchain\Hasher;

/**
 * Class Sha256.
 */
final class Sha256 extends AbstractHasher
{
    /**
     * @var \drupol\phpmerkle\Hasher\Sha256
     */
    private $hasher;

    /**
     * Sha256 constructor.
     */
    public function __construct()
    {
        $this->hasher = new \drupol\phpmerkle\Hasher\Sha256();
    }

    /**
     * {@inheritdoc}
     */
    public function hash(string $data, bool $raw_output = true): string
    {
        return $this->hasher->hash($data, $raw_output);
    }

    /**
     * {@inheritdoc}
     */
    public function unpack(string $data): string
    {
        return $this->hasher->unpack($data);
    }
}
