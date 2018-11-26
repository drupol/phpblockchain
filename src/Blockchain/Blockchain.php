<?php

declare(strict_types=1);

namespace drupol\phpblockchain\Blockchain;

use ArrayAccess;
use Countable;
use drupol\phpblockchain\Block\BlockInterface;
use drupol\phpblockchain\Miner\MinerInterface;
use drupol\phpmerkle\Merkle;
use Exception;
use InvalidArgumentException;
use IteratorAggregate;

use function count;

/**
 * Class Blockchain.
 */
final class Blockchain implements ArrayAccess, BlockchainInterface, Countable, IteratorAggregate
{
    /**
     * @var \drupol\phpblockchain\Block\BlockInterface[]
     */
    private $chain;

    /**
     * @var \drupol\phpblockchain\Miner\MinerInterface
     */
    private $miner;

    /**
     * Blockchain constructor.
     *
     * @param \drupol\phpblockchain\Block\BlockInterface[] $chain
     *   The initial chain.
     * @param \drupol\phpblockchain\Miner\MinerInterface $miner
     */
    public function __construct(array $chain, MinerInterface $miner)
    {
        $this->miner = $miner;
        $this->chain = $chain;
    }

    /**
     * {@inheritdoc}
     */
    public function addBlock(BlockInterface $block): ?BlockchainInterface
    {
        if (false === $this->miner->isMined($block)) {
            throw new Exception('Block not valid, the block must be mined.');
        }

        if (!$this->isValidBlock($block)) {
            throw new Exception('Block not valid, cannot add block to chain');
        }

        if (!$this->isValid()) {
            throw new Exception('Blockchain is invalid, cannot add block to chain');
        }

        $this->chain[] = $block;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->chain);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlock(string $hash): ?BlockInterface
    {
        foreach ($this->getChain() as $candidate_block) {
            if ($candidate_block->getHash() === $hash) {
                return $candidate_block;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockIndex(BlockInterface $block): ?int
    {
        return (false === $key = array_search($block, $this->getChain(), true)) ?
            null :
            $key;
    }

    /**
     * {@inheritdoc}
     */
    public function getChain(): array
    {
        return $this->chain;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        yield from $this->getChain();
    }

    /**
     * {@inheritdoc}
     */
    public function getLastBlock(): ?BlockInterface
    {
        $chain = $this->getChain();

        return (false === $block = end($chain)) ?
            null :
            $block;
    }

    /**
     * {@inheritdoc}
     */
    public function getPreviousBlock(BlockInterface $block): ?BlockInterface
    {
        $key = $this->getBlockIndex($block);

        return 1 <= $key ?
            $this->getChain()[$key - 1] :
            null;
    }

    /**
     * {@inheritdoc}
     */
    public function hash(): string
    {
        $chain = new Merkle(2, $this->miner->getHasher());

        foreach ($this->chain as $block) {
            $chain[] = $block->serialize();
        }

        return $chain->hash() ?? '';
    }

    /**
     * {@inheritdoc}
     */
    public function isValid(): bool
    {
        foreach ($this->getChain() as $block) {
            if (!$this->isValidBlock($block)) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isValidBlock(BlockInterface $block): bool
    {
        // Grab previous block automatically if available
        $previousBlock = $this->getPreviousBlock($block);

        $previousBlockChecks = true;

        if (null !== $previousBlock) {
            $previousBlockIndex = $this->getBlockIndex($previousBlock);
            $blockIndex = $this->getBlockIndex($block);

            $previousBlockChecks = ($previousBlockIndex + 1) === $blockIndex &&
                $previousBlock->getHash() === $block->getPreviousHash() &&
                $this->miner->validateNonce($block, $block->getNonce() ?? 0, $this);
        }

        $blockChecks = $this->miner->isMined($block) &&
            $this->miner->getHasher()->hashBlock($block, false) === $block->getHash();

        return $blockChecks && $previousBlockChecks;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->chain[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->chain[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        if (false === ($value instanceof BlockInterface)) {
            throw new InvalidArgumentException(
                sprintf('You can only add object implementing %s.', BlockInterface::class)
            );
        }

        $this->chain[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->chain[$offset]);
    }
}
