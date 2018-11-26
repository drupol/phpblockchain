<?php

declare(strict_types=1);

namespace drupol\phpblockchain\Miner;

use drupol\phpblockchain\Block\BlockInterface;
use drupol\phpblockchain\Blockchain\BlockchainInterface;
use drupol\phpblockchain\Hasher\HasherInterface;
use drupol\phpblockchain\Miner\HashValidationStrategy\HashValidationStrategyInterface;

/**
 * Class Miner.
 */
final class Miner implements MinerInterface
{
    /**
     * @var HasherInterface
     */
    private $hasher;

    /**
     * @var HashValidationStrategyInterface
     */
    private $hashValidationStrategy;

    /**
     * Miner constructor.
     *
     * @param \drupol\phpblockchain\Hasher\HasherInterface $hasher
     * @param \drupol\phpblockchain\Miner\HashValidationStrategy\HashValidationStrategyInterface $hashValidation
     */
    public function __construct(HasherInterface $hasher, HashValidationStrategyInterface $hashValidation)
    {
        $this->hasher = $hasher;
        $this->hashValidationStrategy = $hashValidation;
    }

    /**
     * {@inheritdoc}
     */
    public function getHasher(): HasherInterface
    {
        return $this->hasher;
    }

    /**
     * {@inheritdoc}
     */
    public function getHashValidationStrategy(): HashValidationStrategyInterface
    {
        return $this->hashValidationStrategy;
    }

    /**
     * {@inheritdoc}
     */
    public function isMined(BlockInterface $block): bool
    {
        // because nonce === hash when it's null.
        return false === ($block->getNonce() === $block->getHash());
    }

    /**
     * {@inheritdoc}
     */
    public function mine(BlockInterface $block, ?BlockchainInterface $blockchain = null): BlockInterface
    {
        $nonce = 0;

        while (false === $this->validateNonce($block, $nonce, $blockchain)) {
            ++$nonce;
        }

        $block = $block->withNonce($nonce);

        return $block->withHash($this->getHasher()->hashBlock($block, false));
    }

    /**
     * {@inheritdoc}
     */
    public function validateNonce(
        BlockInterface $block,
        int $nonce,
        ?BlockchainInterface $blockchain = null
    ): bool {
        $previousHash = $block->getPreviousHash();

        if (null !== $blockchain && null !== $previousHash) {
            $previousBlock = $blockchain->getBlock($previousHash);

            if (null !== $previousBlock) {
                $previousBlockHash = $previousBlock->getHash();

                if (null !== $previousBlockHash) {
                    $block = $block->withPreviousHash($previousBlockHash);
                }
            }
        }

        // We hash the block's hash twice to avoid finding the difficulty
        // in the block's hash.
        return $this
            ->getHashValidationStrategy()
            ->hashMatch(
                $this
                    ->getHasher()
                    ->hash(
                        $this
                            ->getHasher()
                            ->hashBlock(
                                $block->withNonce($nonce),
                                false
                            ),
                        false
                    ),
                $block->getDifficulty()
            );
    }
}
