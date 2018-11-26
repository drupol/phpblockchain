<?php

declare(strict_types=1);

namespace drupol\phpblockchain\Miner;

use drupol\phpblockchain\Block\BlockInterface;
use drupol\phpblockchain\Blockchain\BlockchainInterface;
use drupol\phpblockchain\Hasher\HasherInterface;
use drupol\phpblockchain\Miner\HashValidationStrategy\HashValidationStrategyInterface;

/**
 * Interface MinerInterface.
 */
interface MinerInterface
{
    /**
     * @return \drupol\phpblockchain\Hasher\HasherInterface
     */
    public function getHasher(): HasherInterface;

    /**
     * @return \drupol\phpblockchain\Miner\HashValidationStrategy\HashValidationStrategyInterface
     */
    public function getHashValidationStrategy(): HashValidationStrategyInterface;

    /**
     * @param \drupol\phpblockchain\Block\BlockInterface $block
     *
     * @return bool
     */
    public function isMined(BlockInterface $block): bool;

    /**
     * @param \drupol\phpblockchain\Block\BlockInterface $block
     * @param \drupol\phpblockchain\Blockchain\BlockchainInterface $blockchain
     *
     * @return \drupol\phpblockchain\Block\BlockInterface
     */
    public function mine(BlockInterface $block, BlockchainInterface $blockchain): BlockInterface;

    /**
     * @param \drupol\phpblockchain\Block\BlockInterface $block
     * @param int $nonce
     * @param \drupol\phpblockchain\Blockchain\BlockchainInterface|null $blockchain
     *
     * @return bool
     */
    public function validateNonce(
        BlockInterface $block,
        int $nonce,
        ?BlockchainInterface $blockchain = null
    ): bool;
}
