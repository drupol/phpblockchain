<?php

declare(strict_types=1);

namespace drupol\phpblockchain\Hasher;

use drupol\phpblockchain\Block\BlockInterface;

/**
 * Class AbstractHasher.
 */
abstract class AbstractHasher extends \drupol\phpmerkle\Hasher\AbstractHasher implements HasherInterface
{
    /**
     * {@inheritdoc}
     */
    public function hashBlock(BlockInterface $block, bool $raw_output = true): string
    {
        return $this->hash($block->withHash()->serialize(), $raw_output);
    }
}
