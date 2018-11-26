<?php

declare(strict_types=1);

namespace drupol\phpblockchain\Hasher;

use drupol\phpblockchain\Block\BlockInterface;

/**
 * Interface HasherInterface.
 */
interface HasherInterface extends \drupol\phpmerkle\Hasher\HasherInterface
{
    /**
     * @param \drupol\phpblockchain\Block\BlockInterface $block
     * @param bool $raw_output
     *
     * @return string
     */
    public function hashBlock(BlockInterface $block, bool $raw_output = true): string;
}
