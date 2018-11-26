<?php

declare(strict_types=1);

namespace drupol\phpblockchain\Blockchain;

use drupol\phpblockchain\Block\BlockInterface;
use Exception;

/**
 * Interface BlockchainInterface.
 */
interface BlockchainInterface
{
    /**
     * Adds a block to the chain if it is valid.
     *
     * @param \drupol\phpblockchain\Block\BlockInterface $block
     *   The new block to add
     *
     * @throws Exception
     *
     * @return \drupol\phpblockchain\Blockchain\BlockchainInterface|null
     */
    public function addBlock(BlockInterface $block): ?BlockchainInterface;

    /**
     * @param string $hash
     *
     * @return \drupol\phpblockchain\Block\BlockInterface|null
     */
    public function getBlock(string $hash): ?BlockInterface;

    /**
     * @param \drupol\phpblockchain\Block\BlockInterface $block
     *
     * @return int|null
     */
    public function getBlockIndex(BlockInterface $block): ?int;

    /**
     * Get the chain.
     *
     * @return BlockInterface[]
     */
    public function getChain(): array;

    /**
     * Allows for iterating through the chain.
     */
    public function getIterator();

    /**
     * Get the last block in the chain.
     */
    public function getLastBlock(): ?BlockInterface;

    /**
     * Gets the previous block in the chain (last or based on index).
     *
     * @param \drupol\phpblockchain\Block\BlockInterface $block
     *
     * @return BlockInterface|null
     */
    public function getPreviousBlock(BlockInterface $block): ?BlockInterface;

    /**
     * @return string
     */
    public function hash(): string;

    /**
     * Walks the chain and determine if it is valid.
     *
     * @return bool
     */
    public function isValid(): bool;

    /**
     * Determines if the new block is valid to add to the chain.
     *
     * @param \drupol\phpblockchain\Block\BlockInterface $block
     *   The block
     *
     * @return bool
     */
    public function isValidBlock(BlockInterface $block): bool;
}
